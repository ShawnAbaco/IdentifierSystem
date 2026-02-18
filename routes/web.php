<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IdentificationController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SpeciesController;


Route::get('/test-email', function() {
    $user = App\Models\User::first();

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


// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/', function () {
//     return view('camera');
// });

// // Optional: Save prediction results
// Route::post('/save-result', function (Request $req) {
//     \Log::info($req->all());
//     return response()->json(['status' => 'saved']);
// });



// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Password Reset Routes
Route::prefix('password')->name('password.')->group(function () {
    Route::get('/forgot', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('request');
    Route::post('/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('email');
    Route::get('/verify', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showOtpForm'])->name('verify.form');
    Route::post('/verify', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'verifyOtp'])->name('verify.otp');
    Route::get('/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetForm'])->name('reset.form');
    Route::post('/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'reset'])->name('update');
});

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/register/send-otp', [RegisterController::class, 'sendOtp'])->name('register.send-otp');
Route::post('/register/verify-otp', [RegisterController::class, 'verifyOtp'])->name('register.verify-otp');
Route::post('/register/resend-otp', [RegisterController::class, 'resendOtp'])->name('register.resend-otp');



// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

     // Profile routes
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/update-password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.update-password');

    // Identification
    Route::get('/identify', [IdentificationController::class, 'index'])->name('identify');
    Route::post('/identify/store', [IdentificationController::class, 'store'])->name('identify.store');
    Route::get('/identification/{identification}', [IdentificationController::class, 'show'])->name('identification.show');
    Route::post('/identification/{identification}/feedback', [IdentificationController::class, 'feedback'])->name('identification.feedback');
    Route::delete('/identification/{identification}', [IdentificationController::class, 'destroy'])->name('identification.destroy');

    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('history');




// Admin routes (require admin role)
Route::prefix('admin')->name('admin.')->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');

    // User Management
    Route::get('/users', [App\Http\Controllers\Admin\AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [App\Http\Controllers\Admin\AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [App\Http\Controllers\Admin\AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}', [App\Http\Controllers\Admin\AdminController::class, 'showUser'])->name('users.show');
    Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [App\Http\Controllers\Admin\AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [App\Http\Controllers\Admin\AdminController::class, 'destroyUser'])->name('users.destroy');

// OTP Verification routes
Route::post('/users/{user}/send-otp', [AdminController::class, 'sendVerificationOtp'])->name('users.send-otp');
Route::post('/users/{user}/verify-otp', [AdminController::class, 'verifyOtp'])->name('users.verify-otp');
Route::post('/users/{user}/resend-otp', [AdminController::class, 'resendOtp'])->name('users.resend-otp');

    // Add these missing routes
    Route::post('/users/{user}/verify-email', [App\Http\Controllers\Admin\AdminController::class, 'verifyEmail'])->name('users.verify-email');
    Route::patch('/users/{user}/toggle-status', [App\Http\Controllers\Admin\AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
    Route::put('/users/{user}/reset-password', [App\Http\Controllers\Admin\AdminController::class, 'resetPassword'])->name('users.reset-password');

    Route::get('/statistics', [App\Http\Controllers\Admin\AdminController::class, 'statistics'])->name('statistics');

    // Species management
    Route::resource('species', App\Http\Controllers\Admin\SpeciesController::class);

    // Categories management
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);

    // Additional routes for species and categories
    Route::patch('/species/{species}/toggle-status', [App\Http\Controllers\Admin\SpeciesController::class, 'toggleStatus'])
        ->name('species.toggle-status');
    Route::get('/species/export', [App\Http\Controllers\Admin\SpeciesController::class, 'export'])
        ->name('species.export');
});


    // Public species view (accessible to everyone)
Route::get('/species/{species}', [App\Http\Controllers\SpeciesViewController::class, 'show'])
    ->name('species.show');

// API route for getting species by category
Route::get('/categories/{category}/species', [CategoryController::class, 'getSpecies'])
    ->name('categories.species');

});
