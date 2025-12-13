<?php

namespace App\Patterns\Composite;

/*
|--------------------------------------------------------------------------
| Account Composite
|--------------------------------------------------------------------------
| Represents a parent account that owns child accounts. It aggregates
| balances from its subtree so callers can treat the hierarchy as a single
| component when evaluating total funds.
*/

class AccountComposite extends AccountComponent
{
    public function getBalance(): float
    {
        $total = (float) $this->account->balance;

        foreach ($this->getChildren() as $child) {
            $total += $child->getBalance();
        }

        return $total;
    }
}

