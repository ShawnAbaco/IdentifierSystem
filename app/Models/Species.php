<?php
// app/Models/Species.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Species extends Model
{
    protected $fillable = [
        'category_id',
        'common_name',
        'scientific_name',
        'description',
        'characteristics',
        'image_url',
        'habitat',
        'conservation_status',
        'fun_facts',
        'medicinal_uses',
        'cultural_significance',
        'is_active'
    ];

    protected $casts = [
        'characteristics' => 'array',
        'fun_facts' => 'array',
        'medicinal_uses' => 'array',
        'cultural_significance' => 'array',
        'is_active' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function identifications()
    {
        return $this->hasMany(Identification::class);
    }

    public function getConservationStatusColorAttribute()
    {
        return match(strtolower($this->conservation_status ?? '')) {
            'endangered' => 'danger',
            'vulnerable' => 'warning',
            'rare' => 'info',
            'common' => 'success',
            default => 'secondary'
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('common_name', 'like', "%{$search}%")
            ->orWhere('scientific_name', 'like', "%{$search}%");
    }
}
