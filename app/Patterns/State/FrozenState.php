<?php

namespace App\Patterns\State;

use App\Models\Account;
use DomainException;

class FrozenState implements AccountStateInterface
{
    public function canWithdraw(Account $account): bool
    {
        return false;
    }

    public function canDeposit(Account $account): bool
    {
        return true;
    }

    public function activate(Account $account): void
    {
        $account->state = 'active';
        $account->save();
    }

    public function freeze(Account $account): void
    {
        throw new DomainException('Account already frozen');
    }

    public function suspend(Account $account): void
    {
        throw new DomainException('Frozen account cannot be suspended');
    }

    public function close(Account $account): void
    {
        $account->state = 'closed';
        $account->save();
    }
}
