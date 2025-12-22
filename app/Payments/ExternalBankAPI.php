<?php

namespace App\Payments;

class ExternalBankAPI
{
    public function makeTransfer(array $payload): string
    {
        // محاكاة رد النظام الخارجي
        return 'SUCCESS';
    }
}
