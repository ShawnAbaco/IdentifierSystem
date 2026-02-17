<?php
// app/Models/Identification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Identification extends Model
{
    protected $fillable = [
        'user_id',
        'species_id',
        'identified_as',
        'confidence',
        'all_predictions',
        'image_path',
        'user_notes',
        'location',
        'is_correct'
    ];

    protected $casts = [
        'all_predictions' => 'array',
        'confidence' => 'decimal:4',
        'is_correct' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function species()
    {
        return $this->belongsTo(Species::class);
    }

    public function getConfidencePercentageAttribute()
    {
        return number_format($this->confidence * 100, 1) . '%';
    }

    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
}
