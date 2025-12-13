<?php

namespace App\Models;

/*
|--------------------------------------------------------------------------
| Account Model
|--------------------------------------------------------------------------
| Captures bank account data (savings, checking, loan, investment) and
| supports parent/child hierarchies for aggregated portfolios. It anchors
| transactions, scheduled transfers, and feature decorators so the rest of
| the system can work off a single source of truth for balances and state.
*/

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'parent_id',
        'account_number',
        'type',
        'state',
        'balance',
        'currency',
        'interest_rate',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function features()
    {
        return $this->hasMany(AccountFeature::class);
    }

    public function outgoingTransactions()
    {
        return $this->hasMany(Transaction::class, 'from_account_id');
    }

    public function incomingTransactions()
    {
        return $this->hasMany(Transaction::class, 'to_account_id');
    }

    public function scheduledTransactions()
    {
        return $this->hasMany(ScheduledTransaction::class, 'from_account_id');
    }
}

