<?php

namespace App\Patterns\Composite;

/*
|--------------------------------------------------------------------------
| Account Component (Composite Base)
|--------------------------------------------------------------------------
| Base abstraction for the account composite tree. It wraps an Account model
| so parent and child accounts can be treated uniformly when calculating
| balances across a hierarchy without leaking Eloquent details.
*/

use App\Models\Account;

abstract class AccountComponent
{
    protected Account $account;

    /**
     * @var array<int, AccountComponent>
     */
    protected array $children = [];

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    abstract public function getBalance(): float;

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function add(AccountComponent $component): void
    {
        $this->children[] = $component;
    }

    public function remove(AccountComponent $component): void
    {
        $this->children = array_values(array_filter(
            $this->children,
            fn (AccountComponent $candidate) => $candidate !== $component
        ));
    }

    /**
     * @return array<int, AccountComponent>
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}

