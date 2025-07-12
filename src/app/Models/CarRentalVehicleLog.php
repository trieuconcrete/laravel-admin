<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarRentalVehicleLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'car_rental_id',
        'shipment_id',
        'start_time',
        'end_time',
        'overtime_hours',
        'start_odometer',
        'end_odometer',
        'total_distance',
        'overtime_rate',
        'total_overtime_cost',
        'toll_fee',
        'parking_fee',
        'notes',
        'status'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'overtime_hours' => 'decimal:2',
        'start_odometer' => 'decimal:2',
        'end_odometer' => 'decimal:2',
        'total_distance' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'total_overtime_cost' => 'decimal:2',
        'toll_fee' => 'decimal:2',
        'parking_fee' => 'decimal:2',
    ];

    // Định nghĩa các trạng thái
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Lấy xe liên quan
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'vehicle_id');
    }

    /**
     * Lấy tài xế
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Lấy thông tin thuê xe
     */
    public function carRental()
    {
        return $this->belongsTo(CarRental::class, 'car_rental_id');
    }

    /**
     * Lấy thông tin chuyến hàng
     */
    public function shipment()
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }

    /**
     * Tính tổng chi phí phát sinh
     */
    public function getTotalExtraCostAttribute()
    {
        return $this->total_overtime_cost + $this->toll_fee + $this->parking_fee;
    }

    /**
     * Tính thời gian hoạt động (giờ)
     */
    public function getOperatingHoursAttribute()
    {
        return $this->start_time->diffInHours($this->end_time);
    }

    /**
     * Scope query để lọc theo trạng thái
     */
    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope query để lọc theo khoảng thời gian
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_time', [$startDate, $endDate]);
    }
} 