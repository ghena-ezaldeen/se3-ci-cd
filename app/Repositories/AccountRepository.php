<?php
namespace App\Repositories;

use App\Contracts\AccountRepositoryInterface;
use App\Models\Account;

class AccountRepository implements AccountRepositoryInterface
{
    public function create(array $data): Account
    {
        return Account::create($data);
    }

    public function findById(int $id): ?Account
    {
        return Account::find($id);
    }

    public function findChildren(Account $account): array
    {
        return $account->children()->get()->all();
    }
}
