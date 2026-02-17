<?php
// app/Http/Controllers/ProfileController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $recentIdentifications = $user->identifications()->with('species')->latest()->take(5)->get();
        $totalIdentifications = $user->identifications()->count();

        return view('profile.show', compact('user', 'recentIdentifications', 'totalIdentifications'));
    }

    public function edit()
    {
        $user = Auth::user();
        // Add these variables for the edit view
        $recentIdentifications = $user->identifications()->with('species')->latest()->take(3)->get();
        $totalIdentifications = $user->identifications()->count();

        return view('profile.edit', compact('user', 'recentIdentifications', 'totalIdentifications'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->bio = $request->bio;

        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password updated successfully!');
    }
}
