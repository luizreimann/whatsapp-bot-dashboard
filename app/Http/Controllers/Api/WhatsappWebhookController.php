<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Services\Whatsapp\WhatsappWebhookService;

class WhatsappWebhookController extends Controller
{
    protected $service;

    public function __construct(WhatsappWebhookService $service)
    {
        $this->service = $service;
    }

    /**
     * POST /api/tenants/{tenant}/whatsapp/qr
     */
    public function qr(Request $request, Tenant $tenant)
    {
        $botToken = $request->header('X-Bot-Token');

        if (!$this->service->validateBotToken($tenant, $botToken)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $payload = $request->only(['qr', 'status']);

        $this->service->handleQr($tenant, $payload);

        return response()->json(['ok' => true]);
    }

    /**
     * POST /api/tenants/{tenant}/whatsapp/status
     */
    public function status(Request $request, Tenant $tenant)
    {
        $botToken = $request->header('X-Bot-Token');

        if (!$this->service->validateBotToken($tenant, $botToken)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $payload = $request->all();

        $this->service->handleStatus($tenant, $payload);

        return response()->json(['ok' => true]);
    }

    /**
     * POST /api/tenants/{tenant}/whatsapp/incoming
     */
    public function incoming(Request $request, Tenant $tenant)
    {
        $botToken = $request->header('X-Bot-Token');

        if (!$this->service->validateBotToken($tenant, $botToken)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $payload = $request->all();

        $result = $this->service->handleIncoming($tenant, $payload);

        return response()->json($result);
    }
}