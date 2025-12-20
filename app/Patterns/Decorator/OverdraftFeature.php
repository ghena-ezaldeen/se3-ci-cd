<?php

namespace App\Patterns\Decorator;

/*
|--------------------------------------------------------------------------
| Overdraft Feature Decorator
|--------------------------------------------------------------------------
| Adds overdraft protection to an account, allowing withdrawals that
| exceed the current balance up to a configured limit. This decorator
| modifies the available balance calculation to include the overdraft
| amount, enabling transactions that would otherwise be rejected.
|
| The overdraft limit is stored in the AccountFeature metadata, allowing
| different accounts to have different overdraft limits. This feature is
| commonly applied to checking accounts to prevent declined transactions.
*/

use App\Patterns\Composite\AccountComponent;

class OverdraftFeature extends AccountDecorator
{
    private float $overdraftLimit;

    public function __construct(AccountComponent $component, float $overdraftLimit)
    {
        parent::__construct($component);
        $this->overdraftLimit = $overdraftLimit;
    }

    /**
     * Returns the effective available balance including overdraft protection.
     * If the account balance is negative, the overdraft limit is still available
     * until the total (balance + overdraft) reaches zero.
     */
    public function getBalance(): float
    {
        $baseBalance = parent::getBalance();

        // Available balance is base balance plus overdraft limit
        return $baseBalance + $this->overdraftLimit;
    }

    /**
     * Get the maximum amount that can be withdrawn including overdraft.
     */
    public function getAvailableBalance(): float
    {
        return $this->getBalance();
    }

    /**
     * Get the configured overdraft limit for this account.
     */
    public function getOverdraftLimit(): float
    {
        return $this->overdraftLimit;
    }

    /**
     * Check if a withdrawal amount is within the available balance including overdraft.
     */
    public function canWithdraw(float $amount): bool
    {
        $baseBalance = parent::getBalance();

        return ($baseBalance + $this->overdraftLimit) >= $amount;
    }
}

