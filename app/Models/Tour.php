<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $guarded = [];

    public function scenes()
    {
        return $this->hasMany(Scene::class);
    }
}
