<?php


namespace App\Services\Reports;

use App\Models\Transaction;
use App\Models\Account;
use Carbon\Carbon;

class TransactionReportService
{
    /**
     * تقرير يومي للمعاملات
     */
    public function dailyTransactions(?string $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        return Transaction::with(['fromAccount', 'toAccount', 'initiator'])
            ->whereDate('created_at', $date)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * ملخص الحسابات
     */
    public function accountSummaries(?int $accountId = null)
    {
        $accounts = $accountId
            ? Account::where('id', $accountId)->get()
            : Account::all();

        $summaries = $accounts->map(function ($account) {
            $totalDeposits = Transaction::where('to_account_id', $account->id)
                ->where('type', 'deposit')->sum('amount');

            $totalWithdrawals = Transaction::where('from_account_id', $account->id)
                ->where('type', 'withdraw')->sum('amount');

            $totalTransfersOut = Transaction::where('from_account_id', $account->id)
                ->where('type', 'transfer')->sum('amount');

            $totalTransfersIn = Transaction::where('to_account_id', $account->id)
                ->where('type', 'transfer')->sum('amount');

            return [
                'account_id' => $account->id,
                'account_name' => $account->name ?? null,
                'currency' => $account->currency,
                'current_balance' => $account->balance,
                'total_deposits' => $totalDeposits,
                'total_withdrawals' => $totalWithdrawals,
                'total_transfers_in' => $totalTransfersIn,
                'total_transfers_out' => $totalTransfersOut,
                'total_transactions' => $account->transactions()->count(),
            ];
        });

        return $summaries;
    }
}
