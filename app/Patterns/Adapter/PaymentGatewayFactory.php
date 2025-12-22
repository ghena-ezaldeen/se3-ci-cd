<?php

namespace App\PaymentGateway;

use App\Patterns\Adapter\CreditCardAdapter;
use App\Payments\CreditCardAPI;
use App\Payments\PaymentGatewayInterface;
use App\Payments\ExternalBankAPI;
use App\Patterns\Adapter\ExternalBankAdapter;

class PaymentGatewayFactory
{
    public static function make(string $type): PaymentGatewayInterface
    {
        return match ($type) {
            'credit_card' => new CreditCardAdapter(new CreditCardAPI()),
            'external_Bank' => new ExternalBankAdapter(new ExternalBankAPI()),
            default => throw new \Exception('Unsupported payment method'),
        };
    }
}
