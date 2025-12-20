<?php

namespace App\Services\Account;

/*
|--------------------------------------------------------------------------
| Account Feature Service
|--------------------------------------------------------------------------
| Manages the application and removal of feature decorators on accounts.
| This service builds decorated AccountComponent instances by wrapping
| the base composite tree with feature decorators based on the account's
| AccountFeature records stored in the database.
|
| The service ensures decorators are applied in the correct order and
| maintains compatibility with the Composite pattern used for parent/child
| account hierarchies. Features can be added or removed dynamically without
| modifying the core account structure.
*/

use App\Models\Account;
use App\Models\AccountFeature;
use App\Patterns\Composite\AccountComponent;
use App\Patterns\Composite\AccountComposite;
use App\Patterns\Composite\AccountLeaf;
use App\Patterns\Decorator\AccountDecorator;
use App\Patterns\Decorator\OverdraftFeature;
use App\Patterns\Decorator\PremiumFeature;
use Illuminate\Support\Facades\DB;

class AccountFeatureService
{
    /**
     * Build a decorated AccountComponent tree for the given account.
     * Applies all active features from the database as decorators.
     */
    public function buildDecoratedComponent(Account $account): AccountComponent
    {
        $baseComponent = $this->buildBaseComponent($account);

        return $this->applyFeatures($baseComponent, $account);
    }

    /**
     * Apply a feature decorator to an account by creating an AccountFeature record
     * and storing the feature configuration in the database.
     */
    public function applyFeature(
        Account $account,
        string $featureName,
        array $metadata = []
    ): AccountFeature {
        // Check if feature already exists
        $existing = AccountFeature::where('account_id', $account->id)
            ->where('feature_name', $featureName)
            ->first();

        if ($existing) {
            // Update existing feature metadata
            $existing->update(['metadata' => $metadata]);

            return $existing;
        }

        // Create new feature record
        return AccountFeature::create([
            'account_id' => $account->id,
            'feature_name' => $featureName,
            'description' => $this->getFeatureDescription($featureName),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Remove a feature decorator from an account by deleting the AccountFeature record.
     */
    public function removeFeature(Account $account, string $featureName): bool
    {
        return AccountFeature::where('account_id', $account->id)
            ->where('feature_name', $featureName)
            ->delete() > 0;
    }

    /**
     * Get all active features for an account.
     */
    public function getActiveFeatures(Account $account): array
    {
        return AccountFeature::where('account_id', $account->id)
            ->get()
            ->map(fn (AccountFeature $feature) => [
                'name' => $feature->feature_name,
                'description' => $feature->description,
                'metadata' => $feature->metadata,
            ])
            ->toArray();
    }

    /**
     * Build the base composite tree without decorators.
     */
    private function buildBaseComponent(Account $account): AccountComponent
    {
        $account->loadMissing('children');

        if ($account->children->isEmpty()) {
            return new AccountLeaf($account);
        }

        $composite = new AccountComposite($account);

        foreach ($account->children as $child) {
            $composite->add($this->buildBaseComponent($child));
        }

        return $composite;
    }

    /**
     * Apply feature decorators to a component based on database records.
     */
    private function applyFeatures(AccountComponent $component, Account $account): AccountComponent
    {
        $account->loadMissing('features');

        $decorated = $component;

        foreach ($account->features as $feature) {
            $decorated = $this->createDecorator($decorated, $feature);
        }

        return $decorated;
    }

    /**
     * Create a decorator instance based on the feature name and metadata.
     */
    private function createDecorator(AccountComponent $component, AccountFeature $feature): AccountComponent
    {
        return match ($feature->feature_name) {
            'overdraft' => new OverdraftFeature(
                $component,
                (float) ($feature->metadata['limit'] ?? 0)
            ),
            'premium' => new PremiumFeature(
                $component,
                (float) ($feature->metadata['interest_multiplier'] ?? 1.5),
                (float) ($feature->metadata['fee_reduction'] ?? 0.5)
            ),
            default => $component,
        };
    }

    /**
     * Get a human-readable description for a feature name.
     */
    private function getFeatureDescription(string $featureName): string
    {
        return match ($featureName) {
            'overdraft' => 'Overdraft protection allowing withdrawals beyond account balance',
            'premium' => 'Premium account with enhanced interest rates and reduced fees',
            default => 'Account feature',
        };
    }
}

