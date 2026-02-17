<?php
// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'icon'];

    public function species()
    {
        return $this->hasMany(Species::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getActiveSpeciesCountAttribute()
    {
        return $this->species()->where('is_active', true)->count();
    }
}
