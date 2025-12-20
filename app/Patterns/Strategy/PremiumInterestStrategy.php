<?php

namespace App\Patterns\Strategy;

/*
|--------------------------------------------------------------------------
| Premium Interest Strategy
|--------------------------------------------------------------------------
| Implements enhanced interest calculation for premium accounts with
| a multiplier applied to the base rate. This strategy recognizes that
| premium accounts should receive higher interest rates as a benefit
| of their account tier.
|
| The multiplier is typically retrieved from the PremiumFeature decorator,
| allowing the interest calculation to be enhanced based on account
| features without modifying the core calculation logic.
*/

class PremiumInterestStrategy implements InterestStrategy
{
    private float $multiplier;

    public function __construct(float $multiplier = 1.5)
    {
        $this->multiplier = $multiplier;
    }

    /**
     * Calculate premium interest by applying a multiplier to the base rate.
     * Formula: balance * (rate * multiplier).
     * Only applies interest to positive balances.
     */
    public function calculateInterest(float $balance, float $rate): float
    {
        // Only calculate interest on positive balances
        if ($balance <= 0) {
            return 0.0;
        }

        $enhancedRate = $rate * $this->multiplier;

        return $balance * $enhancedRate;
    }

    /**
     * Get the interest rate multiplier for this premium strategy.
     */
    public function getMultiplier(): float
    {
        return $this->multiplier;
    }
}

