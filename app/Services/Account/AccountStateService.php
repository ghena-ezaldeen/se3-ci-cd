<?php
namespace App\Services\Account;

use App\Models\Account;

class AccountStateService
{
        public function changeState(Account $account, string $action): void
    {
        match ($action) {
            'activate' => $account->stateObject()->activate($account),
            'freeze'   => $account->stateObject()->freeze($account),
            'suspend'  => $account->stateObject()->suspend($account),
            'close'    => $account->stateObject()->close($account),
            default    => throw new InvalidArgumentException('Invalid state action'),
        };
    }

}
