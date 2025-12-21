<?php


namespace App\Providers;

use App\Models\Account;
use App\Policies\AccountPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [

            Account::class => \App\Policies\AccountPolicy::class,
            'report' => \App\Policies\ReportPolicy::class, // المفتاح يمكن أن يكون أي string

    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
