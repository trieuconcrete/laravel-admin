<?php

namespace App\Repositories\Eloquent;

use App\Models\Shipment;
use App\Repositories\Interface\ShipmentRepositoryInterface;

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
            ->orderByDesc('departure_time')
            ->paginate($perPage ?? $this->getPaginationLimit());
    }
}
