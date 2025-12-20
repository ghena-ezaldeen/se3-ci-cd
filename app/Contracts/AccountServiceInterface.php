<?php
namespace App\Contracts;
use App\Models\Account;


interface AccountServiceInterface
{
    public function createAccount(array $data): Account;
    public function updateAccount(Account $account, array $data): Account;
    public function closeAccount(Account $account): bool;
}
