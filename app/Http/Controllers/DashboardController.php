<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Identification;
use App\Models\Species;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $totalIdentifications = Identification::where('user_id', $userId)->count();

        $recentIdentifications = Identification::with('species')
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        $topIdentified = Identification::where('user_id', $userId)
            ->select('identified_as', DB::raw('count(*) as total'))
            ->groupBy('identified_as')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        $todayCount = Identification::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->count();

        $accuracy = Identification::where('user_id', $userId)
            ->whereNotNull('is_correct')
            ->select(DB::raw('AVG(CASE WHEN is_correct = 1 THEN 100 ELSE 0 END) as accuracy'))
            ->first();

        $recentActivity = Identification::with('species')
            ->where('user_id', $userId)
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'totalIdentifications',
            'recentIdentifications',
            'topIdentified',
            'todayCount',
            'accuracy',
            'recentActivity'
        ));
    }
}
