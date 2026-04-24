<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SitePlan extends Model
{
    protected $guarded = [];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function hotspots()
    {
        return $this->hasMany(SitePlanHotspot::class);
    }
}
