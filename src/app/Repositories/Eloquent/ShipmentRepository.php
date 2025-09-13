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
        if (!empty($filters['shipment_type'])) {
            $query->shipmentType($filters['shipment_type']);
        }
        $departureTime = $filters['departure_time'] ?? null;
        $arrivalTime = $filters['estimated_arrival_time'] ?? null;
        $query->when($departureTime, function($q) use ($departureTime) {
            $q->where('departure_time', '>=', $departureTime);
        });
        $query->when($arrivalTime, function($q) use ($arrivalTime) {
            $q->where('estimated_arrival_time', '<=', $arrivalTime);
        });

        if (!empty($filters['keyword'])) {
            $query->search($filters['keyword']);
        }
        $query->when(!empty($filters['customer_id']), function($q) use ($filters) {
            $q->where('customer_id', $filters['customer_id']);
        });
        return $query->with(['driver', 'vehicle', 'goods', 'shipmentDeductions.shipmentDeductionType'])
            ->orderByDesc('departure_time')
            ->orderByDesc('updated_at')
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
     * Get completed shipments for a user in a specific month and year
     * 
     * @param User $user
     * @param int $month
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Collection<\App\Models\Shipment>
     */
    public function getUserCompletedShipments(User $user, int $month, int $year): Collection
    {
        return Shipment::whereHas('shipmentDeductions', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereMonth('departure_time', $month)
            ->whereYear('departure_time', $year)
            ->completed() // Chỉ lấy shipment đã hoàn thành
            ->with(['shipmentDeductions', 'shipmentDeductions.shipmentDeductionType'])
            ->orderBy('departure_time')
            ->get();
    }

    /**
     * Get completed shipments in month for a user in a specific month and year
     * 
     * @param User $user
     * @param int $month
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Collection<\App\Models\Shipment>
     */
    public function getUserShipmentsInMonth(User $user, int $month, int $year): Collection
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
    public function getUserShipmentsByDateRange(User $user, Carbon $startDate, Carbon $endDate, $isCompleted = false): Collection
    {
        return Shipment::whereHas('shipmentDeductions', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereBetween('departure_time', [$startDate, $endDate])
            ->when($isCompleted, function($q) {
                $q->completed(); // Chỉ lấy shipment đã hoàn thành nếu $isCompleted là true
            })
            ->with(['shipmentDeductions', 'shipmentDeductions.shipmentDeductionType'])
            ->orderBy('departure_time')
            ->get();
    }
}
