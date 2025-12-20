<?php

namespace App\Services\Transaction;

use App\Models\Account;
use App\Models\Transaction;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\AccountNotActiveException;
use App\Services\Account\AccountFeatureService;
use App\Patterns\Decorator\OverdraftFeature;
use App\Patterns\Decorator\AccountDecorator; // تأكد من استيراد هذا الكلاس
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionService
{
    public function __construct(
        private AccountFeatureService $featureService,
        private TransactionApprovalService $approvalService
    ) {}

    public function transfer(Account $from, Account $to, float $amount, string $description = '')
    {
        return DB::transaction(function () use ($from, $to, $amount, $description) {

            // 1. التحقق من حالة الحساب
            if ($from->state !== 'active') {
                throw new AccountNotActiveException();
            }

            // 2. التحقق من الرصيد باستخدام الـ Decorator Pattern
            $decoratedAccount = $this->featureService->buildDecoratedComponent($from);

            $overdraft = null;
            // فحص: هل الكائن من نوع Decorator؟ إذا نعم، يمكننا البحث عن ميزة
            if ($decoratedAccount instanceof AccountDecorator) {
                $overdraft = $decoratedAccount->findDecorator(OverdraftFeature::class);
            }

            if ($overdraft) {
                if (!$overdraft->canWithdraw($amount)) {
                    throw new InsufficientFundsException();
                }
            } else {
                // حساب عادي أو Leaf لا يحتوي على Decorators
                if ($from->balance < $amount) {
                    throw new InsufficientFundsException();
                }
            }

            // 3. إنشاء سجل المعاملة
            $transaction = Transaction::create([
                'from_account_id' => $from->id,
                'to_account_id'   => $to->id,
                'amount'          => $amount,
                'type'            => 'transfer',
                'status'          => 'pending',
                'initiated_by'    => Auth::id() ?? 1,
                'description'     => $description,
                'currency'        => $from->currency,
            ]);

            // 4. نظام الموافقات
            if ($this->approvalService->approveTransaction($transaction)) {
                $from->decrement('balance', $amount);
                $to->increment('balance', $amount);
                $transaction->update(['status' => 'completed']);
            } else {
                $transaction->update(['status' => 'rejected']);
                throw new \Exception("العملية مرفوضة من قبل نظام الرقابة والتدقيق.");
            }

            return $transaction;
        });
    }
}
