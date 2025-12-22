<?php

namespace App\Patterns\Adapter;

use App\Payments\CreditCardAPI;
use App\Payments\PaymentGatewayInterface;

class CreditCardAdapter implements PaymentGatewayInterface
{
    public function __construct(private CreditCardAPI $api) {}

    public function pay(float $amount, array $data): bool
    {
        return $this->api->chargeCard([
            'card_number' => $data['card'],
            'amount' => $amount,
        ]);
    }
}
