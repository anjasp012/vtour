<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tour;
use App\Models\Scene;
use Illuminate\Support\Facades\Storage;

class SceneController extends Controller
{
    private function getTour()
    {
        return Tour::firstOrCreate(
            ['id' => 1],
            ['name' => 'Main Virtual Tour', 'description' => 'Automatically generated primary tour.']
        );
    }

    public function index()
    {
        $tour = $this->getTour();
        $tour->load(['scenes' => function($q) {
            $q->orderBy('order', 'asc')->orderBy('id', 'desc');
        }]);
        return view('admin.scenes.index', compact('tour'));
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:scenes,id'
        ]);

        foreach ($request->order as $index => $id) {
            Scene::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['success' => true, 'message' => 'Order updated successfully.']);
    }

    public function create()
    {
        return view('admin.scenes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp', // 10MB max
            'description_id' => 'nullable|string',
            'description_en' => 'nullable|string',
        ]);

        $tour = $this->getTour();
        $highResPath = $request->file('image')->store('scenes/images', 'public');
        $multiRes = $this->generateMultiResImages($highResPath);

        $newScene = $tour->scenes()->create([
            'name' => $validated['name'],
            'high_res_path' => $highResPath,
            'low_res_path' => $multiRes['low_res_path'] ?? null,
            'thumbnail_path' => $multiRes['thumbnail_path'] ?? null,
            'medium_res_path' => $multiRes['medium_res_path'] ?? null,
            'description_id' => $request->description_id,
            'description_en' => $request->description_en,
            'order' => $tour->scenes()->max('order') + 1,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Scene created successfully.',
                'scene' => $newScene
            ]);
        }

        return redirect()->route('admin.scenes.index')->with('success', 'Scene added successfully.');
    }

    public function show(Scene $scene)
    {
        $scene->load('infospots.targetScene');
        $hasTargetScenes = Scene::where('id', '!=', $scene->id)->get();
        return view('admin.scenes.show', compact('scene', 'hasTargetScenes'));
    }

    public function edit(Scene $scene)
    {
        return view('admin.scenes.edit', compact('scene'));
    }

    public function update(Request $request, Scene $scene)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp',
            'description_id' => 'nullable|string',
            'description_en' => 'nullable|string',
        ]);

        $data = [
            'name' => $validated['name'],
            'description_id' => $request->description_id,
            'description_en' => $request->description_en,
        ];

        if ($request->hasFile('image')) {
            $this->deletePhysicalImages($scene);
            
            $highResPath = $request->file('image')->store('scenes/images', 'public');
            $multiRes = $this->generateMultiResImages($highResPath);
            
            $data['high_res_path'] = $highResPath;
            $data['low_res_path'] = $multiRes['low_res_path'] ?? null;
            $data['thumbnail_path'] = $multiRes['thumbnail_path'] ?? null;
            $data['medium_res_path'] = $multiRes['medium_res_path'] ?? null;
        }

        $scene->update($data);

        return redirect()->route('admin.scenes.index')->with('success', 'Scene updated successfully.');
    }

    public function destroy(Scene $scene)
    {
        $this->deletePhysicalImages($scene);
        $scene->delete();

        return redirect()->route('admin.scenes.index')->with('success', 'Scene deleted successfully.');
    }

    private function deletePhysicalImages(Scene $scene)
    {
        $paths = ['high_res_path', 'low_res_path', 'thumbnail_path', 'medium_res_path'];
        foreach ($paths as $p) {
            if ($scene->$p && Storage::disk('public')->exists($scene->$p)) {
                Storage::disk('public')->delete($scene->$p);
            }
        }
    }

    /**
     * Save the initial camera direction (lon/lat) for the scene.
     */
    public function lockView(Request $request, Scene $scene)
    {
        $request->validate([
            'lon' => 'required|numeric',
            'lat' => 'required|numeric',
        ]);

        $scene->update([
            'initial_lon' => $request->lon,
            'initial_lat' => $request->lat,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Initial view locked.',
            'lon'     => $scene->initial_lon,
            'lat'     => $scene->initial_lat,
        ]);
    }

    private function generateMultiResImages($imagePath)
    {
        try {
            $fullPath = storage_path('app/public/' . $imagePath);
            if (!file_exists($fullPath)) return [];

            $info = getimagesize($fullPath);
            $mime = $info['mime'];

            switch ($mime) {
                case 'image/jpeg': $src = imagecreatefromjpeg($fullPath); break;
                case 'image/png': $src = imagecreatefrompng($fullPath); break;
                default: return [];
            }

            if (!$src) return [];

            $width = imagesx($src);
            $height = imagesy($src);
            $thumbDir = dirname($imagePath);
            $baseName = basename($imagePath);

            $results = [];

            // 1. Thumbnail (sidebar) - 5% of original
            $results['thumbnail_path'] = $this->resizeByPercent($src, $width, $height, 0.05, 'thumb_', $thumbDir, $baseName, 80);

            // 2. Low Res (pano start) - 15% of original
            $results['low_res_path'] = $this->resizeByPercent($src, $width, $height, 0.15, 'low_', $thumbDir, $baseName, 70);

            // 3. Medium Res (pano mid) - 40% of original
            $results['medium_res_path'] = $this->resizeByPercent($src, $width, $height, 0.40, 'mid_', $thumbDir, $baseName, 75);

            imagedestroy($src);
            return $results;
        } catch (\Exception $e) {
            \Log::error("Multi-res generation failed: " . $e->getMessage());
            return [];
        }
    }

    private function resizeByPercent($src, $origW, $origH, $percent, $prefix, $dir, $baseName, $quality)
    {
        $targetW = max(50, round($origW * $percent));
        $targetH = max(25, round($origH * $percent));
        
        $tmp = imagecreatetruecolor($targetW, $targetH);
        imagecopyresampled($tmp, $src, 0, 0, 0, 0, $targetW, $targetH, $origW, $origH);
        
        $path = $dir . '/' . $prefix . $baseName;
        $fullPath = storage_path('app/public/' . $path);
        
        imagejpeg($tmp, $fullPath, $quality);
        imagedestroy($tmp);
        
        return $path;
    }
}
