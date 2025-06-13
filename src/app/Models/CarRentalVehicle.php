<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CarRentalVehicle extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'car_rental_vehicles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'car_rental_id',
        'vehicle_id',
        'product_name',
        'unit',
        'amount',
        'price',
        'start_date',
        'end_date',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'integer',
        'price' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array<int, string>
     */
    protected $dates = ['start_date', 'end_date', 'deleted_at'];

    /**
     * Unit constants
     */
    const UNIT_MONTH = 'tháng';
    const UNIT_DAY = 'ngày';
    const UNIT_KM = 'km';
    const UNIT_TRIP = 'chuyến';

    /**
     * Get all available units
     *
     * @return array
     */
    public static function getUnits(): array
    {
        return [
            self::UNIT_MONTH => 'Tháng',
            self::UNIT_DAY => 'Ngày',
            self::UNIT_KM => 'Km',
            self::UNIT_TRIP => 'Chuyến',
        ];
    }

    /**
     * Get the car rental that owns the vehicle.
     */
    public function carRental(): BelongsTo
    {
        return $this->belongsTo(CarRental::class, 'car_rental_id');
    }

    /**
     * Get the vehicle.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    /**
     * Get total price attribute
     *
     * @return float
     */
    public function getTotalPriceAttribute(): float
    {
        return $this->amount * $this->price;
    }

    /**
     * Get rental days attribute
     *
     * @return int
     */
    public function getRentalDaysAttribute(): int
    {
        if ($this->start_date && $this->end_date) {
            return Carbon::parse($this->start_date)->diffInDays(Carbon::parse($this->end_date)) + 1;
        }
        return 0;
    }

    /**
     * Check if rental is active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        $now = Carbon::now();
        return $now->between($this->start_date, $this->end_date);
    }

    /**
     * Check if rental is upcoming
     *
     * @return bool
     */
    public function isUpcoming(): bool
    {
        return Carbon::now()->lt($this->start_date);
    }

    /**
     * Check if rental is past
     *
     * @return bool
     */
    public function isPast(): bool
    {
        return Carbon::now()->gt($this->end_date);
    }

    /**
     * Scope a query to only include active rentals.
     */
    public function scopeActive($query)
    {
        $now = Carbon::now()->toDateString();
        return $query->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
    }

    /**
     * Scope a query to only include upcoming rentals.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', Carbon::now()->toDateString());
    }

    /**
     * Scope a query to only include past rentals.
     */
    public function scopePast($query)
    {
        return $query->where('end_date', '<', Carbon::now()->toDateString());
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<=', $startDate)
                     ->where('end_date', '>=', $endDate);
              });
        });
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Update car rental total money when creating
        static::created(function ($carRentalVehicle) {
            $carRentalVehicle->carRental->updateTotalMoney();
        });

        // Update car rental total money when updating
        static::updated(function ($carRentalVehicle) {
            $carRentalVehicle->carRental->updateTotalMoney();
        });

        // Update car rental total money when deleting
        static::deleted(function ($carRentalVehicle) {
            $carRentalVehicle->carRental->updateTotalMoney();
        });
    }
}