<?php

namespace App\Services\Account;

/*
|--------------------------------------------------------------------------
| Account Balance Service
|--------------------------------------------------------------------------
| Builds an account composite tree and computes aggregate balances that
| include all descendants. Keeps composite construction isolated so
| controllers can ask for totals without managing recursion or Eloquent
| loading concerns.
*/

use App\Models\Account;
use App\Patterns\Composite\AccountComponent;
use App\Patterns\Composite\AccountComposite;
use App\Patterns\Composite\AccountLeaf;

class AccountBalanceService
{
    public function computeAggregateBalance(Account $account): float
    {
        $component = $this->buildComponent($account);

        return $component->getBalance();
    }

    private function buildComponent(Account $account): AccountComponent
    {
        $account->loadMissing('children');

        if ($account->children->isEmpty()) {
            return new AccountLeaf($account);
        }

        $composite = new AccountComposite($account);

        foreach ($account->children as $child) {
            $composite->add($this->buildComponent($child));
        }

        return $composite;
    }
}

