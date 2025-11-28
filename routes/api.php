<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WhatsappWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aqui ficam todas as rotas que o container Node (whatsapp-bot) vai chamar.
| Elas são registradas com o prefixo "/api" automaticamente porque definimos
| isso em bootstrap/app.php usando o withRouting().
|
| Cada rota é protegida por um token via header "X-Bot-Token".
|
*/

Route::middleware('api')->group(function () {

    // Teste básico
    Route::get('/ping', function () {
        return ['message' => 'pong'];
    });

    // Webhooks do bot por Tenant
    Route::prefix('tenants/{tenant}')->group(function () {

        // O bot envia QR Code para o dashboard
        Route::post('/whatsapp/qr', [WhatsappWebhookController::class, 'qr']);

        // O bot envia status de conexão (connected, disconnected, auth_failure...)
        Route::post('/whatsapp/status', [WhatsappWebhookController::class, 'status']);

        // O bot envia mensagens recebidas do WhatsApp
        Route::post('/whatsapp/incoming', [WhatsappWebhookController::class, 'incoming']);
    });

});