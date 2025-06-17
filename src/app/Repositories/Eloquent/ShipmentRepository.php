<?php

namespace App\Repositories\Eloquent;

use App\Models\Shipment;
use App\Repositories\Interface\ShipmentRepositoryInterface;
use App\Models\User;

class ShipmentRepository extends BaseRepository implements ShipmentRepositoryInterface
{
    public function __construct(Shipment $model)
    {
        parent::__construct($model);
    }

    /**
     * Summary of getShipmentsWithFilters
     * @param mixed $filters
     * @param mixed $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getShipmentsWithFilters($filters = [], $perPage = null)
    {
        $query = Shipment::query();
        if (!empty($filters['status'])) {
            $query->ofStatus($filters['status']);
        }
        
        if (!empty($filters['departure_time']) && empty($filters['estimated_arrival_time'])) {
            $query->where('departure_time', '>=', $filters['departure_time']);
        } elseif (empty($filters['departure_time']) && !empty($filters['estimated_arrival_time'])) {
            $query->where('estimated_arrival_time', '<=', $filters['estimated_arrival_time']);
        } elseif (!empty($filters['departure_time']) && !empty($filters['estimated_arrival_time'])) {
            $query->where(function($query) use ($filters) {
                $query->where('departure_time', '>=', $filters['departure_time'])
                    ->where('departure_time', '<=', $filters['estimated_arrival_time']);
            })->orWhere(function($query) use ($filters) {
                $query->where('estimated_arrival_time', '>=', $filters['departure_time'])
                    ->where('estimated_arrival_time', '<=', $filters['estimated_arrival_time']);
            })->orWhere(function($query) use ($filters) {
                $query->where('departure_time', '<=', $filters['departure_time'])
                    ->where('estimated_arrival_time', '>=', $filters['estimated_arrival_time']);
            });
        }

        if (!empty($filters['keyword'])) {
            $query->search($filters['keyword']);
        }
        return $query->with(['driver', 'vehicle', 'goods', 'shipmentDeductions.shipmentDeductionType'])
            ->orderByDesc('created_at')
            ->paginate($perPage ?? $this->getPaginationLimit());
    }

    /**
     * Get shipments for a user in a specific month and year
     * 
     * @param User $user
     * @param int $month
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserShipments(User $user, int $month, int $year)
    {
        return Shipment::whereHas('shipmentDeductions', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereMonth('departure_time', $month)
            ->whereYear('departure_time', $year)
            ->with(['shipmentDeductions', 'shipmentDeductions.shipmentDeductionType'])
            ->orderBy('departure_time')
            ->get();
    }
    
    /**
     * Get shipments for a user between specific dates
     * 
     * @param User $user
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserShipmentsByDateRange(User $user, $startDate, $endDate)
    {
        return Shipment::whereHas('shipmentDeductions', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereBetween('departure_time', [$startDate, $endDate])
            ->with(['shipmentDeductions', 'shipmentDeductions.shipmentDeductionType'])
            ->orderBy('departure_time')
            ->get();
    }
}
