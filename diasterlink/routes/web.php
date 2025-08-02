<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ResponderController;
use App\Http\Controllers\CivilianController;
use App\Http\Controllers\SOSController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\CommunityPostController;
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
    Route::get('/profile', [CivilianController::class, 'profile'])->name('profile');
    Route::get('/sos', [CivilianController::class, 'sos'])->name('sos');
    Route::get('/incident', [CivilianController::class, 'incident'])->name('incident');
});

// API Routes for Dashboard functionality
Route::middleware('auth')->group(function () {
    // SOS Routes
    Route::post('/sos', [SOSController::class, 'store'])->name('sos.store');
    
    // Incident Routes
    Route::resource('incidents', IncidentController::class);
    
    // Community Routes
    Route::get('/community', [CommunityController::class, 'index'])->name('community.index');
    Route::get('/community/search', [CommunityController::class, 'search'])->name('community.search');
    Route::post('/community/posts', [CommunityPostController::class, 'store'])->name('community.posts.store');
    Route::post('/community/posts/{post}/react', [CommunityPostController::class, 'react'])->name('community.posts.react');
    Route::post('/community/posts/{post}/comment', [CommunityPostController::class, 'comment'])->name('community.posts.comment');
    
    // Notification Routes
    Route::get('/notifications/check', [NotificationController::class, 'check'])->name('notifications.check');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
