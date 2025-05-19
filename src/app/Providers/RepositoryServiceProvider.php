<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interface\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Interface\DriverLicenseRepositoryInterface;
use App\Repositories\Eloquent\DriverLicenseRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(DriverLicenseRepositoryInterface::class, DriverLicenseRepository::class);
    }

    public function boot()
    {
        //
    }
}
