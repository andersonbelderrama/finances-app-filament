<?php

namespace App\Filament\Widgets;

use App\Models\Budget;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class BudgetProgressBar extends Widget
{
    protected static string $view = 'filament.widgets.budget-progress-bar';

    protected static ?int $sort = 3;

    protected function getViewData(): array
    {

        $budget = Budget::select('name', DB::raw('ROUND((budget_used / budget_limit) * 100, 1) as percentage'), 'budget_used', 'budget_limit')
        ->where('period', 'monthly')
        ->get();

        return [
            'items' => $budget
        ];
    }

}
