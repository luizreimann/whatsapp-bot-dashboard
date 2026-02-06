<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\BotController;
use App\Http\Controllers\Dashboard\LeadController;
use App\Http\Controllers\Dashboard\Integrations\IntegrationController;
use App\Http\Controllers\Dashboard\SubscriptionController;
use App\Http\Controllers\Dashboard\FluxController;
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

// Onboarding - Step 1 (dados pessoais)
Route::get('/register', [RegisterController::class, 'showStep1'])
    ->name('register')
    ->middleware('guest');

Route::post('/register', [RegisterController::class, 'processStep1'])
    ->name('register.step1')
    ->middleware('guest');

// Onboarding - Step 2 (dados da empresa, opcional)
Route::get('/register/company', [RegisterController::class, 'showStep2'])
    ->name('register.company')
    ->middleware('guest');

Route::post('/register/company', [RegisterController::class, 'processStep2'])
    ->name('register.step2')
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

        // Fluxos
        Route::prefix('fluxes')
            ->name('fluxes.')
            ->group(function () {
                Route::get('/', [FluxController::class, 'index'])->name('index');
                Route::get('/create', [FluxController::class, 'create'])->name('create');
                Route::post('/', [FluxController::class, 'store'])->name('store');
                Route::get('/{flux}/edit', [FluxController::class, 'edit'])->name('edit');
                Route::put('/{flux}', [FluxController::class, 'update'])->name('update');
                Route::delete('/{flux}', [FluxController::class, 'destroy'])->name('destroy');
                Route::post('/{flux}/duplicate', [FluxController::class, 'duplicate'])->name('duplicate');
                Route::post('/{flux}/toggle-status', [FluxController::class, 'toggleStatus'])->name('toggle-status');
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