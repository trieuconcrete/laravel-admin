<?php

namespace App\Repositories\Interface;

interface VehicleRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Summary of getVehiclesWithFilters
     * @param array $filters
     * @return void
     */
    public function getVehiclesWithFilters(array $filters);
}
