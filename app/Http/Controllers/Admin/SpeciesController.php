<?php
// app/Http/Controllers/Admin/SpeciesController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Species;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SpeciesController extends Controller
{
    /**
     * Display a listing of the species.
     */
    public function index(Request $request)
    {
        $query = Species::with('category');

        // Search functionality
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('common_name', 'like', '%' . $request->search . '%')
                  ->orWhere('scientific_name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $species = $query->orderBy('common_name')->paginate(15);
        $categories = Category::all();

        return view('admin.species.index', compact('species', 'categories'));
    }

    /**
     * Show the form for creating a new species.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.species.create', compact('categories'));
    }

    /**
     * Store a newly created species in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'common_name' => 'required|string|max:255',
            'scientific_name' => 'nullable|string|max:255',
            'description' => 'required|string',
            'characteristics' => 'nullable|array',
            'characteristics.*' => 'string',
            'habitat' => 'nullable|string|max:500',
            'conservation_status' => 'nullable|string|max:100',
            'fun_facts' => 'nullable|array',
            'fun_facts.*' => 'string',
            'medicinal_uses' => 'nullable|array',
            'medicinal_uses.*' => 'string',
            'cultural_significance' => 'nullable|array',
            'cultural_significance.*' => 'string',
            'image_url' => 'nullable|url|max:500',
            'is_active' => 'boolean'
        ]);

        // Convert arrays to JSON
        $validated['characteristics'] = $validated['characteristics'] ?? null;
        $validated['fun_facts'] = $validated['fun_facts'] ?? null;
        $validated['medicinal_uses'] = $validated['medicinal_uses'] ?? null;
        $validated['cultural_significance'] = $validated['cultural_significance'] ?? null;
        $validated['is_active'] = $request->has('is_active');

        Species::create($validated);

        return redirect()->route('admin.species.index')
            ->with('success', 'Species created successfully.');
    }

    /**
     * Display the specified species.
     */
    public function show(Species $species)
    {
        $species->load('category', 'identifications.user');
        $totalIdentifications = $species->identifications()->count();
        $averageConfidence = $species->identifications()->avg('confidence');

        return view('admin.species.show', compact('species', 'totalIdentifications', 'averageConfidence'));
    }

    /**
     * Show the form for editing the specified species.
     */
    public function edit(Species $species)
    {
        $categories = Category::all();
        return view('admin.species.edit', compact('species', 'categories'));
    }

    /**
     * Update the specified species in storage.
     */
    public function update(Request $request, Species $species)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'common_name' => 'required|string|max:255',
            'scientific_name' => 'nullable|string|max:255',
            'description' => 'required|string',
            'characteristics' => 'nullable|array',
            'characteristics.*' => 'string',
            'habitat' => 'nullable|string|max:500',
            'conservation_status' => 'nullable|string|max:100',
            'fun_facts' => 'nullable|array',
            'fun_facts.*' => 'string',
            'medicinal_uses' => 'nullable|array',
            'medicinal_uses.*' => 'string',
            'cultural_significance' => 'nullable|array',
            'cultural_significance.*' => 'string',
            'image_url' => 'nullable|url|max:500',
            'is_active' => 'boolean'
        ]);

        // Convert arrays to JSON
        $validated['characteristics'] = $validated['characteristics'] ?? null;
        $validated['fun_facts'] = $validated['fun_facts'] ?? null;
        $validated['medicinal_uses'] = $validated['medicinal_uses'] ?? null;
        $validated['cultural_significance'] = $validated['cultural_significance'] ?? null;
        $validated['is_active'] = $request->has('is_active');

        $species->update($validated);

        return redirect()->route('admin.species.index')
            ->with('success', 'Species updated successfully.');
    }

    /**
     * Remove the specified species from storage.
     */
    public function destroy(Species $species)
    {
        // Check if species has identifications
        if ($species->identifications()->exists()) {
            return back()->with('error', 'Cannot delete species that has identification records.');
        }

        $species->delete();

        return redirect()->route('admin.species.index')
            ->with('success', 'Species deleted successfully.');
    }

    /**
     * Toggle species active status.
     */
    public function toggleStatus(Species $species)
    {
        $species->update([
            'is_active' => !$species->is_active
        ]);

        return back()->with('success', 'Species status updated successfully.');
    }

    /**
     * Export species data.
     */
    public function export()
    {
        $species = Species::with('category')->get();

        $csvData = [];
        $csvData[] = ['ID', 'Common Name', 'Scientific Name', 'Category', 'Description', 'Habitat', 'Conservation Status', 'Status'];

        foreach ($species as $item) {
            $csvData[] = [
                $item->id,
                $item->common_name,
                $item->scientific_name,
                $item->category->name,
                $item->description,
                $item->habitat,
                $item->conservation_status,
                $item->is_active ? 'Active' : 'Inactive'
            ];
        }

        $filename = 'species_export_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);
        exit;
    }
}
