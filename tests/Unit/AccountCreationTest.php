<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountCreationTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    use RefreshDatabase;

    /** @test */
    public function it_creates_an_account_with_valid_type()
    {
        $user = User::factory()->create();

        $types = ['savings', 'current', 'loan', 'investment'];

        foreach ($types as $type) {
            $account = Account::create([
                'user_id' => $user->id,
                'account_number' => 'ACC' . rand(1000, 9999),
                'type' => $type,
                'balance' => 0,
                'currency' => 'USD',
            ]);

            $this->assertDatabaseHas('accounts', [
                'id' => $account->id,
                'type' => $type,
            ]);
        }
    }

    /** @test */
    public function it_fails_to_create_account_with_invalid_type()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $user = User::factory()->create();

        Account::create([
            'user_id' => $user->id,
            'account_number' => 'ACC' . rand(1000, 9999),
            'type' => 'invalid',
            'balance' => 0,
            'currency' => 'USD',
        ]);
    }
}
