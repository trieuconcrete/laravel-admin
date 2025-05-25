<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interface\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Interface\DriverLicenseRepositoryInterface;
use App\Repositories\Eloquent\DriverLicenseRepository;
use App\Repositories\Interface\VehicleRepositoryInterface;
use App\Repositories\Eloquent\VehicleRepository;
use App\Repositories\Interface\PositionRepositoryInterface;
use App\Repositories\Eloquent\PositionRepository;
use App\Repositories\Interface\CustomerRepositoryInterface;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Interface\VehicleDocumentRepositoryInterface;
use App\Repositories\Eloquent\VehicleDocumentRepository;


/**
 * Summary of RepositoryServiceProvider
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Summary of register
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(DriverLicenseRepositoryInterface::class, DriverLicenseRepository::class);
        $this->app->bind(VehicleRepositoryInterface::class, VehicleRepository::class);
        $this->app->bind(PositionRepositoryInterface::class, PositionRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(VehicleDocumentRepositoryInterface::class, VehicleDocumentRepository::class);
    }

    /**
     * Summary of boot
     * @return void
     */
    public function boot()
    {
        //
    }
}
