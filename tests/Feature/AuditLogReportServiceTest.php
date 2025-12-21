<?php

namespace Tests\Feature;

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

        // إنشاء مستخدم مع صلاحية report.create
        $this->adminUser = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->adminUser = $this->getMockBuilder(\App\Models\User::class)
            ->onlyMethods(['hasPermission'])
            ->getMock();

        $this->adminUser->method('hasPermission')->willReturn(true);
        $this->adminUser->id = 1;
    }

    /** @test */
    public function it_can_generate_daily_report()
    {
        // إنشاء حساب
        $account = Account::create([
            'user_id' => $this->adminUser->id, // مهم جداً
            'account_number' => 'ACC-001',
            'balance' => 1000,
            'state' => 'active',
            'currency' => 'USD',
            'type' => 'checking', // يجب أن يكون موجود حسب جدول DB
        ]);

// إنشاء Transaction
        $transaction = Transaction::create([
            'from_account_id' => $account->id,
            'to_account_id' => $account->id,
            'amount' => 100,
            'status' => 'completed',
            'description' => 'Test transaction',
            'currency' => 'USD', // إذا كان الحقل موجود في DB
            'type' => 'transfer', // حسب ما يطلب الجدول
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
        $this->assertEquals($auditLog->id, $logs->first()->id);
    }

}
