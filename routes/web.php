<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IdentificationController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SpeciesViewController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SpeciesController;

/*
|--------------------------------------------------------------------------
| Test Routes (Remove in Production)
|--------------------------------------------------------------------------
*/
Route::get('/test-email', function() {
    $user = App\Models\User::first();
    if (!$user) {
        return "No users found in database.";
    }

    $verification = App\Models\EmailVerification::generateForUser($user->id);
    $body = App\Helpers\MailHelper::getOtpEmailBody($user->name, $verification->otp);

    $result = App\Helpers\MailHelper::sendEmail(
        $user->email,
        $user->name,
        'Test Email',
        $body
    );

    if ($result['success']) {
        return "Email sent successfully to {$user->email}! Check your inbox.";
    } else {
        return "Failed: " . $result['message'];
    }
})->middleware('auth');

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Password Reset Routes
|--------------------------------------------------------------------------
*/
Route::prefix('password')->name('password.')->group(function () {
    Route::get('/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('request');
    Route::post('/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('email');
    Route::get('/verify', [ForgotPasswordController::class, 'showOtpForm'])->name('verify.form');
    Route::post('/verify', [ForgotPasswordController::class, 'verifyOtp'])->name('verify.otp');
    Route::get('/reset', [ForgotPasswordController::class, 'showResetForm'])->name('reset.form');
    Route::post('/reset', [ForgotPasswordController::class, 'reset'])->name('update');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/register/send-otp', [RegisterController::class, 'sendOtp'])->name('register.send-otp');
Route::post('/register/verify-otp', [RegisterController::class, 'verifyOtp'])->name('register.verify-otp');
Route::post('/register/resend-otp', [RegisterController::class, 'resendOtp'])->name('register.resend-otp');

/*
|--------------------------------------------------------------------------
| Public Species View
|--------------------------------------------------------------------------
*/
Route::get('/species/{species}', [SpeciesViewController::class, 'show'])->name('species.show');

/*
|--------------------------------------------------------------------------
| Protected Routes (Require Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');

    // Identification
    Route::get('/identify', [IdentificationController::class, 'index'])->name('identify');
    Route::post('/identify/store', [IdentificationController::class, 'store'])->name('identify.store');
    Route::get('/identification/{identification}', [IdentificationController::class, 'show'])->name('identification.show');
    Route::post('/identification/{identification}/feedback', [IdentificationController::class, 'feedback'])->name('identification.feedback');
    Route::delete('/identification/{identification}', [IdentificationController::class, 'destroy'])->name('identification.destroy');

    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('history');

    // API route for getting species by category
    Route::get('/categories/{category}/species', [CategoryController::class, 'getSpecies'])->name('categories.species');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes (Require Admin Role)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // User Management - Place export route BEFORE parameter routes
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');

        // IMPORTANT: Export route must come BEFORE /users/{user} to avoid conflict
        Route::get('/users/export', [AdminController::class, 'exportUsers'])->name('users.export');

        // User parameter routes (these come AFTER export)
        Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

        // OTP Verification routes
        Route::post('/users/{user}/send-otp', [AdminController::class, 'sendVerificationOtp'])->name('users.send-otp');
        Route::post('/users/{user}/verify-otp', [AdminController::class, 'verifyOtp'])->name('users.verify-otp');
        Route::post('/users/{user}/resend-otp', [AdminController::class, 'resendOtp'])->name('users.resend-otp');

        // Additional user actions
        Route::post('/users/{user}/verify-email', [AdminController::class, 'verifyEmail'])->name('users.verify-email');
        Route::patch('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
        Route::put('/users/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('users.reset-password');

        // Statistics
        Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');

        // Species management
        Route::resource('species', SpeciesController::class);
        Route::patch('/species/{species}/toggle-status', [SpeciesController::class, 'toggleStatus'])->name('species.toggle-status');
        Route::get('/species/export', [SpeciesController::class, 'export'])->name('species.export');

        // Categories management
        Route::resource('categories', CategoryController::class);
    });
});
