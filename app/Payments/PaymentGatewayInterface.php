<?php

namespace App\Payments;

interface PaymentGatewayInterface
{
    public function pay(float $amount, array $data): bool;
}