<?php

namespace App\Patterns\State;

use App\Models\Account;
use DomainException;

class ClosedState implements AccountStateInterface
{
    public function canWithdraw(Account $account): bool
    {
        return false;
    }

    public function canDeposit(Account $account): bool
    {
        return false;
    }

    public function activate(Account $account): void
    {
        throw new DomainException('Closed account cannot be activated');
    }

    public function freeze(Account $account): void
    {
        throw new DomainException('Closed account cannot be frozen');
    }

    public function suspend(Account $account): void
    {
        throw new DomainException('Closed account cannot be suspended');
    }

    public function close(Account $account): void
    {
        throw new DomainException('Account already closed');
    }
}
