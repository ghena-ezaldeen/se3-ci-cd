<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

        protected $model = Account::class;

        public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'parent_id' => null,
            'account_number' => 'ACC-' . Str::upper(Str::random(10)),
            'type' => 'savings',
            'state' => 'active',
            'balance' => 1000,
            'currency' => 'USD',
            'interest_rate' => 1.5,
        ];
    }
}
