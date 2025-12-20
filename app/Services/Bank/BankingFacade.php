<?php

namespace App\Services;

use App\Models\Account;
use App\Services\Transaction\TransactionService;
use App\Models\Transaction;

class BankingFacade
{
    public function __construct(
        private TransactionService $transactionService
    ) {}

    /**
     * تحويل مالي مبسط
     */
    public function transfer(int $fromId, int $toId, float $amount, string $note = '')
    {
        $from = Account::findOrFail($fromId);
        $to = Account::findOrFail($toId);

        return $this->transactionService->transfer($from, $to, $amount, $note);
    }

    /**
     * الحصول على رصيد حساب (يدعم الحسابات المركبة Composite)
     */
    public function getBalance(int $accountId)
    {
        $account = Account::findOrFail($accountId);
        // هنا يمكنك استدعاء خدمة الرصيد التي تدعم الـ Composite
        return $account->balance;
    }
    public function transferFunds(int $fromId, int $toId, float $amount, string $note = '')
    {
        $from = Account::findOrFail($fromId);
        $to = Account::findOrFail($toId);

        return $this->transactionService->transfer($from, $to, $amount, $note);
    }
}
