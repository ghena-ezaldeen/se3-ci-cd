<?php

namespace App\Patterns\Strategy;

/*
|--------------------------------------------------------------------------
| Interest Strategy Interface
|--------------------------------------------------------------------------
| Defines the contract for different interest calculation strategies.
| This interface allows the system to swap between calculation methods
| (standard, premium, overdraft) without modifying the calculation
| service, following the Strategy pattern for flexible interest rules.
|
| Each strategy implementation handles a specific scenario, making it
| easy to add new interest calculation rules in the future.
*/

interface InterestStrategy
{
    /**
     * Calculate interest based on account balance and interest rate.
     *
     * @param float $balance The account balance to calculate interest on
     * @param float $rate The base interest rate (as decimal, e.g., 0.05 for 5%)
     * @return float The calculated interest amount
     */
    public function calculateInterest(float $balance, float $rate): float;
}

