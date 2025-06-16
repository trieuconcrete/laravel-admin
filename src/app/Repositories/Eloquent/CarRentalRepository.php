<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interface\CarRentalRepositoryInterface;
use App\Helpers\DateHelper;
use App\Models\CarRental;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Models\ShipmentDeductionType;

class CarRentalRepository extends BaseRepository implements CarRentalRepositoryInterface
{
    /**
     * Summary of __construct
     * @param \App\Models\CarRental $model
     */
    public function __construct(CarRental $model)
    {
        parent::__construct($model);
    }
}
