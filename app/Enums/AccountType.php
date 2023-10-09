<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AccountType: string implements HasLabel
{
    case SAVINGS = 'savings';
    case CURRENT = 'current';
    case INVESTMENT = 'investment';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SAVINGS => 'Poupança',
            self::CURRENT => 'Corrente',
            self::INVESTMENT => 'Investimento',
        };
    }



}
