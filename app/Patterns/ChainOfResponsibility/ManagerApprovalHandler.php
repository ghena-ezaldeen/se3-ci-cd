<?php

namespace App\Patterns\ChainOfResponsibility;

/*
|--------------------------------------------------------------------------
| Manager Approval Handler
|--------------------------------------------------------------------------
| Requires manager approval for transactions that exceed the auto-approve
| limit but are below a maximum threshold. This handler checks if the
| transaction has been pre-approved by a manager (via the approval_required
| flag or a related approval record).
|
| Transactions requiring manager approval that haven't been approved
| are rejected. Approved transactions or those below the manager threshold
| are passed to the next handler for final compliance checks.
*/

use App\Models\Transaction;

class ManagerApprovalHandler extends TransactionHandler
{
    private float $managerThreshold;
    private ?string $reason = null;

    public function __construct(float $managerThreshold = 10000.0)
    {
        $this->managerThreshold = $managerThreshold;
    }

    /**
     * Check if transaction requires and has manager approval.
     */
    public function handle(Transaction $transaction): bool
    {
        $amount = (float) $transaction->amount;

        // If amount exceeds manager threshold, require approval
        if ($amount > $this->managerThreshold) {
            // Check if transaction has been approved
            // In a real system, this would check an approval record or status
            $isApproved = $transaction->approval_required === false
                || $transaction->status === 'approved'
                || $this->hasManagerApproval($transaction);

            if (!$isApproved) {
                $this->reason = "Transaction amount ({$amount}) exceeds manager threshold ({$this->managerThreshold}) and requires manager approval";

                return false;
            }

            $this->reason = "Transaction approved by manager";
        }

        // Pass to next handler for compliance check
        return $this->handleNext($transaction);
    }

    /**
     * Check if the transaction has manager approval.
     * Checks if the initiator has manager or admin role, or if the transaction
     * has been pre-approved (approval_required is false).
     */
    private function hasManagerApproval(Transaction $transaction): bool
    {
        // If approval_required is false, transaction is already approved
        if ($transaction->approval_required === false) {
            return true;
        }

        // Check if initiator has manager or admin role
        if ($transaction->initiator) {
            $user = $transaction->initiator;
            $user->loadMissing('roles');

            $roleNames = $user->roles->pluck('name')->toArray();

            return in_array('manager', $roleNames) || in_array('admin', $roleNames);
        }

        // Default: require approval
        return false;
    }

    /**
     * Get the reason for the decision made by this handler.
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * Get the manager approval threshold.
     */
    public function getManagerThreshold(): float
    {
        return $this->managerThreshold;
    }
}

