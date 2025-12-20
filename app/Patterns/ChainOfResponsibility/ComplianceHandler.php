<?php

namespace App\Patterns\ChainOfResponsibility;

/*
|--------------------------------------------------------------------------
| Compliance Handler
|--------------------------------------------------------------------------
| Final handler in the approval chain that performs compliance checks
| such as anti-money laundering (AML) rules, regulatory limits, or
| account status validation. This handler ensures all transactions
| meet regulatory and policy requirements before final approval.
|
| Since this is typically the last handler in the chain, it makes
| the final approval decision. If compliance checks pass, the transaction
| is approved; otherwise, it is rejected with a compliance reason.
*/

use App\Models\Transaction;

class ComplianceHandler extends TransactionHandler
{
    private ?string $reason = null;

    /**
     * Perform final compliance checks on the transaction.
     */
    public function handle(Transaction $transaction): bool
    {
        // Check account status
        if (!$this->checkAccountStatus($transaction)) {
            $this->reason = "Account status check failed - account may be closed or frozen";

            return false;
        }

        // Check for suspicious activity patterns
        if ($this->isSuspiciousActivity($transaction)) {
            $this->reason = "Transaction flagged for suspicious activity - requires additional review";

            return false;
        }

        // Check currency compliance
        if (!$this->checkCurrencyCompliance($transaction)) {
            $this->reason = "Currency compliance check failed";

            return false;
        }

        // All compliance checks passed
        $this->reason = "Transaction passed all compliance checks";

        return true;
    }

    /**
     * Check if the source and destination accounts are in valid states.
     */
    private function checkAccountStatus(Transaction $transaction): bool
    {
        $fromAccount = $transaction->fromAccount;
        $toAccount = $transaction->toAccount;

        // Check source account - must exist and be in valid state
        if (!$fromAccount) {
            return false;
        }

        // Allow null state or common valid states
        $validStates = ['active', 'open', null];
        if ($fromAccount->state && !in_array($fromAccount->state, ['active', 'open'])) {
            return false;
        }

        // Check destination account (only for transfers with to_account_id)
        if ($toAccount) {
            if ($toAccount->state && !in_array($toAccount->state, ['active', 'open'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check for suspicious activity patterns (simplified example).
     * In production, this would integrate with AML systems.
     */
    private function isSuspiciousActivity(Transaction $transaction): bool
    {
        $amount = (float) $transaction->amount;

        // Example: Flag transactions over a very high threshold
        if ($amount > 50000.0) {
            // In production, this would check against AML databases
            return false; // For now, allow but could be enhanced
        }

        // Check for round numbers that might indicate structuring
        // (e.g., multiple transactions just under reporting thresholds)
        // This is a simplified example

        return false;
    }

    /**
     * Check currency compliance (e.g., supported currencies).
     */
    private function checkCurrencyCompliance(Transaction $transaction): bool
    {
        $currency = strtoupper($transaction->currency ?? 'USD');
        $supportedCurrencies = ['USD', 'EUR', 'GBP', 'JPY'];

        return in_array($currency, $supportedCurrencies);
    }

    /**
     * Get the reason for the decision made by this handler.
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }
}

