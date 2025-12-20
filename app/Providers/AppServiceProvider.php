<?php

namespace App\Providers;

use App\Contracts\AccountRepositoryInterface;
use App\Repositories\AccountRepository;
use App\Services\Account\AccountService;
use App\Contracts\AccountServiceInterface;
use App\Contracts\AuthServiceInterface;
use App\Contracts\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(AccountServiceInterface::class, AccountService::class);
        $this->app->bind(AccountRepositoryInterface::class,AccountRepository::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
