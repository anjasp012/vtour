<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scene extends Model
{
    protected $guarded = [];



    public function infospots()
    {
        return $this->hasMany(Infospot::class);
    }

    public function sitePlanHotspots()
    {
        return $this->hasMany(SitePlanHotspot::class);
    }
}
