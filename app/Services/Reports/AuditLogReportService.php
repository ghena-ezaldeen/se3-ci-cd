<?php


namespace App\Services\Reports;

use App\Models\TransactionAuditLog;
use Carbon\Carbon;

class AuditLogReportService
{
    /**
     * تقرير يومي لسجلات التدقيق
     */
    public function daily(?string $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        return TransactionAuditLog::with(['transaction', 'actor'])
            ->whereDate('created_at', $date)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * تقرير حسب حساب معين
     */
    public function byAccount(int $accountId)
    {
        return TransactionAuditLog::whereHas('transaction', function ($q) use ($accountId) {
            $q->where('from_account_id', $accountId)
                ->orWhere('to_account_id', $accountId);
        })
            ->with(['transaction', 'actor'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * تقرير حسب مستخدم
     */
    public function byUser(int $userId)
    {
        return TransactionAuditLog::where('performed_by', $userId)
            ->with(['transaction'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
