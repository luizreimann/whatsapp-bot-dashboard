<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\BotController;
use App\Http\Controllers\Dashboard\LeadController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Login
Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.post')
    ->middleware('guest');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// Dashboard
Route::prefix('dashboard')
    ->middleware('auth')
    ->name('dashboard.')
    ->group(function () {

        // Homepage
        Route::get('/', [DashboardController::class, 'index'])->name('home');

        // Bot
        Route::get('/bot', [BotController::class, 'index'])->name('bot');

        // Leads
        Route::get('/leads', [LeadController::class, 'index'])->name('leads');
        Route::get('/leads/data', [LeadController::class, 'data'])->name('leads.data');
        Route::get('/leads/lead/{lead}', [LeadController::class, 'show'])->name('leads.show');
        Route::patch('/leads/{lead}/notes', [LeadController::class, 'updateNotes'])->name('leads.notes.update');

        // Logout
        Route::get('/logout', [DashboardController::class, 'logout'])->name('logout');
    });