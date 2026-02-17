<?php
// app/Http/Controllers/HistoryController.php

namespace App\Http\Controllers;

use App\Models\Identification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Identification::with('species')
            ->where('user_id', Auth::id());

        // Search filter
        if ($request->filled('search')) {
            $query->where('identified_as', 'like', '%' . $request->search . '%');
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Confidence filter
        if ($request->filled('min_confidence')) {
            $query->where('confidence', '>=', $request->min_confidence / 100);
        }

        // Correct/Incorrect filter
        if ($request->filled('correct')) {
            $query->where('is_correct', $request->correct === 'true');
        }

        $identifications = $query->latest()->paginate(12);

        $statistics = [
            'total' => Identification::where('user_id', Auth::id())->count(),
            'avg_confidence' => Identification::where('user_id', Auth::id())->avg('confidence'),
            'correct_count' => Identification::where('user_id', Auth::id())->where('is_correct', true)->count(),
        ];

        return view('history', compact('identifications', 'statistics'));
    }
}
