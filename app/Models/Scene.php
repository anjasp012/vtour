<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scene extends Model
{
    protected $guarded = [];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function views()
    {
        return $this->hasMany(SceneView::class);
    }

    public function primaryView()
    {
        return $this->hasOne(SceneView::class)->where('is_primary', true);
    }

    public function infospots()
    {
        return $this->hasManyThrough(Infospot::class, SceneView::class, 'scene_id', 'scene_view_id');
    }
}
