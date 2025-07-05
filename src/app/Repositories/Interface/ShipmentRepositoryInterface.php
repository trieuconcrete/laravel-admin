<?php

namespace App\Repositories\Interface;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ShipmentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get shipments with filters
     *
     * @param array $filters
     * @param int|null $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getShipmentsWithFilters(array $filters = [], ?int $perPage = 20): LengthAwarePaginator;

    /**
     * Get shipments for a user in a specific month and year
     * 
     * @param User $user
     * @param int $month
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Collection<\App\Models\Shipment>
     */
    public function getUserShipments(User $user, int $month, int $year): Collection;
    
    /**
     * Get shipments for a user between specific dates
     * 
     * @param User $user
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return \Illuminate\Database\Eloquent\Collection<\App\Models\Shipment>
     */
    public function getUserShipmentsByDateRange(User $user, Carbon $startDate, Carbon $endDate): Collection;
}
