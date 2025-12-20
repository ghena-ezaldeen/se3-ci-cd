<?php

namespace App\Models;

/*
|--------------------------------------------------------------------------
| Transaction Model
|--------------------------------------------------------------------------
| Represents money movement events (deposit, withdrawal, transfer) with
| approval flags for workflows. It ties accounts, initiators, audit logs,
| scheduled instructions, and payment gateway records together so later
| services can orchestrate approvals and posting consistently.
*/

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'from_account_id',
        'to_account_id',
        'scheduled_transaction_id',
        'initiated_by',
        'type',
        'amount',
        'currency',
        'status',
        'description',
        'approval_required',
    ];

    protected $casts = [
        'approval_required' => 'boolean',
    ];

    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function auditLogs()
    {
        return $this->hasMany(TransactionAuditLog::class);
    }

    public function scheduledInstruction()
    {
        return $this->belongsTo(ScheduledTransaction::class, 'scheduled_transaction_id');
    }

    public function payments()
    {
        return $this->hasMany(PaymentLog::class);
    }
}

