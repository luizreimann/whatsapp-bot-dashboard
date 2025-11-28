<?php

namespace App\Enums;

enum LeadStatus: string
{
    case NEW = 'new';
    case QUALIFIED = 'qualified';
    case IN_PROGRESS = 'in_progress';
    case LOST = 'lost';

    /**
     * Retorna o rótulo em português.
     */
    public function label(): string
    {
        return match($this) {
            self::NEW         => 'Novo',
            self::QUALIFIED   => 'Qualificado',
            self::IN_PROGRESS => 'Em Progresso',
            self::LOST        => 'Perdido',
        };
    }

    /**
     * Retorna uma cor de badge baseada no status.
     */
    public function color(): string
    {
        return match($this) {
            self::NEW         => 'primary',
            self::QUALIFIED   => 'success',
            self::IN_PROGRESS => 'warning',
            self::LOST        => 'danger',
        };
    }

    /**
     * Converte string para enum (seguro).
     */
    public static function fromValue(string $value): self
    {
        return self::tryFrom($value) ?? self::NEW;
    }
}