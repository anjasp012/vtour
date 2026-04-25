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

    public function products()
    {
        return $this->hasMany(Product::class)->orderBy('sort_order');
    }
}
