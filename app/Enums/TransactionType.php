<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TransactionType: string implements HasLabel, HasColor
{
    case REVENUE = 'revenue';
    case EXPENSE = 'expense';
    case TRANSFER = 'transfer';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::REVENUE => 'Entrada',
            self::EXPENSE => 'Saída',
            self::TRANSFER => 'Transferência',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::REVENUE => 'success',
            self::EXPENSE => 'danger',
            self::TRANSFER => 'info',
        };
    }

}
