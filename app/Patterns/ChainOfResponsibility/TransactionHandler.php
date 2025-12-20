<?php

namespace App\Patterns\ChainOfResponsibility;

/*
|--------------------------------------------------------------------------
| Transaction Handler (Chain of Responsibility Base)
|--------------------------------------------------------------------------
| Base abstract class for the Chain of Responsibility pattern used in
| transaction approval workflows. Each handler in the chain can either
| approve the transaction, reject it, or pass it to the next handler
| for further evaluation.
|
| This pattern allows transaction approval logic to be broken into
| discrete, testable handlers that can be composed in different orders
| or configurations. Handlers can log their decisions and reasons for
| approval or rejection, providing audit trails for compliance.
*/

use App\Models\Transaction;

abstract class TransactionHandler
{
    private ?TransactionHandler $nextHandler = null;

    /**
     * Set the next handler in the chain and return it for fluent chaining.
     */
    public function setNext(TransactionHandler $handler): TransactionHandler
    {
        $this->nextHandler = $handler;

        return $handler;
    }

    /**
     * Handle the transaction approval request.
     * Subclasses should implement their approval logic and either:
     * - Return true if approved (stops the chain)
     * - Return false if rejected (stops the chain)
     * - Call handleNext() to pass to the next handler
     *
     * @param Transaction $transaction The transaction to evaluate
     * @return bool True if approved, false if rejected
     */
    abstract public function handle(Transaction $transaction): bool;

    /**
     * Get the reason for approval or rejection (for logging/audit).
     * Subclasses should override this to provide specific reasons.
     */
    public function getReason(): ?string
    {
        return null;
    }

    /**
     * Pass the transaction to the next handler in the chain.
     * Returns false if there is no next handler (chain exhausted).
     */
    protected function handleNext(Transaction $transaction): bool
    {
        if ($this->nextHandler === null) {
            return false;
        }

        return $this->nextHandler->handle($transaction);
    }

    /**
     * Get the next handler in the chain.
     */
    protected function getNextHandler(): ?TransactionHandler
    {
        return $this->nextHandler;
    }
}

