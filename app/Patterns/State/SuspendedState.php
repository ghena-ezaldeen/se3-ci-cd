<?php
namespace App\Patterns\State;

use App\Models\Account;
use App\Patterns\State\AccountStateInterface;
use DomainException;

class SuspendedState implements AccountStateInterface
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
        throw new DomainException('Suspended account requires review');
    }

    public function freeze(Account $account): void
    {
        throw new DomainException('Suspended account cannot be frozen');
    }

    public function suspend(Account $account): void
    {
        throw new DomainException('Account already suspended');
    }

    public function close(Account $account): void
    {
        $account->state = 'closed';
        $account->save();
    }
}
