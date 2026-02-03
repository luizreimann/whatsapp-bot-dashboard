<?php

namespace App\Integrations;

use App\Enums\IntegrationProvider;
use App\Integrations\Contracts\IntegrationInterface;
use App\Integrations\Crm\RdStationCrmIntegration;
use App\Integrations\Crm\PipedriveCrmIntegration;
use Illuminate\Support\Collection;

/**
 * Registry central de integrações disponíveis no sistema.
 *
 * Responsável por:
 * - saber quais integrações existem;
 * - expor metadados (label, categoria, auth_type, etc.) para o dashboard;
 * - permitir recuperar a definição de uma integração específica.
 */
final class IntegrationRegistry
{
    /**
     * Mapa de provider => classe de integração.
     *
     * @var array<string, class-string<IntegrationInterface>>
     */
    private const MAP = [
        // CRM
        IntegrationProvider::RD_STATION_CRM->value  => RdStationCrmIntegration::class,
        IntegrationProvider::PIPEDRIVE->value       => PipedriveCrmIntegration::class,

        // Adicione aqui os próximos providers...
        // IntegrationProvider::HUBSPOT->value => \App\Integrations\Crm\HubspotIntegration::class,
    ];

    /**
     * Retorna todas as integrações registradas com seus metadados.
     *
     * Estrutura de cada item:
     * [
     *   'provider'    => string,
     *   'category'    => string,
     *   'label'       => string,
     *   'description' => ?string,
     *   'icon'        => string,
     *   'auth_type'   => string,
     *   'class'       => class-string<IntegrationInterface>,
     * ]
     */
    public static function all(): Collection
    {
        return collect(self::MAP)->map(
            /**
             * @param  class-string<IntegrationInterface>  $class
             */
            function (string $class, string $provider): array {
                return [
                    'provider'    => $provider,
                    'category'    => $class::category()->value,
                    'label'       => $class::label(),
                    'description' => $class::description(),
                    'icon'        => $class::icon(),
                    'auth_type'   => $class::authType(),
                    'class'       => $class,
                ];
            }
        );
    }

    /**
     * Retorna metadados de uma integração específica.
     */
    public static function definition(IntegrationProvider $provider): ?array
    {
        /** @var class-string<IntegrationInterface>|null $class */
        $class = self::MAP[$provider->value] ?? null;

        if (! $class) {
            return null;
        }

        return [
            'provider'    => $provider->value,
            'category'    => $class::category()->value,
            'label'       => $class::label(),
            'description' => $class::description(),
            'icon'        => $class::icon(),
            'auth_type'   => $class::authType(),
            'class'       => $class,
        ];
    }

    /**
     * Retorna o FQCN da classe de integração para um provider.
     *
     * Útil quando você quer instanciar o service em runtime.
     *
     * @return class-string<IntegrationInterface>|null
     */
    public static function classForProvider(IntegrationProvider $provider): ?string
    {
        return self::MAP[$provider->value] ?? null;
    }
}