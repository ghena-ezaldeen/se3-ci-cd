<?php
namespace App\Services\Account;

use App\Contracts\AccountRepositoryInterface;
use App\Contracts\AccountServiceInterface;
use App\Models\Account;
use App\Services\Account\AccountBalanceService;
use Illuminate\Support\Str;

class AccountService implements AccountServiceInterface
{
    protected AccountRepositoryInterface $accountRepo;


    public function __construct(AccountRepositoryInterface $accountRepo) {
        $this->accountRepo = $accountRepo;
    }

    public function createAccount(array $data): Account
    {
        $data['account_number'] = $this->generateAccountNumber();
        return $this->accountRepo->create($data);
    }

    public function updateAccount(Account $account, array $data): Account
    {
        $account->update($data);
        return $account;
    }

    public function closeAccount(Account $account): bool
    {
        return $account->update(['state' => 'closed']);
    }

    private function generateAccountNumber(): string
    {
        return sprintf('ACC-%s', Str::upper(Str::random(10)));
    }

}
