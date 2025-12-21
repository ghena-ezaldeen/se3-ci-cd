<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuditLogReportRequest;
use App\Services\Reports\AuditLogReportService;
use App\Services\Reports\TransactionReportService;
use App\Models\Account;
use Illuminate\Http\Request;

class TransactionReportController extends Controller
{
    public function __construct(
        private AuditLogReportService $auditReport,
        private TransactionReportService $transactionReport
    ) {}

    public function auditLogs(AuditLogReportRequest $request)
    {
        $this->authorize('create', 'report');

        $type = $request->type;

        $logs = match ($type) {
            'daily'      => $this->auditReport->daily($request->date),
            'by_user'    => $this->auditReport->byUser($request->user_id),
            'by_account' => $this->auditReport->byAccount($request->account_id),
        };

        return response()->json([
            'report_type' => $type,
            'count'       => $logs->count(),
            'data'        => $logs,
        ]);
    }

    public function transactions(Request $request)
    {
        $this->authorize('create', 'report');

        $type = $request->type;

        $data = match ($type) {
            'daily_transactions' => $this->transactionReport->dailyTransactions($request->date),
            'account_summaries' => $this->transactionReport->accountSummaries($request->account_id ?? null),
        };

        return response()->json([
            'report_type' => $type,
            'count'       => is_iterable($data) ? count($data) : 0,
            'data'        => $data,
        ]);
    }


}
