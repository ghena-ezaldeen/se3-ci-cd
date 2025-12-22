<?php

namespace App\Patterns\Adapter;

use App\Payments\ExternalBankAPI;
use App\Payments\PaymentGatewayInterface;

class ExternalBankAdapter implements PaymentGatewayInterface
{
    protected ExternalBankAPI $externalBank;

    public function __construct(ExternalBankAPI $externalBank)
    {
        $this->externalBank = $externalBank;
    }

    public function pay(float $amount, array $data): bool
    {
        $response = $this->externalBank->makeTransfer([
            'sum' => $amount,
            'from_account' => $data['from'],
            'to_account' => $data['to'],
        ]);

        return $response === 'SUCCESS';
    }
}
