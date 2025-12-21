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
        private TransactionApprovalService $approvalService,
        private TransactionAuditService $auditService
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
            //logging
            $this->auditService->log(
                $transaction,
                'created',
                'Transaction created and pending approval'
            );

            // 4. نظام الموافقات
            if ($this->approvalService->approveTransaction($transaction)) {

                $from->decrement('balance', $amount);
                $to->increment('balance', $amount);
                $transaction->update(['status' => 'completed']);

                //logging
                $this->auditService->log(
                    $transaction,
                    'completed',
                    'Funds transferred successfully'
                );
            } else {
                $transaction->update(['status' => 'rejected']);
                //logging
                $this->auditService->log(
                    $transaction,
                    'rejected',
                    'Rejected by approval system'
                );
                throw new \Exception("العملية مرفوضة من قبل نظام الرقابة والتدقيق.");
            }

            return $transaction;
        });
    }

    public function deposit(Account $account, float $amount, string $description = 'Deposit')
    {
        return DB::transaction(function () use ($account, $amount, $description) {

            // استخدام State Pattern للتحقق من إمكانية الإيداع
            if (!$account->stateObject()->canDeposit($account)) {
                throw new AccountNotActiveException("Cannot deposit to current state: {$account->state}");
            }

            // زيادة الرصيد (لا تحتاج تحقق معقد)
            $account->increment('balance', $amount);

            // إنشاء السجل
            $transaction = Transaction::create([
                'to_account_id'   => $account->id,
                'from_account_id' => null, // لأنه إيداع خارجي
                'amount'          => $amount,
                'type'            => 'deposit',
                'status'          => 'completed',
                'initiated_by'    => Auth::id() ?? 1,
                'description'     => $description,
                'currency'        => $account->currency,
            ]);

            $this->auditService->log(
                $transaction,
                'completed',
                'Deposit completed successfully'
            );
            return $transaction;
        });
    }

    /**
     * عملية سحب (Withdraw)
     */
    public function withdraw(Account $account, float $amount, string $description = 'Withdraw')
    {
        return DB::transaction(function () use ($account, $amount, $description) {

            // 1. التحقق من الحالة
            // استخدام State Pattern للتحقق من إمكانية السحب
            if (!$account->stateObject()->canWithdraw($account)) {
                throw new AccountNotActiveException("Cannot withdraw from current state: {$account->state}");
            }

            // 2. التحقق من الرصيد (مع Decorators)
            $decoratedAccount = $this->featureService->buildDecoratedComponent($account);

            $overdraft = null;
            if ($decoratedAccount instanceof AccountDecorator) {
                $overdraft = $decoratedAccount->findDecorator(OverdraftFeature::class);
            }

            if ($overdraft) {
                if (!$overdraft->canWithdraw($amount)) {
                    throw new InsufficientFundsException();
                }
            } else {
                if ($account->balance < $amount) {
                    throw new InsufficientFundsException();
                }
            }

            // 3. الخصم
            $account->decrement('balance', $amount);

            // 4. إنشاء السجل
            $transaction= Transaction::create([
                'from_account_id' => $account->id,
                'to_account_id'   => null,
                'amount'          => $amount,
                'type'            => 'withdraw',
                'status'          => 'completed',
                'initiated_by'    => Auth::id() ?? 1,
                'description'     => $description,
                'currency'        => $account->currency,
            ]);

            $this->auditService->log(
                $transaction,
                'completed',
                'Withdraw completed successfully'
            );
            return $transaction;
        });
    }
}

