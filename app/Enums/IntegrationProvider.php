<?php

namespace App\Enums;

enum IntegrationProvider: string
{
    // CRM e Vendas
    case RD_STATION_CRM = 'rd_station_crm';
    case PIPEDRIVE      = 'pipedrive';

    // E-mail Marketing
    case MAILCHIMP      = 'mailchimp';

    // Gateways de Pagamento
    case MERCADO_PAGO   = 'mercado_pago';
    case PAGARME        = 'pagarme';

    // E-commerce
    case NUVEMSHOP      = 'nuvemshop';
    case WOOCOMMERCE    = 'woocommerce';

    // Tráfego
    case META_CAPI      = 'meta_capi';
    case GOOGLE_ADS     = 'google_ads';
    case GA4            = 'ga4';

    // Suporte
    case ZENDESK        = 'zendesk';

    // Automação
    case GOOGLE_SHEETS  = 'google_sheets';
    case PLUGA          = 'pluga';
    case WEBHOOK        = 'webhook';

    public function category(): IntegrationCategory
    {
        return match ($this) {
            self::RD_STATION_CRM,
            self::PIPEDRIVE,
                => IntegrationCategory::CRM,

            self::MAILCHIMP
                => IntegrationCategory::EMAIL_MARKETING,

            self::MERCADO_PAGO,
            self::PAGARME,
                => IntegrationCategory::PAYMENT,

            self::NUVEMSHOP,
            self::WOOCOMMERCE,
                => IntegrationCategory::ECOMMERCE,

            self::META_CAPI,
            self::GOOGLE_ADS,
            self::GA4,
                => IntegrationCategory::TRAFFIC,

            self::ZENDESK
                => IntegrationCategory::SUPPORT,

            self::GOOGLE_SHEETS,
            self::PLUGA,
            self::WEBHOOK,
                => IntegrationCategory::AUTOMATION,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::RD_STATION_CRM => 'RD Station CRM',
            self::PIPEDRIVE      => 'Pipedrive',
            self::MAILCHIMP      => 'Mailchimp',

            self::MERCADO_PAGO   => 'Mercado Pago',
            self::PAGARME        => 'Pagar.me',

            self::NUVEMSHOP      => 'Nuvemshop',
            self::WOOCOMMERCE    => 'WooCommerce',

            self::META_CAPI      => 'Meta Business CAPI',
            self::GOOGLE_ADS     => 'Google Ads',
            self::GA4            => 'Google Analytics 4',

            self::ZENDESK        => 'Zendesk',

            self::GOOGLE_SHEETS  => 'Google Sheets',
            self::PLUGA          => 'Pluga',
            self::WEBHOOK        => 'Webhook genérico',
        };
    }
}