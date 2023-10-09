<?php

namespace App\Filament\Pages;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    public function getColumns(): int | string | array
    {
        return 1;
    }

}
