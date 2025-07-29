<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ResponderController;
use App\Http\Controllers\CivilianController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/role', [AdminController::class, 'updateRole'])->name('users.update-role');
});

// Responder Routes
Route::middleware(['auth', 'role:responder,admin'])->prefix('responder')->name('responder.')->group(function () {
    Route::get('/dashboard', [ResponderController::class, 'dashboard'])->name('dashboard');
    Route::get('/incidents', [ResponderController::class, 'incidents'])->name('incidents');
    Route::get('/profile', [ResponderController::class, 'profile'])->name('profile');
});

// Civilian Routes
Route::middleware(['auth', 'role:civilian,admin'])->prefix('civilian')->name('civilian.')->group(function () {
    Route::get('/dashboard', [CivilianController::class, 'dashboard'])->name('dashboard');
    Route::get('/report-incident', [CivilianController::class, 'reportIncident'])->name('report-incident');
    Route::get('/send-sos', [CivilianController::class, 'sendSOS'])->name('send-sos');
    Route::get('/profile', [CivilianController::class, 'profile'])->name('profile');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
