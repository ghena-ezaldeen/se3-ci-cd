<?php

namespace App\Patterns\Composite;

/*
|--------------------------------------------------------------------------
| Account Leaf
|--------------------------------------------------------------------------
| Represents a single account with no children in the composite tree.
| Exposes balance calculation for stand-alone accounts while rejecting
| attempts to add nested items, keeping the tree structurally valid.
*/

use InvalidArgumentException;

class AccountLeaf extends AccountComponent
{
    public function getBalance(): float
    {
        return (float) $this->account->balance;
    }

    public function add(AccountComponent $component): void
    {
        throw new InvalidArgumentException('Cannot add children to an account leaf.');
    }

    public function remove(AccountComponent $component): void
    {
        throw new InvalidArgumentException('Cannot remove children from an account leaf.');
    }

    public function getChildren(): array
    {
        return [];
    }
}

