<?php

namespace App\Patterns\Strategy;

use App\Patterns\Strategy\PremiumInterestStrategy;  
use App\Patterns\Strategy\StandardInterestStrategy;  
use App\Patterns\Strategy\OverdraftInterestStrategy;    


class InterestStrategyResolver
{
    public static function resolve($account)
    {
        return match ($account->type) {
            'premium' => new PremiumInterestStrategy(),
            'standard' => new StandardInterestStrategy(),
            'overdraft' => new OverdraftInterestStrategy(),
        };
    }
}