<?php
// app/Http/Controllers/Admin/AdminController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Identification;
use App\Models\Species;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{

  public function __construct()
    {
        // This ensures admin check even if route middleware fails
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            if (!Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access. Admin only.');
            }

            return $next($request);
        });
    }
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

    // Add these methods to your AdminController

public function showUser(User $user)
{
    $user->load('identifications.species');
    $recentIdentifications = $user->identifications()->with('species')->latest()->take(10)->get();

    return view('admin.users.show', compact('user', 'recentIdentifications'));
}


public function createUser()
{
    return view('admin.users.create');
}

/**
 * Verify user email
 */
public function verifyEmail(Request $request, User $user)
{
    // Mark email as verified
    $user->email_verified_at = now();
    $user->save();

    return back()->with('success', 'Email verified successfully for ' . $user->name);
}

/**
 * Reset user password
 */
public function resetPassword(Request $request, User $user)
{
    $request->validate([
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user->password = Hash::make($request->password);
    $user->save();

    return back()->with('success', 'Password reset successfully for ' . $user->name);
}




public function editUser(User $user)
{
    return view('admin.users.edit', compact('user'));
}

public function updateUser(Request $request, User $user)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'role' => 'required|in:user,admin',
        'bio' => 'nullable|string|max:500',
        'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    $user->name = $request->name;
    $user->email = $request->email;
    $user->role = $request->role;
    $user->bio = $request->bio;

    if ($request->hasFile('avatar')) {
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
    }

    $user->save();

    return redirect()->route('admin.users')->with('success', 'User updated successfully.');
}

public function destroyUser(User $user)
{
    // Delete user's avatar
    if ($user->avatar) {
        Storage::disk('public')->delete($user->avatar);
    }

    // Delete user's identifications (cascade will handle this if set in migration)
    $user->delete();

    return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
}

public function storeUser(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'role' => 'required|in:user,admin',
        'bio' => 'nullable|string|max:500',
        'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    $user = new User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->password = Hash::make($request->password);
    $user->role = $request->role;
    $user->bio = $request->bio;

    if ($request->hasFile('avatar')) {
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
    }

    $user->save();

    return redirect()->route('admin.users')->with('success', 'User created successfully.');
}
}
