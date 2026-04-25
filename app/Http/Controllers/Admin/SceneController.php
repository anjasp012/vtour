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
        $tour->load('scenes');
        return view('admin.scenes.index', compact('tour'));
    }

    public function create()
    {
        return view('admin.scenes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg', // 10MB max
            'is_start_scene' => 'boolean',
            'description_id' => 'nullable|string',
            'description_en' => 'nullable|string',
        ]);

        $tour = $this->getTour();
        $imagePath = $request->file('image')->store('scenes/images', 'public');
        $thumbnailPath = $this->generateThumbnail($imagePath);

        if ($request->has('is_start_scene') && $request->is_start_scene) {
            $tour->scenes()->update(['is_start_scene' => false]);
        }

        $newScene = $tour->scenes()->create([
            'name' => $validated['name'],
            'image_path' => $imagePath,
            'thumbnail_path' => $thumbnailPath,
            'is_start_scene' => $request->has('is_start_scene') ? true : false,
            'description_id' => $request->description_id,
            'description_en' => $request->description_en,
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
            'is_start_scene' => 'boolean',
            'description_id' => 'nullable|string',
            'description_en' => 'nullable|string',
        ]);

        if ($request->has('is_start_scene') && $request->is_start_scene) {
            $scene->tour->scenes()->update(['is_start_scene' => false]);
        }

        $data = [
            'name' => $validated['name'],
            'is_start_scene' => $request->has('is_start_scene') ? true : false,
            'description_id' => $request->description_id,
            'description_en' => $request->description_en,
        ];

        if ($request->hasFile('image')) {
            if (Storage::disk('public')->exists($scene->image_path)) {
                Storage::disk('public')->delete($scene->image_path);
            }
            if ($scene->thumbnail_path && Storage::disk('public')->exists($scene->thumbnail_path)) {
                Storage::disk('public')->delete($scene->thumbnail_path);
            }
            
            $imagePath = $request->file('image')->store('scenes/images', 'public');
            $data['image_path'] = $imagePath;
            $data['thumbnail_path'] = $this->generateThumbnail($imagePath);
        }

        $scene->update($data);

        return redirect()->route('admin.scenes.index')->with('success', 'Scene updated successfully.');
    }

    public function destroy(Scene $scene)
    {
        if (Storage::disk('public')->exists($scene->image_path)) {
            Storage::disk('public')->delete($scene->image_path);
        }
        if ($scene->thumbnail_path && Storage::disk('public')->exists($scene->thumbnail_path)) {
            Storage::disk('public')->delete($scene->thumbnail_path);
        }
        
        $scene->delete();

        return redirect()->route('admin.scenes.index')->with('success', 'Scene deleted successfully.');
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

    private function generateThumbnail($imagePath)
    {
        try {
            $fullPath = storage_path('app/public/' . $imagePath);
            if (!file_exists($fullPath)) return null;

            $info = getimagesize($fullPath);
            $mime = $info['mime'];

            switch ($mime) {
                case 'image/jpeg':
                    $src = imagecreatefromjpeg($fullPath);
                    break;
                case 'image/png':
                    $src = imagecreatefrompng($fullPath);
                    break;
                default:
                    return null;
            }

            if (!$src) return null;

            $width = imagesx($src);
            $height = imagesy($src);

            // Thumbnail target width: 1024px (standard for low-res pano)
            $newWidth = 1024;
            $newHeight = ($height / $width) * $newWidth;

            $tmp = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            $thumbName = 'thumb_' . basename($imagePath);
            $thumbDir = dirname($imagePath);
            $thumbPath = $thumbDir . '/' . $thumbName;
            $thumbFullPath = storage_path('app/public/' . $thumbPath);

            // Save as JPEG with quality 60 (very light)
            imagejpeg($tmp, $thumbFullPath, 60);

            imagedestroy($src);
            imagedestroy($tmp);

            return $thumbPath;
        } catch (\Exception $e) {
            \Log::error("Thumbnail generation failed: " . $e->getMessage());
            return null;
        }
    }
}
