<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfospotProduct extends Model
{
    protected $guarded = [];

    public function infospot()
    {
        return $this->belongsTo(Infospot::class);
    }

    public function assets()
    {
        return $this->hasMany(InfospotAsset::class)->orderBy('sort_order');
    }
}
