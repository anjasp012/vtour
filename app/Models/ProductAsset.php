<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAsset extends Model
{
    protected $guarded = [];

    /**
     * Get the storage URL for this asset.
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Determine if this asset is a 3D model.
     */
    public function is3d(): bool
    {
        return $this->file_type === '3d';
    }

    /**
     * Determine if this asset is a 2D image.
     */
    public function is2d(): bool
    {
        return $this->file_type === '2d';
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
