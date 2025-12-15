<?php

namespace App\Patterns\Strategy;

/*
|--------------------------------------------------------------------------
| Overdraft Interest Strategy
|--------------------------------------------------------------------------
| Implements special interest calculation for accounts with negative
| balances (overdraft). When an account is overdrawn, interest is
| calculated differently - typically as a charge (negative interest)
| or at a different rate structure than standard interest.
|
| This strategy handles the case where accounts have used their overdraft
| protection, applying appropriate interest charges based on the
| overdraft amount and a penalty rate.
*/

class OverdraftInterestStrategy implements InterestStrategy
{
    private float $penaltyRate;

    public function __construct(float $penaltyRate = 0.15)
    {
        $this->penaltyRate = $penaltyRate;
    }

    /**
     * Calculate interest for overdraft accounts.
     * If balance is negative, applies penalty interest on the overdraft amount.
     * If balance is positive, applies standard interest.
     */
    public function calculateInterest(float $balance, float $rate): float
    {
        if ($balance < 0) {
            // Apply penalty interest on the overdraft amount (negative balance)
            return abs($balance) * $this->penaltyRate;
        }

        // For positive balances, apply standard interest
        return $balance * $rate;
    }

    /**
     * Get the penalty interest rate for overdraft accounts.
     */
    public function getPenaltyRate(): float
    {
        return $this->penaltyRate;
    }
}

