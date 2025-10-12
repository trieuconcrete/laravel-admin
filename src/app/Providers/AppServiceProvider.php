<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Repositories\Interface\SalaryAdvanceRequestRepositoryInterface;
use App\Repositories\SalaryAdvanceRequestRepository;
use App\Services\Auth\SanctumTokenService;
use App\Services\Auth\TokenServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SalaryAdvanceRequestRepositoryInterface::class, SalaryAdvanceRequestRepository::class);
        $this->app->bind(TokenServiceInterface::class, SanctumTokenService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
    }
}
