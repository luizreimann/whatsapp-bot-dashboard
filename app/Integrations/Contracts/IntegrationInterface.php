<?php

namespace App\Integrations\Contracts;

use App\Enums\IntegrationCategory;
use App\Enums\IntegrationProvider;

interface IntegrationInterface
{
    public static function provider(): IntegrationProvider;

    public static function category(): IntegrationCategory;

    public static function label(): string;

    public static function description(): ?string;

    public static function icon(): string;

    public static function authType(): string;
}
