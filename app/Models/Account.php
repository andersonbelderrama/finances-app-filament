<?php

namespace App\Models;

use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
            $query->where('accounts.user_id', auth()->user()->id);
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
