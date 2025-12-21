<?php
namespace App\Services\Transaction;

use App\Models\Transaction;
use App\Models\TransactionAuditLog;
use Illuminate\Support\Facades\Auth;

class TransactionAuditService
{
    public function log(Transaction $transaction, string $action, ?string $notes = null): void
    {
        TransactionAuditLog::create([
            'transaction_id' => $transaction->id,
            'performed_by'   => Auth::id() ?? 1,
            'action'         => $action,
            'notes'          => $notes,
        ]);
    }
}
