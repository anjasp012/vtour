<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function infospot()
    {
        return $this->belongsTo(Infospot::class);
    }

    public function assets()
    {
        return $this->hasMany(ProductAsset::class)->orderBy('sort_order');
    }
}
