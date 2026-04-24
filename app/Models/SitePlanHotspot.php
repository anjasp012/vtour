<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SitePlanHotspot extends Model
{
    protected $guarded = [];

    public function sitePlan()
    {
        return $this->belongsTo(SitePlan::class);
    }

    public function scene()
    {
        return $this->belongsTo(Scene::class);
    }
}
