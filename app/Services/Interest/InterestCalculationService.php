<?php

namespace App\Services\Interest;

/*
|--------------------------------------------------------------------------
| Interest Calculation Service
|--------------------------------------------------------------------------
| Orchestrates interest calculation using the Strategy pattern. This service
| determines which interest calculation strategy to use based on account
| features (premium, overdraft) and applies the appropriate calculation.
|
| The service integrates with the Composite and Decorator patterns, allowing
| it to work with both simple accounts and decorated account components.
| It automatically detects premium features and overdraft status to select
| the correct strategy without requiring manual strategy selection.
*/

use App\Models\Account;
use App\Patterns\Composite\AccountComponent;
use App\Patterns\Decorator\AccountDecorator;
use App\Patterns\Decorator\OverdraftFeature;
use App\Patterns\Decorator\PremiumFeature;
use App\Patterns\Strategy\InterestStrategy;
use App\Patterns\Strategy\OverdraftInterestStrategy;
use App\Patterns\Strategy\PremiumInterestStrategy;
use App\Patterns\Strategy\StandardInterestStrategy;
use App\Services\Account\AccountFeatureService;

class InterestCalculationService
{
    public function __construct(
        private AccountFeatureService $featureService
    ) {
    }

    /**
     * Calculate interest for an account using the appropriate strategy.
     * Automatically selects the strategy based on account features and balance.
     *
     * @param Account|AccountComponent $account The account or decorated component
     * @return float The calculated interest amount
     */
    public function calculateInterest(Account|AccountComponent $account): float
    {
        $accountModel = $this->getAccountModel($account);
        $component = $this->getAccountComponent($account);
        
        // Get base balance from account model (not decorated balance)
        // This is important for overdraft calculations
        $baseBalance = (float) $accountModel->balance;
        $decoratedBalance = $component->getBalance();
        
        $baseRate = (float) ($accountModel->interest_rate ?? 0.0);

        // Convert percentage to decimal if needed (e.g., 5% -> 0.05)
        $rate = $baseRate > 1 ? $baseRate / 100 : $baseRate;

        // Use base balance for strategy selection (to detect negative balances)
        // but pass base balance to strategy for accurate calculation
        $strategy = $this->selectStrategy($component, $baseBalance);

        return $strategy->calculateInterest($baseBalance, $rate);
    }

    /**
     * Get the appropriate interest calculation strategy based on account features.
     */
    private function selectStrategy(AccountComponent $component, float $balance): InterestStrategy
    {
        // Check for overdraft feature first
        $overdraftDecorator = $this->findDecorator($component, OverdraftFeature::class);
        if ($overdraftDecorator && $balance < 0) {
            return new OverdraftInterestStrategy(0.15); // 15% penalty rate
        }

        // Check for premium feature
        $premiumDecorator = $this->findDecorator($component, PremiumFeature::class);
        if ($premiumDecorator) {
            $multiplier = $premiumDecorator->getInterestRateMultiplier();

            return new PremiumInterestStrategy($multiplier);
        }

        // Default to standard interest
        return new StandardInterestStrategy();
    }

    /**
     * Extract the Account model from either an Account instance or AccountComponent.
     */
    private function getAccountModel(Account|AccountComponent $account): Account
    {
        return $account instanceof AccountComponent
            ? $account->getAccount()
            : $account;
    }

    /**
     * Get an AccountComponent from either an Account or AccountComponent.
     * If an Account is provided, builds a decorated component using the feature service.
     */
    private function getAccountComponent(Account|AccountComponent $account): AccountComponent
    {
        if ($account instanceof AccountComponent) {
            return $account;
        }

        // Build decorated component from account model
        return $this->featureService->buildDecoratedComponent($account);
    }

    /**
     * Find a decorator of a specific type in the decorator chain.
     */
    private function findDecorator(AccountComponent $component, string $decoratorClass): ?AccountDecorator
    {
        if ($component instanceof AccountDecorator) {
            return $component->findDecorator($decoratorClass);
        }

        return null;
    }
}

