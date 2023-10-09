<?php

namespace App\Filament\Widgets;

use App\Enums\AccountType;
use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{

    protected static bool $isLazy = true;
    protected function getStats(): array
    {
        $accountBalance = $this->getAccountBalance();
        $monthlyRevenue = $this->getMonthlyRevenue();
        $monthlyExpense = $this->getMonthlyExpense();


        return [
            Stat::make('Saldo', $accountBalance)
                ->description('Somente Contas Correntes'),
            Stat::make('Receita Mensal', $monthlyRevenue['currentMonthRevenue'])
                ->description($monthlyRevenue['description'])
                ->descriptionIcon($monthlyRevenue['descriptionIcon'])
                ->color($monthlyRevenue['color']),
            Stat::make('Despesa Mensal', $monthlyExpense['currentMonthExpense'])
                ->description($monthlyExpense['description'])
                ->descriptionIcon($monthlyExpense['descriptionIcon'])
                ->color($monthlyExpense['color']),

        ];
    }

    protected function getAccountBalance(): string
    {
        return 'R$ ' . number_format(Account::where('account_type', AccountType::CURRENT)->sum('balance'), 2, ',', '.');
    }

    protected function getMonthlyRevenue(): array
    {
        $currentMonth = date('m');
        $lastMonth = date('m', strtotime('-1 month'));

        $currentMonthRevenue = Transaction::where('type', TransactionType::REVENUE)
            ->whereMonth('payment_date', $currentMonth)
            ->where('has_been_paid', true)
            ->sum('amount');



        $lastMonthRevenue = Transaction::where('type', TransactionType::REVENUE)
            ->whereMonth('payment_date', $lastMonth)
            ->where('has_been_paid', true)
            ->sum('amount');


        $description = '-';
        $descriptionIcon = '';
        $color = 'gray';

        if ($currentMonthRevenue > $lastMonthRevenue) {
            $difference = number_format(($currentMonthRevenue - $lastMonthRevenue), 2, ',', '.');
            $description = 'Aumento de R$ ' . $difference;
            $descriptionIcon = 'heroicon-m-arrow-trending-up';
            $color = 'success';
        } elseif ($currentMonthRevenue < $lastMonthRevenue) {
            $difference = number_format(($lastMonthRevenue - $currentMonthRevenue), 2, ',', '.');
            $description = 'Redução de R$ ' . $difference;
            $descriptionIcon = 'heroicon-m-arrow-trending-down';
            $color = 'danger';
        } else {
            $description = 'Mesma receita que o mês anterior';
            $descriptionIcon = 'heroicon-m-exclamation-circle';
            $color = 'success';
        }

        return [
            'currentMonthRevenue' => 'R$ ' . number_format( $currentMonthRevenue, 2, ',', '.'),
            'description' => $description,
            'descriptionIcon' => $descriptionIcon,
            'color' => $color,
        ];
    }

    protected function getMonthlyExpense(): array
    {
        $currentMonth = date('m');
        $lastMonth = date('m', strtotime('-1 month'));

        $currentMonthExpense = Transaction::where('type', TransactionType::EXPENSE)
            ->whereMonth('payment_date', $currentMonth)
            ->where('has_been_paid', true)
            ->sum('amount');

        $lastMonthExpense = Transaction::where('type', TransactionType::EXPENSE)
            ->whereMonth('payment_date', $lastMonth)
            ->where('has_been_paid', true)
            ->sum('amount');


        $description = '-';
        $descriptionIcon = '';
        $color = 'gray';

        if ($currentMonthExpense > $lastMonthExpense) {
            $difference = number_format(($currentMonthExpense - $lastMonthExpense), 2, ',', '.');
            $description = 'Aumento de R$ ' . $difference;
            $descriptionIcon = 'heroicon-m-arrow-trending-up';
            $color = 'danger';
        } elseif ($currentMonthExpense < $lastMonthExpense) {
            $difference = number_format(($lastMonthExpense - $currentMonthExpense), 2, ',', '.');
            $description = 'Redução de R$ ' . $difference;
            $descriptionIcon = 'heroicon-m-arrow-trending-down';
            $color = 'success';
        } else {
            $description = 'Mesma despesa que o mês anterior';
            $descriptionIcon = 'heroicon-m-exclamation-circle';
            $color = 'warning';
        }

        return [
            'currentMonthExpense' => 'R$ ' . number_format( $currentMonthExpense, 2, ',', '.'),
            'description' => $description,
            'descriptionIcon' => $descriptionIcon,
            'color' => $color,
        ];
    }
}
