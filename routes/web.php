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

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Identification
    Route::get('/identify', [IdentificationController::class, 'index'])->name('identify');
    Route::post('/identify/store', [IdentificationController::class, 'store'])->name('identify.store');
    Route::get('/identification/{identification}', [IdentificationController::class, 'show'])->name('identification.show');
    Route::post('/identification/{identification}/feedback', [IdentificationController::class, 'feedback'])->name('identification.feedback');
    Route::delete('/identification/{identification}', [IdentificationController::class, 'destroy'])->name('identification.destroy');

    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('history');

    // Admin routes (require admin role)
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');

        // Species management
        Route::resource('species', \App\Http\Controllers\Admin\SpeciesController::class);

        // Categories management
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    });

    // Additional routes for species and categories
Route::patch('/species/{species}/toggle-status', [SpeciesController::class, 'toggleStatus'])
    ->name('species.toggle-status');
Route::get('/species/export', [SpeciesController::class, 'export'])
    ->name('species.export');

// API route for getting species by category
Route::get('/categories/{category}/species', [CategoryController::class, 'getSpecies'])
    ->name('categories.species');


});
