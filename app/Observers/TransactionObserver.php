<?php

namespace App\Observers;

use App\Models\Budget;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        $this->updateBudgetUsed($transaction);
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        $this->updateBudgetUsed($transaction);
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        $this->updateBudgetUsed($transaction);
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        $this->updateBudgetUsed($transaction);
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        $this->updateBudgetUsed($transaction);
    }

    protected function updateBudgetUsed(Transaction $transaction)
    {
        $category_id = $transaction->category_id;

        // Buscar todos os orçamentos com a mesma categoria
        $budgets = Budget::where('category_id', $category_id)->get();

        foreach ($budgets as $budget) {
            $period = $budget->period;

            $query = Transaction::where('category_id', $category_id)
                                ->where('type', 'expense')
                                ->where('user_id', Auth::user()->id);

            if ($period === 'monthly') {
                // Filtre apenas as transações do mês atual
                $query->whereMonth('payment_date', now()->month);
            } elseif ($period === 'quarterly') {
                // Filtre as transações dos últimos 3 meses
                $query->whereBetween('payment_date', [now()->subMonths(3), now()]);
            } elseif ($period === 'semiannually') {
                // Filtre as transações dos últimos 6 meses
                $query->whereBetween('payment_date', [now()->subMonths(6), now()]);
            } elseif ($period === 'yearly') {
                // Filtre as transações do último ano
                $query->whereYear('payment_date', now()->year);
            }

            $budget->budget_used = $query->sum('amount');
            $budget->save();
        }
    }
}
