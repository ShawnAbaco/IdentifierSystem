<?php
// app/Http/Controllers/Admin/AdminController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Identification;
use App\Models\Species;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalIdentifications = Identification::count();
        $totalSpecies = Species::count();
        $totalCategories = Category::count();

        $recentUsers = User::latest()->take(5)->get();
        $recentIdentifications = Identification::with('user', 'species')
            ->latest()
            ->take(10)
            ->get();

        $topSpecies = Species::withCount('identifications')
            ->orderBy('identifications_count', 'desc')
            ->take(5)
            ->get();

        $userGrowth = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalIdentifications',
            'totalSpecies',
            'totalCategories',
            'recentUsers',
            'recentIdentifications',
            'topSpecies',
            'userGrowth'
        ));
    }

    public function users()
    {
        $users = User::withCount('identifications')->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function toggleUserStatus(User $user)
    {
        // You can implement soft deletes or status toggle
        return back()->with('success', 'User status updated');
    }

    public function statistics()
    {
        $stats = [
            'daily_identifications' => Identification::whereDate('created_at', today())->count(),
            'weekly_identifications' => Identification::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'monthly_identifications' => Identification::whereMonth('created_at', now()->month)->count(),
            'most_identified_species' => Species::withCount('identifications')
                ->orderBy('identifications_count', 'desc')
                ->first(),
            'most_active_user' => User::withCount('identifications')
                ->orderBy('identifications_count', 'desc')
                ->first(),
        ];

        return view('admin.statistics', compact('stats'));
    }
}
