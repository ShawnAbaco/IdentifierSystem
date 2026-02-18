<?php
// app/Http/Controllers/Auth/RegisterController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');

        }

            public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store user data and OTP in cache (expires in 10 minutes)
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'otp' => $otp,
            'created_at' => now()
        ];

        Cache::put('registration_' . $request->email, $userData, now()->addMinutes(10));

        // Send OTP via email
        try {
            Mail::send('emails.otp', ['otp' => $otp, 'name' => $request->name], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Email Verification OTP - ' . config('app.name'));
            });

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your email.'
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('OTP Email Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please check your email configuration and try again.'
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $cachedData = Cache::get('registration_' . $request->email);

        if (!$cachedData) {
            return redirect()->back()
                ->withErrors(['otp' => 'OTP expired or not found. Please register again.'])
                ->withInput();
        }

        if ($cachedData['otp'] !== $request->otp) {
            return redirect()->back()
                ->withErrors(['otp' => 'Invalid OTP code. Please try again.'])
                ->withInput();
        }

        // Create user
        $user = User::create([
            'name' => $cachedData['name'],
            'email' => $cachedData['email'],
            'password' => $cachedData['password'],
            'role' => 'user', // Default role
            'email_verified_at' => now(), // Mark as verified since OTP is confirmed
        ]);

        // Clear cached data
        Cache::forget('registration_' . $request->email);

        // Log the user in
        auth()->login($user);

        return redirect()->route('dashboard')->with('success', 'Registration successful! Your email has been verified.');
    }

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $cachedData = Cache::get('registration_' . $request->email);

        if (!$cachedData) {
            return response()->json([
                'success' => false,
                'message' => 'Registration session expired. Please register again.'
            ], 404);
        }

        // Generate new OTP
        $newOtp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $cachedData['otp'] = $newOtp;
        $cachedData['created_at'] = now();

        Cache::put('registration_' . $request->email, $cachedData, now()->addMinutes(10));

        // Send new OTP
        try {
            Mail::send('emails.otp', ['otp' => $newOtp, 'name' => $cachedData['name']], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('New Email Verification OTP - ' . config('app.name'));
            });

            return response()->json([
                'success' => true,
                'message' => 'New OTP sent successfully.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Resend OTP Email Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.'
            ], 500);
        }

    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default role
        ]);

        auth()->login($user);

        return redirect()->route('dashboard');
    }
}
