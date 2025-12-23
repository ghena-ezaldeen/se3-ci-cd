<?php

namespace Tests\Feature;

use App\Services\Transaction\TransactionAuditService;
use Mockery;
use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionAuditLog;
use App\Services\Reports\AuditLogReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AuditLogReportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuditLogReportService $service;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(AuditLogReportService::class);

        $this->adminUser = User::factory()->create();

        $this->actingAs($this->adminUser);

        // Mock TransactionAuditService
        $auditMock = Mockery::mock(TransactionAuditService::class);
        $auditMock->shouldReceive('log')->andReturnNull();
        $this->app->instance(TransactionAuditService::class, $auditMock);

        // Mock صلاحيات المستخدم
        $this->partialMock(User::class, function ($mock) {
            $mock->shouldReceive('hasPermission')->andReturn(true);
        });
    }

    /** @test */
    public function it_can_generate_daily_report()
    {
        // إنشاء حساب
        $account = Account::create([
            'user_id' => $this->adminUser->id,
            'account_number' => 'ACC-001',
            'balance' => 1000,
            'state' => 'active',
            'currency' => 'USD',
            'type' => 'checking',
        ]);

        // إنشاء Transaction
        $transaction = Transaction::create([
            'from_account_id' => $account->id,
            'to_account_id' => $account->id,
            'amount' => 100,
            'status' => 'completed',
            'description' => 'Test transaction',
            'currency' => 'USD',
            'type' => 'transfer',
        ]);

        // إنشاء AuditLog
        $auditLog = TransactionAuditLog::create([
            'transaction_id' => $transaction->id,
            'performed_by' => $this->adminUser->id,
            'action' => 'created',
            'created_at' => Carbon::today(),
        ]);

        $logs = $this->service->daily();
        $this->assertCount(1, $logs);
        $this->assertTrue($logs->contains($auditLog));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
