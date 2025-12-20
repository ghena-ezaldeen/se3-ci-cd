<?php

namespace App\Patterns\Strategy;

/*
|--------------------------------------------------------------------------
| Standard Interest Strategy
|--------------------------------------------------------------------------
| Implements the standard interest calculation formula: balance * rate.
| This is the default strategy used for regular accounts without special
| features. It applies simple interest calculation where the interest
| amount is directly proportional to the account balance and interest rate.
|
| This strategy is used when an account has no premium features or
| overdraft protection that would require different calculation logic.
*/

class StandardInterestStrategy implements InterestStrategy
{
    /**
     * Calculate standard interest using the formula: balance * rate.
     * Only applies interest to positive balances.
     */
    public function calculateInterest(float $balance, float $rate): float
    {
        // Only calculate interest on positive balances
        if ($balance <= 0) {
            return 0.0;
        }

        return $balance * $rate;
    }
}

