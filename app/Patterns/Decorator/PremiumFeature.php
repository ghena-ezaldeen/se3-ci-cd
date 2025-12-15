<?php

namespace App\Patterns\Decorator;

/*
|--------------------------------------------------------------------------
| Premium Feature Decorator
|--------------------------------------------------------------------------
| Adds premium account benefits such as higher interest rates, reduced
| transaction fees, or priority customer service. This decorator can
| modify account behavior and metadata without changing the core account
| structure.
|
| Premium features are typically applied to accounts that meet certain
| criteria (minimum balance, account age, etc.) and provide enhanced
| services to valued customers. The decorator pattern allows these
| features to be added or removed dynamically based on account status.
*/

use App\Patterns\Composite\AccountComponent;

class PremiumFeature extends AccountDecorator
{
    private float $interestRateMultiplier;
    private float $feeReductionPercentage;

    public function __construct(
        AccountComponent $component,
        float $interestRateMultiplier = 1.5,
        float $feeReductionPercentage = 0.5
    ) {
        parent::__construct($component);
        $this->interestRateMultiplier = $interestRateMultiplier;
        $this->feeReductionPercentage = $feeReductionPercentage;
    }

    /**
     * Premium accounts maintain the same balance, but may have enhanced
     * interest calculations applied elsewhere in the system.
     */
    public function getBalance(): float
    {
        return parent::getBalance();
    }

    /**
     * Get the enhanced interest rate multiplier for premium accounts.
     * This can be used by interest calculation services to apply higher rates.
     */
    public function getInterestRateMultiplier(): float
    {
        return $this->interestRateMultiplier;
    }

    /**
     * Get the fee reduction percentage for premium accounts.
     * Transactions fees can be reduced by this percentage when this feature is active.
     */
    public function getFeeReductionPercentage(): float
    {
        return $this->feeReductionPercentage;
    }

    /**
     * Calculate the effective interest rate for this account.
     * Combines the base account interest rate with the premium multiplier.
     */
    public function getEffectiveInterestRate(): float
    {
        $baseRate = (float) $this->getAccount()->interest_rate;

        return $baseRate * $this->interestRateMultiplier;
    }

    /**
     * Calculate the reduced transaction fee for premium accounts.
     */
    public function calculateReducedFee(float $baseFee): float
    {
        return $baseFee * (1 - $this->feeReductionPercentage);
    }
}

