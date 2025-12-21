<?php

namespace App\Services;

use App\Models\Account;
use App\Services\Transaction\TransactionService;
use App\Contracts\AccountServiceInterface;
use App\Services\Account\AccountBalanceService;

class BankingFacade
{
    public function __construct(
        private TransactionService $transactionService,
        private AccountServiceInterface $accountService,
        private AccountBalanceService $balanceService
    ) {}


    public function transferFunds(int $fromId, int $toId, float $amount, string $note = '') {
        $from = Account::findOrFail($fromId);
        $to = Account::findOrFail($toId);
        return $this->transactionService->transfer($from, $to, $amount, $note);
    }

    public function deposit(int $accountId, float $amount, string $note = '') {
        $account = Account::findOrFail($accountId);
        return $this->transactionService->deposit($account, $amount, $note);
    }

    public function withdraw(int $accountId, float $amount, string $note = '') {
        $account = Account::findOrFail($accountId);
        return $this->transactionService->withdraw($account, $amount, $note);
    }


    public function openAccount(array $data) {
        return $this->accountService->createAccount($data);
    }

    public function getTotalBalance(Account $account) {
        return $this->balanceService->computeAggregateBalance($account);
    }
}
