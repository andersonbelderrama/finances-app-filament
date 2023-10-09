<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BudgetPeriod: string implements HasLabel
{
    case MONTHLY  = 'monthly';
    case QUARTERLY = 'quarterly';
    case SEMI_ANNUALLY = 'semiannually';
    case YEARLY = 'yearly';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MONTHLY => 'Mensal',
            self::QUARTERLY => 'Trimestral',
            self::SEMI_ANNUALLY => 'Semestral',
            self::YEARLY => 'Anual',
        };
    }

}
