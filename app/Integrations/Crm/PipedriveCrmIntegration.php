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
 * Integração com o Pipedrive CRM (API v2).
 *
 * Responsabilidades:
 * - expor metadados da integração (provider, categoria, label, ícone, auth_type);
 * - encapsular acesso à config (api_token, company_domain) a partir do IntegrationAccount;
 * - oferecer métodos de alto nível (testConnection, syncLead).
 *
 * Essa classe segue o mesmo padrão da RdStationCrmIntegration
 * e serve de modelo para os próximos CRMs.
 */
class PipedriveCrmIntegration implements CrmIntegrationContract
{
    /**
     * Metadados estáticos da integração.
     */
    protected static array $meta = [
        'provider'    => IntegrationProvider::PIPEDRIVE,
        'category'    => IntegrationCategory::CRM,
        'label'       => 'Pipedrive',
        'description' => 'Crie e atualize contatos no Pipedrive a partir dos leads do seu fluxo.',
        'icon'        => 'pipedrive.png',
        'auth_type'   => 'api_token', // token pessoal do usuário Pipedrive
    ];

    /**
     * Base global da API para descoberta de company_domain.
     * Ex.: https://api.pipedrive.com/v1/users/me?api_token=...
     */
    protected string $globalBaseUrl = 'https://api.pipedrive.com/v1';

    /**
     * Base da API v2 por company_domain:
     * Ex.: https://{company_domain}.pipedrive.com/api/v2
     */
    protected ?string $baseUrl = null;

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

    public static function authType(): string
    {
        return static::$meta['auth_type'];
    }

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
    |
    | Convenção de chaves em $account->config:
    | - api_token        => string (obrigatório)
    | - company_domain   => string (opcional; será descoberto se vazio)
    |
    */

    public function getConfig(?string $key = null, mixed $default = null): mixed
    {
        $config = $this->account->config ?? [];

        if ($key === null) {
            return $config;
        }

        return Arr::get($config, $key, $default);
    }

    public function setConfig(array $config, bool $merge = true): self
    {
        $current = $this->account->config ?? [];

        $this->account->config = $merge
            ? array_replace_recursive($current, $config)
            : $config;

        $this->account->save();

        return $this;
    }

    protected function getApiToken(): ?string
    {
        // aceitamos api_token, token ou api_key por segurança
        return $this->getConfig('api_token')
            ?? $this->getConfig('token')
            ?? $this->getConfig('api_key');
    }

    protected function getCompanyDomain(): ?string
    {
        return $this->getConfig('company_domain');
    }

    /**
     * Garante que temos o company_domain:
     *
     * 1. Se já estiver em config, usa.
     * 2. Se não, chama a API global /users/me com api_token
     *    e salva o company_domain retornado.
     *
     * Docs oficiais mostram esse fluxo pra descobrir company_domain. 
     */
    protected function resolveCompanyDomain(): ?string
    {
        $domain = $this->getCompanyDomain();
        $token  = $this->getApiToken();

        if (!empty($domain) || empty($token)) {
            return $domain;
        }

        $this->log('Pipedrive: resolvendo company_domain via /users/me.');

        $response = $this->client()->get(
            rtrim($this->globalBaseUrl, '/') . '/users/me',
            [
                'api_token' => $token,
            ]
        );

        if (!$response->successful()) {
            $this->log('Pipedrive: falha ao descobrir company_domain.', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return null;
        }

        $body = $response->json();

        $domain = Arr::get($body, 'data.company_domain');

        if (!$domain) {
            $this->log('Pipedrive: resposta de /users/me sem company_domain esperado.', [
                'body' => $body,
            ]);

            return null;
        }

        // persiste para futuras chamadas
        $this->setConfig([
            'company_domain' => $domain,
        ]);

        return $domain;
    }

    /**
     * Garante e devolve a baseUrl (https://{domain}.pipedrive.com/api/v2).
     */
    protected function getBaseUrl(): ?string
    {
        if ($this->baseUrl !== null) {
            return $this->baseUrl;
        }

        $domain = $this->resolveCompanyDomain();

        if (!$domain) {
            return null;
        }

        // Recomendação oficial: usar {COMPANYDOMAIN}.pipedrive.com/api/v2. 
        $this->baseUrl = sprintf('https://%s.pipedrive.com/api/v2', $domain);

        return $this->baseUrl;
    }

    /*
    |--------------------------------------------------------------------------
    | HTTP + log helpers (reutilizáveis entre integrações)
    |--------------------------------------------------------------------------
    */

    protected function client()
    {
        return Http::acceptJson()
            ->asJson()
            ->timeout(10);
    }

    /**
     * Monta URL v2 com api_token em query string.
     */
    protected function url(string $path, array $query = []): ?string
    {
        $base = $this->getBaseUrl();
        $token = $this->getApiToken();

        if (!$base || !$token) {
            return null;
        }

        $base = rtrim($base, '/');
        $path = '/' . ltrim($path, '/');

        $query = array_merge(
            ['api_token' => $token],
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
    | Implementação do CrmIntegrationContract
    |--------------------------------------------------------------------------
    */

    /**
     * Testa o token + company_domain chamando /users/me.
     *
     * - Descobre company_domain se ainda não estiver salvo.
     * - Atualiza status e metadata da IntegrationAccount.
     */
    public function testConnection(): bool
    {
        $token = $this->getApiToken();

        if (empty($token)) {
            $this->log('Pipedrive: testConnection sem api_token configurado.');

            $this->account->status = 'error';
            $this->account->metadata = array_merge($this->account->metadata ?? [], [
                'last_test_at' => now()->toIso8601String(),
                'last_test_ok' => false,
                'last_error'   => 'API token não configurado.',
            ]);
            $this->account->save();

            return false;
        }

        $url = $this->url('/users/me');

        if (!$url) {
            $this->log('Pipedrive: testConnection sem company_domain resolvido.');

            $this->account->status = 'error';
            $this->account->metadata = array_merge($this->account->metadata ?? [], [
                'last_test_at' => now()->toIso8601String(),
                'last_test_ok' => false,
                'last_error'   => 'Não foi possível resolver o company_domain.',
            ]);
            $this->account->save();

            return false;
        }

        $response = $this->client()->get($url);

        if ($response->successful()) {
            $body = $response->json();

            $this->log('Pipedrive: testConnection OK.', [
                'user_email'      => Arr::get($body, 'data.email'),
                'company_domain'  => Arr::get($body, 'data.company_domain'),
            ]);

            $this->account->status = 'connected';
            $this->account->metadata = array_merge($this->account->metadata ?? [], [
                'last_test_at'          => now()->toIso8601String(),
                'last_test_ok'          => true,
                'pipedrive_user_email'  => Arr::get($body, 'data.email'),
                'pipedrive_company_id'  => Arr::get($body, 'data.company_id'),
                'pipedrive_company_name'=> Arr::get($body, 'data.company_name'),
            ]);
            $this->account->save();

            return true;
        }

        $this->log('Pipedrive: testConnection FAILED.', [
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
     * MVP: cria um "Person" no Pipedrive a partir de um Lead.
     *
     * Endpoint: POST /persons (API v2) com body JSON. 
     *
     * Retorno segue o contrato do CrmIntegrationContract:
     * [
     *   'contact_id' => int|null,
     *   'deal_id'    => null,        // (no futuro podemos criar negócio)
     *   'raw'        => mixed,
     * ]
     */
    public function syncLead(Lead $lead, array $options = []): array
    {
        $token = $this->getApiToken();
        $url   = $this->url('/persons');

        if (empty($token) || !$url) {
            $this->log('Pipedrive: syncLead sem api_token ou company_domain.', [
                'lead_id' => $lead->id,
            ]);

            return [
                'contact_id' => null,
                'deal_id'    => null,
                'raw'        => null,
            ];
        }

        $name = trim((string) $lead->name);
        if (mb_strlen($name) < 2) {
            $name = 'Lead Zaptria #' . $lead->id;
        }

        $emails = [];
        if (!empty($lead->email)) {
            $emails[] = [
                'primary' => true,
                'value'   => $lead->email,
                'label'   => 'work',
            ];
        }

        $phones = [];
        if (!empty($lead->phone)) {
            $phones[] = [
                'primary' => true,
                'value'   => $lead->phone,
                'label'   => 'work',
            ];
        }

        // visibilidade opcional (3 = toda a empresa) – podemos deixar configurable depois
        $visibleTo = $options['visible_to'] ?? null;

        $payload = array_filter([
            'name'       => $name,
            'emails'     => $emails ?: null,
            'phones'     => $phones ?: null,
            'visible_to' => $visibleTo, // se não vier, API assume default
        ], fn ($v) => $v !== null);

        $this->log('Pipedrive: criando pessoa a partir de lead.', [
            'lead_id' => $lead->id,
            'payload' => $payload,
        ]);

        $response = $this->client()->post($url, $payload);

        $body = $response->json();

        if (!$response->successful() || !Arr::get($body, 'success', false)) {
            $this->log('Pipedrive: erro ao criar pessoa.', [
                'lead_id' => $lead->id,
                'status'  => $response->status(),
                'body'    => $response->body(),
            ]);

            return [
                'contact_id' => null,
                'deal_id'    => null,
                'raw'        => $body,
            ];
        }

        $contactId = Arr::get($body, 'data.id');

        $this->log('Pipedrive: pessoa criada com sucesso.', [
            'lead_id'    => $lead->id,
            'contact_id' => $contactId,
        ]);

        return [
            'contact_id' => $contactId,
            'deal_id'    => null,
            'raw'        => $body,
        ];
    }
}