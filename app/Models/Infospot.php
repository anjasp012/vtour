<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Infospot extends Model
{
    protected $guarded = [];

    public function scene()
    {
        return $this->belongsTo(Scene::class);
    }

    public function targetScene()
    {
        return $this->belongsTo(Scene::class, 'target_scene_id');
    }

    public function assets()
    {
        return $this->hasMany(InfospotAsset::class)->orderBy('sort_order');
    }
}
