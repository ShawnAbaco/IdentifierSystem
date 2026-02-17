<?php
// app/Http/Controllers/SpeciesViewController.php

namespace App\Http\Controllers;

use App\Models\Species;
use Illuminate\Http\Request;

class SpeciesViewController extends Controller
{
    public function show(Species $species)
    {
        // Load the category relationship
        $species->load('category');

        // Get related/similar species from the same category
        $relatedSpecies = Species::where('category_id', $species->category_id)
            ->where('id', '!=', $species->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return view('species.show', compact('species', 'relatedSpecies'));
    }
}
