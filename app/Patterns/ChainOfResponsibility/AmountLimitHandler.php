<?php

namespace App\Patterns\ChainOfResponsibility;

/*
|--------------------------------------------------------------------------
| Amount Limit Handler
|--------------------------------------------------------------------------
| Automatically approves transactions that are below a configured amount
| threshold. This handler is typically the first in the chain, allowing
| small transactions to be approved immediately without requiring manual
| review or manager approval.
|
| Transactions exceeding the limit are passed to the next handler in
| the chain for further evaluation. This provides a fast path for
| routine transactions while ensuring larger amounts go through
| additional scrutiny.
*/

use App\Models\Transaction;

class AmountLimitHandler extends TransactionHandler
{
    private float $autoApproveLimit;
    private ?string $reason = null;

    public function __construct(float $autoApproveLimit = 1000.0)
    {
        $this->autoApproveLimit = $autoApproveLimit;
    }

    /**
     * Approve transactions below the limit, pass larger ones to the next handler.
     */
    public function handle(Transaction $transaction): bool
    {
        $amount = (float) $transaction->amount;

        if ($amount <= $this->autoApproveLimit) {
            $this->reason = "Transaction amount ({$amount}) is within auto-approve limit ({$this->autoApproveLimit})";

            return true;
        }

        // Amount exceeds limit, pass to next handler
        return $this->handleNext($transaction);
    }

    /**
     * Get the reason for the decision made by this handler.
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * Get the auto-approve limit configured for this handler.
     */
    public function getAutoApproveLimit(): float
    {
        return $this->autoApproveLimit;
    }
}

