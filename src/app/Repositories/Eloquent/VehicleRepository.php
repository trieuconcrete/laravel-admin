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
}
