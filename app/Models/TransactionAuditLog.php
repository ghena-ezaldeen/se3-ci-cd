<?php

namespace App\Models;

/*
|--------------------------------------------------------------------------
| Transaction Audit Log Model
|--------------------------------------------------------------------------
| Tracks actions taken on transactions (submission, approval, rejection)
| so the system retains a compliance-friendly history. It links a user to
| a transaction event, letting later reporting and review features surface
| who did what and when.
*/

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionAuditLog extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'transaction_id',
        'performed_by',
        'action',
        'notes',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}

