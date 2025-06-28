<?php

namespace App\Repositories\Eloquent;

use App\Models\Shipment;
use App\Repositories\Interface\ShipmentRepositoryInterface;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class ShipmentRepository extends BaseRepository implements ShipmentRepositoryInterface
{
    public function __construct(Shipment $model)
    {
        parent::__construct($model);
    }

    /**
     * Get shipments with filters
     *
     * @param array $filters
     * @param int|null $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getShipmentsWithFilters(array $filters = [], ?int $perPage = null): LengthAwarePaginator
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
     * @return \Illuminate\Database\Eloquent\Collection<\App\Models\Shipment>
     */
    public function getUserShipments(User $user, int $month, int $year): Collection
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
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return \Illuminate\Database\Eloquent\Collection<\App\Models\Shipment>
     */
    public function getUserShipmentsByDateRange(User $user, Carbon $startDate, Carbon $endDate): Collection
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
