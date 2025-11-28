<?php

namespace App\Services\Whatsapp;

use App\Models\Tenant;
use App\Models\WhatsappInstance;
use App\Models\Lead;
use Illuminate\Support\Facades\Log;

class WhatsappWebhookService
{
    public function __construct()
    {
        //
    }

    /**
     * Valida o token vindo do bot/container
     */
    public function validateBotToken(Tenant $tenant, string $token = null): bool
    {
        $instance = $tenant->whatsappInstance;

        if (!$instance) {
            return false;
        }

        return $instance->bot_token === $token;
    }

    /**
     * Processa QR code enviado pelo bot Node
     */
    public function handleQr(Tenant $tenant, array $payload): void
    {
        $instance = $tenant->whatsappInstance;

        $instance->update([
            'status' => 'qr_ready',
            'last_status_payload' => $payload,
        ]);

        Log::info("QR atualizado para tenant {$tenant->id}");
    }

    /**
     * Processa mudanças de status: connected, disconnected, auth_failure etc.
     */
    public function handleStatus(Tenant $tenant, array $payload): void
    {
        $instance = $tenant->whatsappInstance;

        $status = $payload['status'] ?? 'unknown';

        $data = [
            'status' => $status,
            'last_status_payload' => $payload,
        ];

        if ($status === 'connected') {
            $data['last_connected_at'] = now();
        }

        if (!empty($payload['number'])) {
            $data['number'] = $payload['number'];
        }

        $instance->update($data);

        Log::info("Status ({$status}) atualizado para tenant {$tenant->id}", [
            'number' => $instance->number ?? $payload['number'] ?? null,
        ]);
    }

    /**
     * Processa mensagens recebidas pelo bot (incoming messages)
     */
    public function handleIncoming(Tenant $tenant, array $payload): array
    {
        Log::info("Mensagem recebida", [
            'tenant_id' => $tenant->id,
            'payload' => $payload,
        ]);

        // Aqui futuramente:
        // - recuperar sessão de fluxo
        // - rodar step da máquina de estados
        // - gerar resposta (actions)

        return [
            'actions' => [],
        ];
    }
}