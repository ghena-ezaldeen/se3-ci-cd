<?php

namespace Tests\Feature;

use App\Services\Transaction\TransactionAuditService;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Services\Transaction\TransactionService;
use App\Exceptions\InsufficientFundsException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TransactionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock TransactionAuditService مستقل لكل test
        $auditMock = Mockery::mock(TransactionAuditService::class);
        $auditMock->shouldReceive('log')->andReturnNull();
        $this->app->instance(TransactionAuditService::class, $auditMock);

        $this->service = app(TransactionService::class);
    }

    /** @test */
    public function it_can_transfer_money_successfully_between_two_accounts()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $acc1 = Account::factory()->create([
            'user_id' => $user->id,
            'balance' => 1000,
            'state' => 'active',
        ]);

        $acc2 = Account::factory()->create([
            'user_id' => $user->id,
            'balance' => 500,
            'state' => 'active',
        ]);

        $transaction = $this->service->transfer($acc1, $acc2, 300, 'Test transfer');

        $this->assertEquals('completed', $transaction->status);
        $this->assertEquals(700, $acc1->fresh()->balance);
        $this->assertEquals(800, $acc2->fresh()->balance);
        $this->assertDatabaseHas('transactions', [
            'amount' => 300,
            'status' => 'completed'
        ]);
    }

    /** @test */
    public function it_fails_when_balance_is_insufficient()
    {
        $user = User::factory()->create();

        $acc1 = Account::create([
            'user_id' => $user->id,
            'balance' => 100,
            'state' => 'active',
            'account_number' => 'A1',
            'type' => 'checking'
        ]);

        $acc2 = Account::create([
            'user_id' => $user->id,
            'balance' => 100,
            'state' => 'active',
            'account_number' => 'A2',
            'type' => 'checking'
        ]);

        $this->expectException(InsufficientFundsException::class);

        $this->service->transfer($acc1, $acc2, 500);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

