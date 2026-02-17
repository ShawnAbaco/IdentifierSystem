<?php
// app/Http/Controllers/IdentificationController.php

namespace App\Http\Controllers;

use App\Models\Identification;
use App\Models\Species;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IdentificationController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category', 'all');

        if ($category !== 'all') {
            $species = Species::whereHas('category', function($query) use ($category) {
                $query->where('slug', $category);
            })->active()->get();
        } else {
            $species = Species::active()->get();
        }

        $categories = \App\Models\Category::all();

        return view('identify', compact('species', 'category', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|string',
            'identified_as' => 'required|string',
            'confidence' => 'required|numeric',
            'all_predictions' => 'required|json',
            'user_notes' => 'nullable|string'
        ]);

        // Find matching species
        $species = Species::where('common_name', $request->identified_as)->first();

        // Decode and save image
        $imageData = $request->image;
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $imageName = 'identifications/' . Auth::id() . '/' . uniqid() . '.png';

        Storage::disk('public')->put($imageName, base64_decode($imageData));

        $identification = Identification::create([
            'user_id' => Auth::id(),
            'species_id' => $species ? $species->id : null,
            'identified_as' => $request->identified_as,
            'confidence' => $request->confidence,
            'all_predictions' => json_decode($request->all_predictions, true),
            'image_path' => $imageName,
            'user_notes' => $request->user_notes,
            'location' => $request->location
        ]);

        return response()->json([
            'success' => true,
            'identification' => $identification->load('species')
        ]);
    }

    public function show(Identification $identification)
    {
        if ($identification->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $similarSpecies = Species::where('category_id', $identification->species?->category_id)
            ->where('id', '!=', $identification->species_id)
            ->where('is_active', true)
            ->take(3)
            ->get();

        return view('identification.show', compact('identification', 'similarSpecies'));
    }

    public function feedback(Request $request, Identification $identification)
    {
        if ($identification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'is_correct' => 'required|boolean'
        ]);

        $identification->update([
            'is_correct' => $request->is_correct
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Identification $identification)
    {
        if ($identification->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Delete image file
        if ($identification->image_path) {
            Storage::disk('public')->delete($identification->image_path);
        }

        $identification->delete();

        return redirect()->route('history')->with('success', 'Identification deleted successfully');
    }
}
