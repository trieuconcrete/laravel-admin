<?php

namespace App\Repositories\Interface;

interface ShipmentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Summary of getShipmentsWithFilters
     * @param mixed $filters
     * @param mixed $perPage
     * @return void
     */
    public function getShipmentsWithFilters($filters = [], $perPage = 20);

    /**
     * Get shipments for a user in a specific month and year
     * 
     * @param \App\Models\User $user
     * @param int $month
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserShipments(\App\Models\User $user, int $month, int $year);
    
    /**
     * Get shipments for a user between specific dates
     * 
     * @param \App\Models\User $user
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserShipmentsByDateRange(\App\Models\User $user, $startDate, $endDate);
}
