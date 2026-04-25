<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SitePlan;
use App\Models\SitePlanHotspot;
use App\Models\Scene;
use Illuminate\Support\Facades\Storage;

class SitePlanController extends Controller
{
    public function index()
    {
        $sitePlans = SitePlan::all();
        return view('admin.site_plans.index', compact('sitePlans'));
    }

    public function create()
    {
        return view('admin.site_plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg',
        ]);

        $highResPath = $request->file('image')->store('site_plans', 'public');
        $multiRes = $this->generateMultiResImages($highResPath);

        SitePlan::create([
            'name' => $validated['name'],
            'high_res_path' => $highResPath,
            'low_res_path' => $multiRes['low_res_path'] ?? null,
            'thumbnail_path' => $multiRes['thumbnail_path'] ?? null,
            'medium_res_path' => $multiRes['medium_res_path'] ?? null,
        ]);

        return redirect()->route('admin.site-plans.index')->with('success', 'Site plan created successfully.');
    }

    public function show(SitePlan $sitePlan)
    {
        $sitePlan->load('hotspots.scene');
        $scenes = Scene::all();
        return view('admin.site_plans.show', compact('sitePlan', 'scenes'));
    }

    public function edit(SitePlan $sitePlan)
    {
        return view('admin.site_plans.edit', compact('sitePlan'));
    }

    public function update(Request $request, SitePlan $sitePlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        $data = ['name' => $validated['name']];

        if ($request->hasFile('image')) {
            $this->deletePhysicalImages($sitePlan);
            
            $highResPath = $request->file('image')->store('site_plans', 'public');
            $multiRes = $this->generateMultiResImages($highResPath);
            
            $data['high_res_path'] = $highResPath;
            $data['low_res_path'] = $multiRes['low_res_path'] ?? null;
            $data['thumbnail_path'] = $multiRes['thumbnail_path'] ?? null;
            $data['medium_res_path'] = $multiRes['medium_res_path'] ?? null;
        }

        $sitePlan->update($data);

        return redirect()->route('admin.site-plans.index')->with('success', 'Site plan updated successfully.');
    }

    public function destroy(SitePlan $sitePlan)
    {
        $this->deletePhysicalImages($sitePlan);
        $sitePlan->delete();

        return redirect()->route('admin.site-plans.index')->with('success', 'Site plan deleted successfully.');
    }

    private function deletePhysicalImages(SitePlan $sitePlan)
    {
        $paths = ['high_res_path', 'low_res_path', 'thumbnail_path', 'medium_res_path'];
        foreach ($paths as $p) {
            if ($sitePlan->$p && Storage::disk('public')->exists($sitePlan->$p)) {
                Storage::disk('public')->delete($sitePlan->$p);
            }
        }
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

            // 1. Thumbnail - 5% of original
            $results['thumbnail_path'] = $this->resizeByPercent($src, $width, $height, 0.05, 'thumb_', $thumbDir, $baseName, 80);

            // 2. Low Res - 15% of original
            $results['low_res_path'] = $this->resizeByPercent($src, $width, $height, 0.15, 'low_', $thumbDir, $baseName, 70);

            // 3. Medium Res - 40% of original
            $results['medium_res_path'] = $this->resizeByPercent($src, $width, $height, 0.40, 'mid_', $thumbDir, $baseName, 75);

            imagedestroy($src);
            return $results;
        } catch (\Exception $e) {
            \Log::error("Multi-res generation failed for site plan: " . $e->getMessage());
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

    public function saveHotspots(Request $request, SitePlan $sitePlan)
    {
        $request->validate([
            'hotspots' => 'required|array',
            'hotspots.*.scene_id' => 'required|exists:scenes,id',
            'hotspots.*.x' => 'required|numeric',
            'hotspots.*.y' => 'required|numeric',
        ]);

        $sitePlan->hotspots()->delete();

        foreach ($request->hotspots as $hotspot) {
            $sitePlan->hotspots()->create([
                'scene_id' => $hotspot['scene_id'],
                'x' => $hotspot['x'],
                'y' => $hotspot['y'],
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Hotspots saved successfully.']);
    }
}
