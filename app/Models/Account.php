<?php

namespace App\Models;

use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Account extends Model
{
    use HasFactory;

    protected $casts = [
        'account_type' => AccountType::class
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(function ($query) {
            if (Auth::check()) {
                $query->where('accounts.user_id', Auth::user()->id);
            }
        });
    }

    public function transactions() : HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
