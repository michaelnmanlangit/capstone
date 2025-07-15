<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegistrationController;

Route::get('/', function () {
    return view('welcome');
});


// General dashboard (optional)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Registration
Route::get('/register', function () {
    return view('auth.register'); 
})->name('register.form');

Route::post('/register', [RegistrationController::class, 'register'])->name('register');

// Admin dashboard
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'role:admin'])->name('admin.dashboard');

// Responder dashboard
Route::get('/responder/home', function () {
    return view('responder.home');
})->middleware(['auth', 'role:responder'])->name('responder.home');

// Civilian dashboard
Route::get('/civilian/home', function () {
    return view('civilian.home');
})->middleware(['auth', 'role:civilian'])->name('civilian.home');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
