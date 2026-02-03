<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\BotController;
use App\Http\Controllers\Dashboard\LeadController;
use App\Http\Controllers\Dashboard\Integrations\IntegrationController;
use App\Http\Controllers\Dashboard\SubscriptionController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\TenantController;

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

// Register
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])
    ->name('register')
    ->middleware('guest');

Route::post('/register', [RegisterController::class, 'register'])
    ->name('register.post')
    ->middleware('guest');

// Checkout
Route::prefix('checkout')
    ->middleware('auth')
    ->name('checkout.')
    ->group(function () {
        Route::get('/success', [CheckoutController::class, 'success'])->name('success');
        Route::get('/{subscription}', [CheckoutController::class, 'index'])->name('index');
        Route::post('/{subscription}/payment', [CheckoutController::class, 'createPayment'])->name('payment');
        Route::post('/{subscription}/process', [CheckoutController::class, 'processPayment'])->name('process');
        Route::patch('/{subscription}/amount', [CheckoutController::class, 'updateAmount'])->name('amount.update');
    });

// Webhooks (sem autenticação)
Route::post('/webhooks/stripe', [WebhookController::class, 'stripe'])->name('webhooks.stripe');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// Dashboard
Route::prefix('dashboard')
    ->middleware(['auth', 'paid'])
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

        // Integrações
        Route::prefix('integrations')
            ->name('integrations.')
            ->group(function () {

                Route::get('/', [IntegrationController::class, 'index'])
                    ->name('index');

                Route::get('/connect/{provider}', [IntegrationController::class, 'showConnectForm'])
                    ->name('connect');

                Route::post('/connect/{provider}', [IntegrationController::class, 'connect'])
                    ->name('connect.store');

                Route::delete('/{integrationAccount}', [IntegrationController::class, 'disconnect'])
                    ->name('disconnect');

            });

        // Subscription
        Route::prefix('subscription')
            ->name('subscription.')
            ->group(function () {
                Route::get('/', [SubscriptionController::class, 'index'])->name('index');
                Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
            });
    });

// Admin
Route::prefix('admin')
    ->middleware(['auth', 'admin'])
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        
        Route::prefix('tenants')
            ->name('tenants.')
            ->group(function () {
                Route::get('/', [TenantController::class, 'index'])->name('index');
                Route::get('/{tenant}', [TenantController::class, 'show'])->name('show');
                Route::post('/{tenant}/suspend', [TenantController::class, 'suspend'])->name('suspend');
                Route::post('/{tenant}/reactivate', [TenantController::class, 'reactivate'])->name('reactivate');
                Route::post('/{tenant}/payment-link', [TenantController::class, 'generatePaymentLink'])->name('payment-link');
            });
    });