<?php

namespace App\Services\Transaction;

/*
|--------------------------------------------------------------------------
| Transaction Approval Service
|--------------------------------------------------------------------------
| Orchestrates the transaction approval workflow using the Chain of
| Responsibility pattern. This service builds the approval chain with
| handlers (amount limit, manager approval, compliance) and processes
| transactions through the chain to determine approval status.
|
| The service logs approval decisions and reasons from each handler,
| providing a complete audit trail for compliance and troubleshooting.
| Handlers can be configured with different thresholds and rules based
| on business requirements.
*/

use App\Models\Transaction;
use App\Models\TransactionAuditLog;
use App\Patterns\ChainOfResponsibility\AmountLimitHandler;
use App\Patterns\ChainOfResponsibility\ComplianceHandler;
use App\Patterns\ChainOfResponsibility\ManagerApprovalHandler;
use App\Patterns\ChainOfResponsibility\TransactionHandler;
use Illuminate\Support\Facades\Log;

class TransactionApprovalService
{
    private TransactionHandler $chain;

    public function __construct()
    {
        $this->buildChain();
    }

    /**
     * Process a transaction through the approval chain.
     * Returns true if approved, false if rejected.
     * Logs the decision and reason for audit purposes.
     *
     * @param Transaction $transaction The transaction to approve
     * @return bool True if approved, false if rejected
     */
    public function approveTransaction(Transaction $transaction): bool
    {
        $approved = $this->chain->handle($transaction);
        $reason = $this->getChainReason($transaction);

        // Log the approval decision
        $this->logApprovalDecision($transaction, $approved, $reason);

        // Create audit log entry
        $this->createAuditLog($transaction, $approved, $reason);

        return $approved;
    }

    /**
     * Build the approval chain with handlers in order:
     * 1. Amount Limit Handler (auto-approve small transactions)
     * 2. Manager Approval Handler (require approval for large amounts)
     * 3. Compliance Handler (final compliance checks)
     */
    private function buildChain(): void
    {
        $amountLimitHandler = new AmountLimitHandler(1000.0);
        $managerHandler = new ManagerApprovalHandler(10000.0);
        $complianceHandler = new ComplianceHandler();

        // Build the chain
        $amountLimitHandler
            ->setNext($managerHandler)
            ->setNext($complianceHandler);

        $this->chain = $amountLimitHandler;
    }

    /**
     * Get the reason from the handler chain.
     * Since handlers set their reason when making decisions, we collect
     * reasons from all handlers that processed the transaction.
     */
    private function getChainReason(Transaction $transaction): ?string
    {
        // Collect reasons from all handlers in the chain
        $reasons = [];
        $handler = $this->chain;

        while ($handler !== null) {
            $reason = $handler->getReason();
            if ($reason !== null) {
                $reasons[] = $reason;
            }

            // Use reflection to access protected nextHandler property
            $reflection = new \ReflectionClass($handler);
            if ($reflection->hasProperty('nextHandler')) {
                $property = $reflection->getProperty('nextHandler');
                $property->setAccessible(true);
                $handler = $property->getValue($handler);
            } else {
                break;
            }
        }

        return !empty($reasons) ? implode(' | ', $reasons) : 'No reason provided';
    }

    /**
     * Log the approval decision to Laravel's log system.
     */
    private function logApprovalDecision(Transaction $transaction, bool $approved, ?string $reason): void
    {
        $status = $approved ? 'APPROVED' : 'REJECTED';
        $message = "Transaction {$transaction->id} {$status}: {$reason}";

        Log::info($message, [
            'transaction_id' => $transaction->id,
            'amount' => $transaction->amount,
            'type' => $transaction->type,
            'status' => $status,
            'reason' => $reason,
        ]);
    }

    /**
     * Create an audit log entry in the database.
     */
    private function createAuditLog(Transaction $transaction, bool $approved, ?string $reason): void
    {
        $notes = json_encode([
            'reason' => $reason,
            'amount' => $transaction->amount,
            'type' => $transaction->type,
        ], JSON_PRETTY_PRINT);

        TransactionAuditLog::create([
            'transaction_id' => $transaction->id,
            'action' => $approved ? 'approved' : 'rejected',
            'performed_by' => $transaction->initiated_by,
            'notes' => $notes,
        ]);
    }

    /**
     * Rebuild the approval chain with custom thresholds.
     * Useful for testing or different approval rules.
     */
    public function rebuildChain(
        float $autoApproveLimit = 1000.0,
        float $managerThreshold = 10000.0
    ): void {
        $amountLimitHandler = new AmountLimitHandler($autoApproveLimit);
        $managerHandler = new ManagerApprovalHandler($managerThreshold);
        $complianceHandler = new ComplianceHandler();

        $amountLimitHandler
            ->setNext($managerHandler)
            ->setNext($complianceHandler);

        $this->chain = $amountLimitHandler;
    }
}

