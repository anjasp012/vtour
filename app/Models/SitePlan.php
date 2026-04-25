<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SitePlan extends Model
{
    protected $guarded = [];



    public function hotspots()
    {
        return $this->hasMany(SitePlanHotspot::class);
    }
}
