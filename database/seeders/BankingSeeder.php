<?php

namespace Database\Seeders;

/*
|--------------------------------------------------------------------------
| Banking Seeder
|--------------------------------------------------------------------------
| Seeds foundational data for the Advanced Banking System: roles, demo
| users, sample accounts (including hierarchy), a decorated feature,
| a scheduled transfer, and a transaction needing manager approval.
| This gives the next modules realistic fixtures to build upon.
*/

use App\Models\Account;
use App\Models\AccountFeature;
use App\Models\PaymentLog;
use App\Models\Role;
use App\Models\ScheduledTransaction;
use App\Models\Transaction;
use App\Models\TransactionAuditLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BankingSeeder extends Seeder
{
    public function run(): void
    {
        $roles = collect([
            ['name' => 'customer', 'description' => 'Retail customer'],
            ['name' => 'teller', 'description' => 'Frontline teller'],
            ['name' => 'manager', 'description' => 'Branch manager'],
            ['name' => 'admin', 'description' => 'System administrator'],
        ])->mapWithKeys(function ($role) {
            $record = Role::firstOrCreate(['name' => $role['name']], $role);

            return [$role['name'] => $record->id];
        });

        $customer = $this->createUser('Customer One', 'customer@example.com', 'customer', $roles);
        $teller = $this->createUser('Teller One', 'teller@example.com', 'teller', $roles);
        $manager = $this->createUser('Manager One', 'manager@example.com', 'manager', $roles);
        $admin = $this->createUser('Admin One', 'admin@example.com', 'admin', $roles);

        $parentAccount = Account::create([
            'user_id' => $customer->id,
            'account_number' => $this->accountNumber('PRNT'),
            'type' => 'savings',
            'state' => 'active',
            'balance' => 25000,
            'currency' => 'USD',
            'interest_rate' => 1.5,
        ]);

        $childAccount = Account::create([
            'user_id' => $customer->id,
            'parent_id' => $parentAccount->id,
            'account_number' => $this->accountNumber('CHLD'),
            'type' => 'investment',
            'state' => 'active',
            'balance' => 12000,
            'currency' => 'USD',
            'interest_rate' => 3.2,
        ]);

        AccountFeature::create([
            'account_id' => $childAccount->id,
            'feature_name' => 'Overdraft Protection Decorator',
            'description' => 'Wraps the account with overdraft protection rules.',
            'metadata' => [
                'limit' => 500,
                'fee' => 5,
            ],
        ]);

        $scheduled = ScheduledTransaction::create([
            'from_account_id' => $parentAccount->id,
            'to_account_id' => $childAccount->id,
            'type' => 'transfer',
            'amount' => 250,
            'currency' => 'USD',
            'scheduled_for' => now()->addDay(),
            'frequency' => 'monthly',
            'status' => 'scheduled',
        ]);

        $needsApproval = Transaction::create([
            'from_account_id' => $parentAccount->id,
            'to_account_id' => $childAccount->id,
            'scheduled_transaction_id' => $scheduled->id,
            'initiated_by' => $teller->id,
            'type' => 'transfer',
            'amount' => 7500,
            'currency' => 'USD',
            'status' => 'pending_approval',
            'description' => 'Transfer exceeding teller limit; manager review required.',
            'approval_required' => true,
        ]);

        TransactionAuditLog::create([
            'transaction_id' => $needsApproval->id,
            'performed_by' => $teller->id,
            'action' => 'submitted',
            'notes' => 'Teller submitted transfer for manager approval.',
        ]);

        PaymentLog::create([
            'transaction_id' => $needsApproval->id,
            'gateway' => 'internal-ledger',
            'reference' => $this->accountNumber('REF'),
            'amount' => $needsApproval->amount,
            'currency' => 'USD',
            'status' => 'pending',
            'payload' => [
                'info' => 'Pending manager approval before posting.',
            ],
        ]);
    }

    private function createUser(string $name, string $email, string $roleKey, Collection $roles): User
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('password'),
                'phone' => '555-0100',
                'status' => 'active',
            ]
        );

        $user->roles()->sync([$roles[$roleKey]]);

        return $user;
    }

    private function accountNumber(string $prefix): string
    {
        return sprintf('%s-%s', $prefix, Str::upper(Str::random(8)));
    }
}

