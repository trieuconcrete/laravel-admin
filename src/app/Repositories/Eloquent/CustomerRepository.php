<?php

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Models\Shipment;
use App\Models\ShipmentDeduction;
use App\Repositories\Interface\CustomerRepositoryInterface;
use App\Helpers\DateHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Models\ShipmentDeductionType;

class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    /**
     * Summary of __construct
     * @param \App\Models\Customer $model
     */
    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }

    /**
     * Summary of getCustomerWithFilters
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getCustomerWithFilters(array $filters): LengthAwarePaginator
    {
        $query = $this->model->with([])->latest();

        /** search vehicle type */
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        /** search status */
        if (!empty($filters['is_active'])) {
            $isActive = $filters['is_active'] == Customer::STATUS_ACTIVE ? true : false;
            $query->where('is_active', $isActive);
        }

        /** search keyword */
        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('customer_code', 'like', '%' . $keyword . '%')
                    ->orWhere('name', 'like', '%' . $keyword . '%')
                    ->orWhere('manufacemailtured_year', 'like', '%' . $keyword . '%');
            });
        }

        return $query->paginate($this->getPaginationLimit());
    }    
    
    /**
     * Get monthly shipments for a customer
     * @param int $customerId
     * @param string $month Format: 'YYYY-MM'
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMonthlyShipments(int $customerId, string $month)
    {
        // Parse the month string to get start and end dates
        $startDate = $month . '-01 00:00:00';
        $endDate = date('Y-m-t 23:59:59', strtotime($startDate));
        
        return Shipment::where('customer_id', $customerId)
            ->whereBetween('departure_time', [$startDate, $endDate])
            ->with(['shipmentDeductions' => function ($query) {
                $query->with(['shipmentDeductionType' => function ($query) {
                    $query->where('type', ShipmentDeductionType::TYPE_EXPENSE)
                        ->where('status', 'active');
                }]);
            }, 'vehicle'])
            ->get();
    }
}
