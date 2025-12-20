<?php
namespace App\Contracts;

use App\Models\Account;

interface AccountRepositoryInterface
{
    public function create(array $data): Account;
    public function findById(int $id): ?Account;
    public function findChildren(Account $account): array;
}
