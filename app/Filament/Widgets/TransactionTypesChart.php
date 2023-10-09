<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class TransactionTypesChart extends ChartWidget
{
    protected static ?string $heading = 'Receita x Despesa por Mês';

    protected static ?int $sort = 1;

    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {
        $data = $this->getTransactionRevenueAndExpensePerMonth();

        return [
            'datasets' => [
                [
                    'label' => 'Receita',
                    'data' => $data['revenue'],
                    'backgroundColor' => '#16a34a',
                    'borderColor' => '#16a34a',
                    'pointBackgroundColor' => '#16a34a',
                ],
                [
                    'label' => 'Despesa',
                    'data' => $data['expense'],
                    'backgroundColor' => '#dc2626',
                    'borderColor' => '#dc2626',
                    'pointBackgroundColor' => '#dc2626',
                ],
            ],
            'labels' => $data['months'],

        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getTransactionRevenueAndExpensePerMonth(): array
    {
        $now = Carbon::now()->locale('pt-BR');

        // Obtenha a data atual (ano e mês) com um limite de 12 meses atrás
        $startDate = $now->subMonths(11);

        $revenue = [];
        $expense = [];

        $months = collect(range(0, 11))->map(function ($offset) use ($startDate, &$revenue, &$expense) {
            // Calcule o mês atual com base na data de início e no deslocamento
            $currentMonth = $startDate->copy()->addMonths($offset);

            $revenueCount = Transaction::whereYear('payment_date', $currentMonth->year)
                ->whereMonth('payment_date', $currentMonth->month)
                ->where('has_been_paid', true)
                ->where('type', 'revenue')
                ->sum('amount');

            $revenue[] = $revenueCount;

            $expenseCount = Transaction::whereYear('payment_date', $currentMonth->year)
                ->whereMonth('payment_date', $currentMonth->month)
                ->where('has_been_paid', true)
                ->where('type', 'expense')
                ->sum('amount');

            $expense[] = $expenseCount;

            return $currentMonth->translatedFormat('F Y');
        })->toArray();

        return [
            'revenue' => $revenue,
            'expense' => $expense,
            'months' => $months,
        ];
    }
}

