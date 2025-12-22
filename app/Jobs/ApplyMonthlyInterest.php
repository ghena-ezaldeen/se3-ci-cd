<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\Transaction;
use App\Patterns\Strategy\InterestStrategyResolver;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ApplyMonthlyInterest implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $accounts = Account::where('status', 'active')->get();

        foreach ($accounts as $account) {
            $strategy = InterestStrategyResolver::resolve($account);

            $interest = $strategy->calculateInterest($account->balance, $account->interest_rate);

            if ($interest <= 0) continue;

            // 3️⃣ تحديث الرصيد
            $account->balance += $interest;
            $account->save();

            // 4️⃣ إنشاء معاملة (لتشغيل Observer)
            Transaction::create([
                'from_account_id' => $account->id,
                'to_account_id' => null,
                'scheduled_transaction_id' => null,
                'initiated_by' => null,
                'type' => 'interest',
                'amount' => $interest,
                'currency' => $account->currency,
                'status' => 'completed',
                'description' => 'Monthly interest applied',    
            ]);
        }
        //
    }
}
