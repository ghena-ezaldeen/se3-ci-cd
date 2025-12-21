<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Patterns\State\ActiveState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class AccountStateTest extends TestCase
{
    /**
     * A basic unit test example.
        /** @test */
        public function active_account_can_be_frozen()
    {
        $account = Mockery::mock(Account::class);

        $account->shouldReceive('setAttribute')
            ->with('state', 'frozen')
            ->once()
            ->andReturnSelf();


        $account->shouldReceive('save')
            ->once()
            ->andReturnTrue();

        $state = new ActiveState();
        $state->freeze($account);
        $this->assertTrue(true);
    }

        protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();

    }



}
