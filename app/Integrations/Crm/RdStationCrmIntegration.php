<?php

namespace App\Integrations\Crm;

use App\Enums\IntegrationCategory;
use App\Enums\IntegrationProvider;
use App\Models\IntegrationAccount;
use App\Models\Lead;
use App\Integrations\Contracts\CrmIntegrationContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Integração com o RD Station CRM (API v1).
 *
 * Responsabilidades:
 * - expor metadados da integração (provider, categoria, label, ícone, auth_type);
 * - encapsular acesso à config (token, etc) a partir do IntegrationAccount;
 * - oferecer métodos de alto nível (testConnection, syncLead).
 *
 * Essa classe é o "modelo" para as próximas integrações de CRM.
 */
class RdStationCrmIntegration implements CrmIntegrationContract
{
    /**
     * Metadados estáticos da integração.
     *
     * Esse padrão deve ser replicado para os próximos provedores.
     */
    protected static array $meta = [
        'provider'    => IntegrationProvider::RD_STATION_CRM,
        'category'    => IntegrationCategory::CRM,
        'label'       => 'RD Station CRM',
        'description' => 'Envie leads qualificados para o RD Station CRM.',
        'icon'        => 'rdstation.png',
        'auth_type'   => 'api_token', // usamos token único do usuário do RD CRM
    ];

    /**
     * Endpoint base da API v1 do RD Station CRM.
     *
     * Ex.: https://crm.rdstation.com/api/v1/contacts?token=XYZ
     */
    protected string $baseUrl = 'https://crm.rdstation.com/api/v1';

    public function __construct(
        protected IntegrationAccount $account,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Meta estática (para IntegrationRegistry / dashboard)
    |--------------------------------------------------------------------------
    */

    public static function provider(): IntegrationProvider
    {
        return static::$meta['provider'];
    }

    public static function category(): IntegrationCategory
    {
        return static::$meta['category'];
    }

    public static function label(): string
    {
        return static::$meta['label'];
    }

    public static function description(): ?string
    {
        return static::$meta['description'];
    }

    public static function icon(): string
    {
        return static::$meta['icon'];
    }

    /**
     * Tipo de autenticação (api_token, oauth, etc.).
     */
    public static function authType(): string
    {
        return static::$meta['auth_type'];
    }

    /**
     * Meta "mastigado" para o IntegrationRegistry e views.
     */
    public static function meta(): array
    {
        return [
            'provider'    => static::provider()->value,
            'category'    => static::category()->value,
            'label'       => static::label(),
            'description' => static::description(),
            'icon'        => static::icon(),
            'auth_type'   => static::authType(),
            'class'       => static::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers de config
    |--------------------------------------------------------------------------
    */

    /**
     * Retorna a config completa ou uma chave específica.
     */
    public function getConfig(?string $key = null, mixed $default = null): mixed
    {
        $config = $this->account->config ?? [];

        if ($key === null) {
            return $config;
        }

        return Arr::get($config, $key, $default);
    }

    /**
     * Atualiza a config da conta de integração (merge por padrão).
     */
    public function setConfig(array $config, bool $merge = true): self
    {
        $current = $this->account->config ?? [];

        $this->account->config = $merge
            ? array_replace_recursive($current, $config)
            : $config;

        $this->account->save();

        return $this;
    }

    /**
     * Token do RD Station CRM.
     *
     * Por padrão esperamos "token", mas aceitamos "api_key" como fallback
     * pra ficar flexível com o nome do campo do formulário.
     */
    protected function getToken(): ?string
    {
        return $this->getConfig('token')
            ?? $this->getConfig('api_key');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers HTTP e log (reutilizáveis para outros provedores)
    |--------------------------------------------------------------------------
    */

    protected function client()
    {
        return Http::acceptJson()
            ->asJson()
            ->timeout(10);
    }

    /**
     * Monta URL com token na query string.
     *
     * A documentação de Contatos v1 mostra o `token` como query param. 
     */
    protected function url(string $path, array $query = []): string
    {
        $base = rtrim($this->baseUrl, '/');
        $path = '/' . ltrim($path, '/');

        $query = array_merge(
            ['token' => $this->getToken()],
            $query
        );

        $queryString = http_build_query(array_filter(
            $query,
            fn ($v) => $v !== null && $v !== ''
        ));

        return $base . $path . ($queryString ? '?' . $queryString : '');
    }

    protected function log(string $message, array $context = []): void
    {
        Log::info($message, array_merge([
            'integration' => static::provider()->value,
            'tenant_id'   => $this->account->tenant_id ?? null,
            'account_id'  => $this->account->id ?? null,
        ], $context));
    }

    /*
    |--------------------------------------------------------------------------
    | Implementação do contrato de CRM
    |--------------------------------------------------------------------------
    */

    /**
     * Testa o token fazendo uma listagem simples de contatos.
     *
     * Se o token estiver inválido, a API retorna erro HTTP (401/403).
     * Usamos isso como "ping" da integração. 
     */
    public function testConnection(): bool
    {
        $token = $this->getToken();

        if (empty($token)) {
            $this->log('RD Station CRM: testConnection sem token configurado.');

            $this->account->status = 'error';
            $this->account->metadata = array_merge($this->account->metadata ?? [], [
                'last_test_at' => now()->toIso8601String(),
                'last_test_ok' => false,
                'last_error'   => 'Token não configurado.',
            ]);
            $this->account->save();

            return false;
        }

        $response = $this->client()->get(
            $this->url('/contacts', ['limit' => 1])
        );

        if ($response->successful()) {
            $this->log('RD Station CRM: testConnection OK.');

            $this->account->status = 'connected';
            $this->account->metadata = array_merge($this->account->metadata ?? [], [
                'last_test_at' => now()->toIso8601String(),
                'last_test_ok' => true,
            ]);
            $this->account->save();

            return true;
        }

        $this->log('RD Station CRM: testConnection FAILED.', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);

        $this->account->status = 'error';
        $this->account->metadata = array_merge($this->account->metadata ?? [], [
            'last_test_at' => now()->toIso8601String(),
            'last_test_ok' => false,
            'last_error'   => $response->body(),
            'last_status'  => $response->status(),
        ]);
        $this->account->save();

        return false;
    }

    /**
     * MVP: cria/sincroniza um contato no RD Station CRM a partir de um Lead.
     *
     * Usa o endpoint POST /contacts da API v1. 
     */
    public function syncLead(Lead $lead, array $options = []): array
    {
        $token = $this->getToken();

        if (empty($token)) {
            $this->log('RD Station CRM: syncLead sem token configurado.', [
                'lead_id' => $lead->id,
            ]);

            return [
                'contact_id' => null,
                'raw'        => null,
            ];
        }

        $name = trim((string) $lead->name);
        if (mb_strlen($name) < 2) {
            $name = 'Lead Zaptria #' . $lead->id;
        }

        $phones = [];
        if (! empty($lead->phone)) {
            $phones[] = [
                'phone' => $lead->phone,
                'type'  => 'work', // valores aceitos: home, work, fax 
            ];
        }

        $emails = [];
        if (! empty($lead->email)) {
            $emails[] = [
                'email' => $lead->email,
            ];
        }

        $notes = null;
        $data  = $lead->data ?? [];
        if (! empty($data)) {
            $notes = 'Lead criado pelo Zaptria.';
            if (! empty($lead->source)) {
                $notes .= ' Fonte: ' . $lead->source;
            }
            if (! empty($data['utm_source'])) {
                $notes .= ' | utm_source=' . $data['utm_source'];
            }
            if (! empty($data['utm_campaign'])) {
                $notes .= ' | utm_campaign=' . $data['utm_campaign'];
            }
        }

        // Corpo aceito para criação de contato na API v1:
        // name, emails, phones, etc. 
        $payload = array_filter([
            'name'   => $name,
            'emails' => $emails ?: null,
            'phones' => $phones ?: null,
            'notes'  => $notes,
        ]);

        $this->log('RD Station CRM: criando contato a partir de lead.', [
            'lead_id' => $lead->id,
            'payload' => $payload,
        ]);

        $response = $this->client()->post(
            $this->url('/contacts'),
            $payload
        );

        $body = $response->json();

        if (! $response->successful()) {
            $this->log('RD Station CRM: erro ao criar contato.', [
                'lead_id' => $lead->id,
                'status'  => $response->status(),
                'body'    => $response->body(),
            ]);

            return [
                'contact_id' => null,
                'raw'        => $body,
            ];
        }

        $contactId = $body['_id'] ?? $body['id'] ?? null;

        $this->log('RD Station CRM: contato criado com sucesso.', [
            'lead_id'    => $lead->id,
            'contact_id' => $contactId,
        ]);

        return [
            'contact_id' => $contactId,
            'raw'        => $body,
        ];
    }
}