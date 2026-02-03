<?php

namespace App\Enums;

enum IntegrationCategory: string
{
    case CRM           = 'crm';
    case EMAIL_MARKETING = 'email_marketing';
    case PAYMENT       = 'payment';
    case ECOMMERCE     = 'ecommerce';
    case TRAFFIC       = 'traffic';
    case SUPPORT       = 'support';
    case AUTOMATION    = 'automation';

    public function label(): string
    {
        return match ($this) {
            self::CRM             => 'CRM & Vendas',
            self::EMAIL_MARKETING => 'E-mail Marketing',
            self::PAYMENT         => 'Pagamentos',
            self::ECOMMERCE       => 'E-commerce',
            self::TRAFFIC         => 'Tráfego & Anúncios',
            self::SUPPORT         => 'Suporte',
            self::AUTOMATION      => 'Automação',
        };
    }
}