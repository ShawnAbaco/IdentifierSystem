<?php
// app/Http/Controllers/IdentificationController.php

namespace App\Http\Controllers;

use App\Models\Identification;
use App\Models\Species;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager; // You'll need to install this package

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

        // Check if it's a data URL and extract the format
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
            $imageType = $matches[1]; // jpeg, png, gif, etc.
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
        } else {
            $imageType = 'png'; // default
        }

        $imageData = str_replace(' ', '+', $imageData);
        $imageData = base64_decode($imageData);

        if ($imageData === false) {
            return response()->json(['error' => 'Invalid image data'], 400);
        }

        // Create directory if it doesn't exist
        $userDirectory = 'identifications/' . Auth::id();
        $fullPath = storage_path('app/public/' . $userDirectory);

        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        // Generate unique filename with correct extension
        $extension = $imageType === 'jpeg' ? 'jpg' : $imageType;
        $filename = uniqid() . '.' . $extension;
        $imageName = $userDirectory . '/' . $filename;

        // Save the image
        Storage::disk('public')->put($imageName, $imageData);

        // Also save to public/storage for direct access
        $publicPath = public_path('storage/' . $imageName);
        $publicDir = dirname($publicPath);
        if (!file_exists($publicDir)) {
            mkdir($publicDir, 0755, true);
        }
        file_put_contents($publicPath, $imageData);

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
            'identification' => $identification->load('species'),
            'image_url' => $identification->image_url
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
