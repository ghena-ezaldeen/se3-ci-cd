<?php

namespace App\Patterns\State;

use App\Models\Account;
use App\Patterns\State\AccountStateInterface;
use DomainException;

class ActiveState implements AccountStateInterface
{
    public function canWithdraw(Account $account): bool
    {
        return true;
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
        $account->state = 'frozen';
        $account->save();
    }

    public function suspend(Account $account): void
    {
        $account->state = 'suspended';
        $account->save();
    }

    public function close(Account $account): void
    {
        $account->state = 'closed';
        $account->save();
    }
}


