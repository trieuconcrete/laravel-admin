<?php

namespace App\Repositories\Eloquent;

use App\Models\Vehicle;
use App\Repositories\Interface\VehicleRepositoryInterface;

class VehicleRepository extends BaseRepository implements VehicleRepositoryInterface
{
    /**
     * Summary of __construct
     * @param \App\Models\Vehicle $model
     */
    public function __construct(Vehicle $model)
    {
        parent::__construct($model);
    }

    /**
     * Summary of getVehiclesWithFilters
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getVehiclesWithFilters(array $filters)
    {
        $query = Vehicle::with(['driver', 'vehicleType', 'documents'])->latest();

        /** search vehicle type */
        if (!empty($filters['vehicle_type_id'])) {
            $query->where('vehicle_type_id', $filters['vehicle_type_id']);
        }

        /** search status */
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        /** search keyword */
        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('plate_number', 'like', '%' . $keyword . '%')
                    ->orWhere('capacity', 'like', '%' . $keyword . '%')
                    ->orWhere('manufactured_year', 'like', '%' . $keyword . '%')
                    ->orWhereHas('driver', function ($q2) use ($keyword) {
                        $q2->where('full_name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        return $query->paginate($this->getPaginationLimit());
    }
}
