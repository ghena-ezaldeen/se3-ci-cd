<?php

namespace Tests\Feature;

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
        $this->service = app(TransactionService::class);
    }

    /** @test */
    public function it_can_transfer_money_successfully_between_two_accounts()
    {

        $user = User::factory()->create();
        $acc1 = Account::create([
            'user_id' => $user->id,
            'account_number' => 'ACC-1',
            'balance' => 1000,
            'state' => 'active',
            'currency' => 'USD',
            'type' => 'checking'
        ]);

        $acc2 = Account::create([
            'user_id' => $user->id,
            'account_number' => 'ACC-2',
            'balance' => 500,
            'state' => 'active',
            'currency' => 'USD',
            'type' => 'savings'
        ]);

        // 2. التنفيذ (Act)
        $this->actingAs($user);
        $transaction = $this->service->transfer($acc1, $acc2, 300, 'Test transfer');

        // 3. التحقق (Assert)
        $this->assertEquals('completed', $transaction->status);
        $this->assertEquals(700, $acc1->fresh()->balance); // 1000 - 300
        $this->assertEquals(800, $acc2->fresh()->balance); // 500 + 300
        $this->assertDatabaseHas('transactions', [
            'amount' => 300,
            'status' => 'completed'
        ]);
    }

    /** @test */
    public function it_fails_when_balance_is_insufficient()
    {
        // تحضير حساب برصيد قليل وبدون Overdraft
        $user = User::factory()->create();
        $acc1 = Account::create(['user_id' => $user->id, 'balance' => 100, 'state' => 'active', 'account_number' => 'A1', 'type' => 'checking']);
        $acc2 = Account::create(['user_id' => $user->id, 'balance' => 100, 'state' => 'active', 'account_number' => 'A2', 'type' => 'checking']);

        $this->expectException(InsufficientFundsException::class);

        $this->service->transfer($acc1, $acc2, 500); // مبلغ أكبر من الرصيد
    }
}
