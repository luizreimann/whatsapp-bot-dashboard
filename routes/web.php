<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\LeadController;

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
    ->group(function () {

        // Homepage
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Bot
        Route::get('/bot', [BotController::class, 'index'])->name('dashboard.bot');

        // Leads
        Route::get('/leads', [LeadController::class, 'index'])->name('dashboard.leads');
        Route::get('/leads/data', [LeadController::class, 'data'])->name('dashboard.leads.data');
        Route::get('/leads/lead/{lead}', [LeadController::class, 'show'])->name('dashboard.leads.show');
        Route::patch('/leads/{lead}/notes', [LeadController::class, 'updateNotes'])
            ->name('dashboard.leads.notes.update');

        // Logout
        Route::get('/logout', [DashboardController::class, 'logout'])->name('dashboard.logout');
    });