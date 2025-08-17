<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarRental extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'status',
        'type',
        'description',
        'notes',
        'total_money',
        'file',
        'monthly_rental_fee',
        'overtime_fee_per_hour',
        'max_distance',
        'over_distance_fee_per_km',
        'invoice_number',
        'statement_number',
        'currency',
        'start_date',
        'end_date',
        'departure_point',
        'destination_point',
        'product_name',
        'contract_number'
    ];

    const OVERTIME_FEE_PER_HOUR_DEFAULT = 50000; // Default value for overtime fee per hour
    const OVER_DISTANCE_FEE_PER_KM_DEFAULT = 7000; // Default value for over distance fee per km

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_money' => 'decimal:2',
        'monthly_rental_fee' => 'decimal:2',
        'overtime_fee_per_hour' => 'decimal:2',
        'max_distance' => 'decimal:2',
        'over_distance_fee_per_km' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array<int, string>
     */
    protected $dates = ['deleted_at'];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get all available statuses
     *
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Tạo mới',
            self::STATUS_APPROVED => 'Đã duyệt',
            self::STATUS_REJECTED => 'Từ chối',
            self::STATUS_COMPLETED => 'Hoàn thành',
            self::STATUS_CANCELLED => 'Đã hủy',
        ];
    }

    /**
     * Get the customer that owns the car rental.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the car rental vehicles for the car rental.
     */
    public function carRentalVehicles(): HasMany
    {
        return $this->hasMany(CarRentalVehicle::class, 'car_rental_id');
    }

    /**
     * Get the car rental vehicle logs for the car rental.
     */
    public function carRentalVehicleLogs(): HasMany
    {
        return $this->hasMany(CarRentalVehicleLog::class, 'car_rental_id');
    }

    /**
     * Get the shipments for the car rental.
     */
    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'car_rental_id');
    }

    /**
     * Get status label attribute
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Get status color attribute for UI
     *
     * @return string
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'secondary',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'warning',
            self::STATUS_COMPLETED => 'info',
            self::STATUS_CANCELLED => 'danger',
            default => 'primary',
        };
    }

    /**
     * Scope a query to only include pending rentals.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include approved rentals.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope a query to only include completed rentals.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Calculate total money from vehicles
     *
     * @return float
     */
    public function calculateTotalMoney(): float
    {
        return $this->carRentalVehicles->sum(function ($vehicle) {
            return $vehicle->amount * $vehicle->price;
        });
    }

    /**
     * Update total money
     *
     * @return void
     */
    public function updateTotalMoney(): void
    {
        $this->update([
            'total_money' => $this->calculateTotalMoney()
        ]);
    }

    /**
     * Calculate total amount with all fees and VAT
     *
     * @param float $vatRate VAT rate (default 8%)
     * @return array Contains subtotal, vatAmount, and totalWithVat
     */
    public function calculateTotalAmountWithTax($vatRate = 0.08): array
    {
        // Lấy phí thuê xe hàng tháng
        $monthlyRentalFee = $this->monthly_rental_fee ?? 0;
        
        // Tính tổng phí làm thêm giờ từ tất cả vehicle logs
        $totalOvertimeCost = 0;
        $vehicleLogs = CarRentalVehicleLog::where('car_rental_id', $this->id)->get();
        
        foreach ($vehicleLogs as $log) {
            $totalOvertimeCost += $log->total_overtime_cost ?? 0;
        }
        
        // Tính tổng phí cầu đường
        $totalTollFees = 0;
        foreach ($vehicleLogs as $log) {
            // Sử dụng relationship để lấy toll fees
            $tollFees = $log->tollFees;
            if ($tollFees) {
                foreach ($tollFees as $tollFee) {
                    $totalTollFees += $tollFee->fee_amount ?? 0;
                }
            }
        }
        
        // Tính tổng phí bãi xe
        $totalParkingFees = $vehicleLogs->sum('parking_fee') ?? 0;
        
        // Tính tổng km đã chạy
        $totalDistance = $this->carRentalVehicleLogs()->sum('total_distance');
        
        // Tính phí vượt giới hạn km
        $overDistanceFee = 0;
        $maxDistance = $this->max_distance;
        $overDistanceFeePerKm = $this->over_distance_fee_per_km ?? self::OVER_DISTANCE_FEE_PER_KM_DEFAULT;

        // Chỉ tính phí vượt giới hạn nếu có max_distance và vượt quá
        if ($maxDistance && $maxDistance > 0 && $totalDistance > $maxDistance) {
            $overDistanceFee = ($totalDistance - $maxDistance) * $overDistanceFeePerKm;
        }
        
        // Tính subtotal (tổng trước thuế)
        $subtotal = $monthlyRentalFee + $totalOvertimeCost + $totalTollFees + $totalParkingFees + $overDistanceFee;
        
        // Tính thuế VAT
        $vatAmount = $subtotal * $vatRate;
        
        // Tính tổng cộng sau thuế
        $totalWithVat = $subtotal + $vatAmount;
        
        return [
            'monthly_rental_fee' => $monthlyRentalFee,
            'total_overtime_cost' => $totalOvertimeCost,
            'total_toll_fees' => $totalTollFees,
            'total_parking_fees' => $totalParkingFees,
            'total_distance' => $totalDistance,
            'over_distance_fee' => $overDistanceFee,
            'subtotal' => $subtotal,
            'vat_rate' => $vatRate,
            'vat_amount' => $vatAmount,
            'total_with_vat' => $totalWithVat,
        ];
    }

    /**
     * Get total overtime cost attribute
     *
     * @return float
     */
    public function getTotalOvertimeCostAttribute(): float
    {
        $calculation = $this->calculateTotalAmountWithTax();
        return $calculation['total_overtime_cost'];
    }

    /**
     * Get total toll fees attribute
     *
     * @return float
     */
    public function getTotalTollFeesAttribute(): float
    {
        $calculation = $this->calculateTotalAmountWithTax();
        return $calculation['total_toll_fees'];
    }

    /**
     * Get total parking fees attribute
     *
     * @return float
     */
    public function getTotalParkingFeesAttribute(): float
    {
        $calculation = $this->calculateTotalAmountWithTax();
        return $calculation['total_parking_fees'];
    }

    /**
     * Get over distance fee attribute
     *
     * @return float
     */
    public function getOverDistanceFeeAttribute(): float
    {
        $totalDistance = $this->carRentalVehicleLogs()->sum('total_distance');
        $maxDistance = $this->max_distance;
        $overDistanceFeePerKm = $this->over_distance_fee_per_km ?? self::OVER_DISTANCE_FEE_PER_KM_DEFAULT;

        // Chỉ tính phí vượt giới hạn nếu có max_distance và vượt quá
        if ($maxDistance && $maxDistance > 0 && $totalDistance > $maxDistance) {
            return ($totalDistance - $maxDistance) * $overDistanceFeePerKm;
        }

        return 0;
    }

    /**
     * Get total amount with VAT attribute
     *
     * @return float
     */
    public function getTotalAmountWithVatAttribute(): float
    {
        $calculation = $this->calculateTotalAmountWithTax();
        return $calculation['total_with_vat'];
    }

    /**
     * Get VAT amount attribute
     *
     * @return float
     */
    public function getVatAmountAttribute(): float
    {
        $calculation = $this->calculateTotalAmountWithTax();
        return $calculation['vat_amount'];
    }

    /**
     * Get subtotal attribute (before VAT)
     *
     * @return float
     */
    public function getSubtotalAttribute(): float
    {
        $calculation = $this->calculateTotalAmountWithTax();
        return $calculation['subtotal'];
    }

    /**
     * Get overtime fee per hour attribute
     *
     * @return float
     */
    public function getOvertimeFeePerHourUnitAttribute()    
    {
        return $this->overtime_fee_per_hour ?? self::OVERTIME_FEE_PER_HOUR_DEFAULT;
    }

    /**
     * Get total distance attribute
     *
     * @return float
     */
    public function getTotalDistanceAttribute(): float
    {
        return $this->carRentalVehicleLogs()->sum('total_distance');
    }

    /**
     * Get total overtime hours attribute
     *
     * @return float
     */
    public function getTotalOvertimeHoursAttribute(): float
    {
        return $this->carRentalVehicleLogs()->sum('overtime_hours');
    }

    /**
     * Get over distance fee per km attribute
     *
     * @return float
     */
    public function getOverDistanceFeePerKmUnitAttribute(): float
    {
        return $this->over_distance_fee_per_km ?? self::OVER_DISTANCE_FEE_PER_KM_DEFAULT;
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            1 => 'Chuyến',
            2 => 'Khoáng',
            default => 'Không xác định',
        };
    }
}