<?php

use App\Models\Account;
use App\Services\Account\AccountFeatureService;

class AccountTransactionService
{
    public function withdraw(Account $account, float $amount): void
    {
        $state = $account->stateObject();

        if (! $state->canWithdraw($account)) {
            throw new DomainException('Withdrawal not allowed in current state');
        }

        // حساب الرصيد المتاح باستخدام Decorator
        $component = app(AccountFeatureService::class)
            ->buildDecoratedComponent($account);

        if ($component->getBalance() < $amount) {
            throw new DomainException('Insufficient balance');
        }

        $account->balance -= $amount;
        $account->save();
    }

    public function deposit(Account $account, float $amount): void
    {
        $state = $account->stateObject();

        if (! $state->canDeposit($account)) {
            throw new DomainException('Deposit not allowed in current state');
        }

        $account->balance += $amount;
        $account->save();
    }
}
