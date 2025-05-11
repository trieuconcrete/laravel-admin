<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'vehicle_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'plate_number',
        'vehicle_type_id',
        'driver_id',
        'capacity',
        'manufactured_year',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'capacity' => 'float',
        'manufactured_year' => 'integer',
    ];

    /**
     * Status constants
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_INACTIVE = 'inactive';

    /**
     * Get all available statuses
     *
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE => 'Đang hoạt động',
            self::STATUS_MAINTENANCE => 'Đang bảo trì',
            self::STATUS_INACTIVE => 'Ngừng hoạt động'
        ];
    }

    /**
     * Get the vehicle type that owns the vehicle.
     */
    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }

    /**
     * Quan hệ với tài xế
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Get the documents for the vehicle.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(VehicleDocument::class, 'vehicle_id');
    }

    /**
     * Get the maintenance records for the vehicle.
     */
    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class, 'vehicle_id');
    }

    /**
     * Scope a query to only include active vehicles.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include vehicles in maintenance.
     */
    public function scopeInMaintenance($query)
    {
        return $query->where('status', self::STATUS_MAINTENANCE);
    }

    /**
     * Get status label
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        return self::getStatuses()[$this->status] ?? '';
    }

    /**
     * Get status badge class
     *
     * @return string
     */
    public function getStatusBadgeClassAttribute()
    {
        switch ($this->status) {
            case self::STATUS_ACTIVE:
                return 'success';
            case self::STATUS_MAINTENANCE:
                return 'warning';
            case self::STATUS_INACTIVE:
                return 'danger';
            default:
                return 'secondary';
        }
    }

    /**
     * Check if vehicle is available for trip
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Get the latest document of a specific type
     *
     * @param string $documentType
     * @return VehicleDocument|null
     */
    public function getLatestDocument($documentType)
    {
        return $this->documents()
            ->where('document_type', $documentType)
            ->orderBy('issue_date', 'desc')
            ->first();
    }

    /**
     * Check if a specific document type is expired
     *
     * @param string $documentType
     * @return bool
     */
    public function isDocumentExpired($documentType)
    {
        $document = $this->getLatestDocument($documentType);
        
        if (!$document || !$document->expiry_date) {
            return true;
        }

        return $document->expiry_date < now();
    }

    /**
     * Get upcoming maintenance
     *
     * @param int $days
     * @return Collection
     */
    public function getUpcomingMaintenance($days = 30)
    {
        return $this->maintenanceRecords()
            ->where('status', 'scheduled')
            ->where('start_date', '<=', now()->addDays($days))
            ->orderBy('start_date')
            ->get();
    }

    /**
     * Get total distance traveled
     *
     * @return float
     */
    public function getTotalDistanceAttribute()
    {
        return $this->trips()
            ->where('status', 'completed')
            ->sum('distance');
    }

    /**
     * Get average fuel consumption
     *
     * @return float|null
     */
    public function getAverageFuelConsumptionAttribute()
    {
        $totalFuel = $this->trips()
            ->join('trip_fuel_logs', 'trips.trip_id', '=', 'trip_fuel_logs.trip_id')
            ->where('trips.status', 'completed')
            ->sum('trip_fuel_logs.quantity');

        $totalDistance = $this->total_distance;

        if ($totalDistance > 0 && $totalFuel > 0) {
            return $totalDistance / $totalFuel;
        }

        return null;
    }
}
