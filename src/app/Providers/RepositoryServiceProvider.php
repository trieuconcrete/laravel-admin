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
use App\Repositories\Interface\QuoteRepositoryInterface;
use App\Repositories\Eloquent\QuoteRepository;
use App\Repositories\Interface\QuoteAttachmentRepositoryInterface;
use App\Repositories\Eloquent\QuoteAttachmentRepository;
use App\Repositories\Interface\QuoteHistoryRepositoryInterface;
use App\Repositories\Eloquent\QuoteHistoryRepository;


/**
 * Summary of RepositoryServiceProvider
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Summary of register
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(DriverLicenseRepositoryInterface::class, DriverLicenseRepository::class);
        $this->app->bind(VehicleRepositoryInterface::class, VehicleRepository::class);
        $this->app->bind(PositionRepositoryInterface::class, PositionRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(VehicleDocumentRepositoryInterface::class, VehicleDocumentRepository::class);
        $this->app->bind(QuoteRepositoryInterface::class, QuoteRepository::class);
        $this->app->bind(QuoteAttachmentRepositoryInterface::class, QuoteAttachmentRepository::class);
        $this->app->bind(QuoteHistoryRepositoryInterface::class, QuoteHistoryRepository::class);
    }

    /**
     * Summary of boot
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
