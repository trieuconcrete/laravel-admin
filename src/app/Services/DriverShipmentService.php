<?php

namespace App\Services;

use App\Models\Shipment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DriverShipmentService
{
    private function isShipmentMember(Shipment $shipment, int $userId): bool
    {
        return
            $shipment->driver_id === $userId ||
            $shipment->co_driver_id === $userId ||
            $shipment->shipmentDeductions()->where('user_id', $userId)->exists();
    }

    private function resolveDateRange(Request $request, string $tz): array
    {
        $start = null;
        $end = null;

        if ($request->filled('date')) {
            $base = match ($request->date) {
                'yesterday' => Carbon::yesterday($tz),
                'tomorrow'  => Carbon::tomorrow($tz),
                default     => Carbon::today($tz),
            };
            $start = $base->copy()->startOfDay();
            $end   = $base->copy()->endOfDay();
        }

        if ($request->filled('from')) {
            $start = Carbon::parse($request->from, $tz)->startOfDay();
        }

        if ($request->filled('to')) {
            $end = Carbon::parse($request->to, $tz)->endOfDay();
        }

        return [$start, $end];
    }

    private function applySorting($qb, string $sort, string $dir): void
    {
        if ($sort === 'run_date') {
            $qb->orderBy('run_date', $dir)->orderBy('departure_time', $dir);
        } elseif ($sort === 'status') {
            $qb->orderBy('status', $dir)->orderBy('departure_time', $dir);
        } else {
            $qb->orderByRaw("COALESCE(departure_time, CONCAT(run_date, ' 00:00:00')) {$dir}")
                ->orderBy('id', $dir);
        }
    }

    public function getShipments(Request $request): array
    {
        $user = $request->user();
        $tz = config('app.timezone', 'Asia/Ho_Chi_Minh');

        // Xử lý thời gian
        [$start, $end] = $this->resolveDateRange($request, $tz);

        // Chuẩn hóa các tham số
        $statuses = collect(explode(',', (string)$request->status))
            ->filter()->values()->all();

        $sort = $request->get('sort', 'departure_time');
        $dir  = $request->get('dir', 'asc');
        $per  = (int) $request->get('per_page', 20);

        // Base query
        $qb = Shipment::query()
            ->with([
                'customer:id,name',
                'vehicle:vehicle_id,plate_number,status,is_car_rental,vehicle_type_id',
                'driver:id,full_name',
                'coDriver:id,full_name',
            ])
            ->where(function ($q) use ($user) {
                $q->where('driver_id', $user->id)
                    ->orWhere('co_driver_id', $user->id)
                    ->orWhereHas(
                        'shipmentDeductions',
                        fn($sq) =>
                        $sq->where('user_id', $user->id)
                    );
            });

        // Lọc theo ngày
        if ($start && $end) {
            $qb->where(function ($q) use ($start, $end) {
                $q->whereBetween('run_date', [$start->toDateString(), $end->toDateString()])
                    ->orWhereBetween('departure_time', [$start, $end]);
            });
        }

        // Lọc theo status
        if (!empty($statuses)) {
            $qb->whereIn('status', $statuses);
        }

        // Lọc theo type
        if ($request->filled('type')) {
            $qb->shipmentType((int) $request->type);
        }

        // Tìm kiếm chung
        if ($request->filled('q')) {
            $qb->search($request->q);
        }

        // Sắp xếp
        $this->applySorting($qb, $sort, $dir);

        // Phân trang
        $paginator = $qb->paginate($per)->appends($request->query());

        // Summary (tổng quan nhanh)
        $summary = [
            'total'      => $paginator->total(),
            'in_transit' => (clone $qb)->where('status', 'in_transit')->count(),
            'completed'  => (clone $qb)->where('status', 'completed')->count(),
            'pending'    => (clone $qb)->where('status', 'pending')->count(),
        ];

        return [$paginator, $summary];
    }

    public function getShipmentDetails(Shipment $shipment, int $userId): array
    {
        if (!$this->isShipmentMember($shipment, $userId)) {
            return [null, 'Bạn không có quyền xem chuyến hàng này.'];
        }

        $shipment->load([
            'vehicle:vehicle_id,plate_number,status,is_car_rental,vehicle_type_id',
            'customer:id,name',
            'driver:id,full_name',
            'coDriver:id,full_name',
        ]);

        return [$shipment, null];
    }
}
