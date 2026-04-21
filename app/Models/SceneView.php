<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SceneView extends Model
{
    protected $guarded = [];

    public function scene()
    {
        return $this->belongsTo(Scene::class);
    }

    public function infospots()
    {
        return $this->hasMany(Infospot::class, 'scene_view_id');
    }
}
