<?php

namespace App\Models;

/*
|--------------------------------------------------------------------------
| Scheduled Transaction Model
|--------------------------------------------------------------------------
| Holds instructions for future or recurring transfers so they can be
| executed by schedulers without manual input. It keeps source/target
| accounts, timing, and status in one place for later automation layers.
*/

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledTransaction extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'from_account_id',
        'to_account_id',
        'type',
        'amount',
        'currency',
        'scheduled_for',
        'frequency',
        'status',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
    ];

    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'scheduled_transaction_id');
    }
}

