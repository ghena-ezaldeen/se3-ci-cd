<?php
namespace App\Patterns\State;

use App\Models\Account;

interface AccountStateInterface
{
    public function canWithdraw(Account $account): bool;
    public function canDeposit(Account $account): bool;

    public function activate(Account $account): void;
    public function freeze(Account $account): void;
    public function suspend(Account $account): void;
    public function close(Account $account): void;
}
