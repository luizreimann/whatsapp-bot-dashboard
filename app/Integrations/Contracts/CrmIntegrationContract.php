<?php

namespace App\Integrations\Contracts;

use App\Models\Lead;

/**
 * Contrato base para integrações de CRM.
 *
 * Qualquer CRM (RD Station, Pipedrive, etc.) deve implementar esse contrato
 * para ser usado de forma padronizada pelo restante do sistema.
 */
interface CrmIntegrationContract
{
    /**
     * Verifica se as credenciais/configuração da conta são válidas.
     *
     * Deve atualizar o status da IntegrationAccount conforme a implementação
     * (ex.: connected, error) e retornar true/false.
     */
    public function testConnection(): bool;

    /**
     * Sincroniza um Lead do Zaptria com o CRM.
     *
     * MVP: geralmente significa criar/atualizar um Contato.
     * Futuro: pode também criar/atualizar um Negócio/Oportunidade.
     *
     * Convenção de retorno (pode ser expandida depois):
     * [
     *   'contact_id' => string|null, // ID do contato no CRM
     *   'deal_id'    => string|null, // (opcional) ID do negócio/oportunidade
     *   'raw'        => mixed,       // resposta original da API, se fizer sentido
     * ]
     */
    public function syncLead(Lead $lead, array $options = []): array;
}