<?php

namespace App\Services\Payment;

use App\Models\Tenant;
use App\Models\WhatsappInstance;
use App\Models\Flux;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TenantProvisioningService
{
    public function provision(Tenant $tenant): void
    {
        Log::info('Starting tenant provisioning', ['tenant_id' => $tenant->id]);

        $this->createWhatsappInstance($tenant);
        $this->createWelcomeFlux($tenant);

        Log::info('Tenant provisioned successfully', [
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
        ]);
    }

    public function suspend(Tenant $tenant): void
    {
        Log::info('Suspending tenant', ['tenant_id' => $tenant->id]);

        $tenant->update(['status' => 'suspended']);

        Log::info('Tenant suspended', [
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
        ]);
    }

    public function reactivate(Tenant $tenant): void
    {
        Log::info('Reactivating tenant', ['tenant_id' => $tenant->id]);

        $tenant->update(['status' => 'active']);

        Log::info('Tenant reactivated', [
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
        ]);
    }

    protected function createWhatsappInstance(Tenant $tenant): void
    {
        $existingInstance = WhatsappInstance::where('tenant_id', $tenant->id)->first();

        if ($existingInstance) {
            Log::info('WhatsApp instance already exists', [
                'tenant_id' => $tenant->id,
                'instance_id' => $existingInstance->id,
            ]);
            return;
        }

        WhatsappInstance::create([
            'tenant_id' => $tenant->id,
            'status' => 'inactive',
            'bot_token' => Str::random(40),
        ]);

        Log::info('WhatsApp instance created', ['tenant_id' => $tenant->id]);
    }

    protected function createWelcomeFlux(Tenant $tenant): void
    {
        $existingFlux = Flux::where('tenant_id', $tenant->id)
            ->where('name', 'Fluxo de Boas-vindas')
            ->first();

        if ($existingFlux) {
            Log::info('Welcome flux already exists', [
                'tenant_id' => $tenant->id,
                'flux_id' => $existingFlux->id,
            ]);
            return;
        }

        Flux::create([
            'tenant_id' => $tenant->id,
            'name' => 'Fluxo de Boas-vindas',
            'status' => 'draft',
            'data' => $this->getWelcomeFlowTemplate(),
            'conversion_goal' => 'Capturar informações básicas do lead',
        ]);

        Log::info('Welcome flux created', ['tenant_id' => $tenant->id]);
    }

    protected function getWelcomeFlowTemplate(): array
    {
        return [
            'version' => 1,
            'description' => 'Fluxo de exemplo para captura de leads',
            'nodes' => [
                [
                    'id' => 'start-1',
                    'type' => 'start',
                    'position' => ['x' => 100, 'y' => 100],
                    'data' => [
                        'label' => 'Início',
                        'trigger' => 'any_message',
                    ],
                ],
                [
                    'id' => 'message-1',
                    'type' => 'message',
                    'position' => ['x' => 100, 'y' => 200],
                    'data' => [
                        'label' => 'Mensagem de Boas-vindas',
                        'text' => 'Olá! Bem-vindo ao nosso atendimento. Como posso ajudar você hoje?',
                        'delay' => 0,
                    ],
                ],
                [
                    'id' => 'question-1',
                    'type' => 'question',
                    'position' => ['x' => 100, 'y' => 300],
                    'data' => [
                        'label' => 'Capturar Nome',
                        'question' => 'Qual é o seu nome?',
                        'variable' => 'nome',
                        'validation' => 'text',
                    ],
                ],
                [
                    'id' => 'action-1',
                    'type' => 'action',
                    'position' => ['x' => 100, 'y' => 400],
                    'data' => [
                        'label' => 'Salvar Lead',
                        'action' => 'save_lead',
                        'fields' => [
                            'name' => '{{nome}}',
                        ],
                    ],
                ],
                [
                    'id' => 'end-1',
                    'type' => 'end',
                    'position' => ['x' => 100, 'y' => 500],
                    'data' => [
                        'label' => 'Fim',
                        'message' => 'Obrigado, {{nome}}! Em breve entraremos em contato.',
                    ],
                ],
            ],
            'edges' => [
                [
                    'id' => 'e1',
                    'source' => 'start-1',
                    'target' => 'message-1',
                ],
                [
                    'id' => 'e2',
                    'source' => 'message-1',
                    'target' => 'question-1',
                ],
                [
                    'id' => 'e3',
                    'source' => 'question-1',
                    'target' => 'action-1',
                ],
                [
                    'id' => 'e4',
                    'source' => 'action-1',
                    'target' => 'end-1',
                ],
            ],
        ];
    }
}
