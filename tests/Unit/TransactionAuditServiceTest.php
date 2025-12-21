<?php

namespace Tests\Unit;

use App\Models\Transaction;
use App\Models\TransactionAuditLog;
use App\Services\Transaction\TransactionAuditService;
use Mockery;
use Tests\TestCase;

    /**
     * A basic unit test example.
     */

class TransactionAuditServiceTest extends TestCase
{
    /** @test */
    public function it_logs_transaction_action()
    {
        $transaction = Mockery::mock(Transaction::class);
        $transaction->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);

        $auditLogMock = Mockery::mock('alias:' . TransactionAuditLog::class);

        $auditLogMock->shouldReceive('create')
            ->once()
            ->with(Mockery::subset([
                'transaction_id' => 1,
                'action' => 'created',
            ]));

        $service = new TransactionAuditService();

        $service->log($transaction, 'created', 'test');
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
