<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Patterns\State\ActiveState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountStateTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    use RefreshDatabase;

    /** @test */
    public function active_account_can_be_frozen()
    {
        $account = Account::factory()->create([
            'state' => 'active',
        ]);

        $state = new ActiveState();

        $state->freeze($account);

        $this->assertEquals('frozen', $account->fresh()->state);
    }
}
