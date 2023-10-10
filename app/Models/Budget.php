<?php

namespace App\Models;

use App\Enums\BudgetPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Budget extends Model
{
    use HasFactory;

    protected $casts = [
        'period' => BudgetPeriod::class
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(function ($query) {
            if (Auth::check()) {
                $query->where('budgets.user_id', Auth::user()->id);
            }
        });
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
