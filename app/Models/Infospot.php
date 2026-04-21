<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Infospot extends Model
{
    protected $guarded = [];

    public function view()
    {
        return $this->belongsTo(SceneView::class, 'scene_view_id');
    }

    public function targetScene()
    {
        return $this->belongsTo(Scene::class, 'target_scene_id');
    }
}
