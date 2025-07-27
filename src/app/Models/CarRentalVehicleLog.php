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
        'run_date',
        'start_location',
        'end_location',
        'overtime_hours',
        'start_odometer',
        'end_odometer',
        'total_distance',
        'overtime_rate',
        'total_overtime_cost',
        'parking_fee',
        'notes',
        'status',
        'overtime_fee_per_hour',
        'max_distance',
        'over_distance_fee_per_km',
    ];

    protected $casts = [
        'start_time' => 'string', // time
        'end_time' => 'string',   // time
        'run_date' => 'date',
        'overtime_hours' => 'decimal:2',
        'start_odometer' => 'decimal:2',
        'end_odometer' => 'decimal:2',
        'total_distance' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'total_overtime_cost' => 'decimal:2',
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
     * Lấy danh sách phí cầu đường
     */
    public function tollFees()
    {
        return $this->hasMany(TollFee::class, 'vehicle_log_id');
    }

    /**
     * Tính tổng chi phí phát sinh
     */
    public function getTotalExtraCostAttribute()
    {
        return $this->total_overtime_cost + $this->total_toll_fee + $this->parking_fee;
    }

    /**
     * Tính tổng phí cầu đường
     */
    public function getTotalTollFeeAttribute()
    {
        return $this->tollFees()->sum('fee_amount');
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