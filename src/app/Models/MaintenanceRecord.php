<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRecord extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'record_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'maintenance_type',
        'description',
        'start_date',
        'end_date',
        'cost',
        'service_provider',
        'status',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'cost' => 'decimal:2'
    ];

    /**
     * Status constants
     */
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Maintenance type constants
     */
    const TYPE_ROUTINE = 'Bảo dưỡng định kỳ';
    const TYPE_REPAIR = 'Sửa chữa';
    const TYPE_TIRE_CHANGE = 'Thay lốp xe';
    const TYPE_OIL_CHANGE = 'Thay dầu máy';
    const TYPE_BRAKE_SERVICE = 'Bảo dưỡng phanh';
    const TYPE_ENGINE_SERVICE = 'Bảo dưỡng động cơ';
    const TYPE_TRANSMISSION_SERVICE = 'Bảo dưỡng hộp số';
    const TYPE_ELECTRICAL = 'Sửa chữa điện';
    const TYPE_BODY_WORK = 'Sửa chữa thân vỏ';
    const TYPE_OTHER = 'Khác';

    /**
     * Get all available statuses
     *
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_SCHEDULED => 'Đã lên lịch',
            self::STATUS_IN_PROGRESS => 'Đang thực hiện',
            self::STATUS_COMPLETED => 'Hoàn thành',
            self::STATUS_CANCELLED => 'Đã hủy'
        ];
    }

    /**
     * Get all maintenance types
     *
     * @return array
     */
    public static function getMaintenanceTypes()
    {
        return [
            self::TYPE_ROUTINE => 'Bảo dưỡng định kỳ',
            self::TYPE_REPAIR => 'Sửa chữa',
            self::TYPE_TIRE_CHANGE => 'Thay lốp xe',
            self::TYPE_OIL_CHANGE => 'Thay dầu máy',
            self::TYPE_BRAKE_SERVICE => 'Bảo dưỡng phanh',
            self::TYPE_ENGINE_SERVICE => 'Bảo dưỡng động cơ',
            self::TYPE_TRANSMISSION_SERVICE => 'Bảo dưỡng hộp số',
            self::TYPE_ELECTRICAL => 'Sửa chữa điện',
            self::TYPE_BODY_WORK => 'Sửa chữa thân vỏ',
            self::TYPE_OTHER => 'Khác'
        ];
    }

    /**
     * Get the vehicle that owns the maintenance record.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    /**
     * Scope a query to only include scheduled records.
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    /**
     * Scope a query to only include in progress records.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope a query to only include completed records.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to only include cancelled records.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Scope a query to only include upcoming maintenance.
     */
    public function scopeUpcoming($query, $days = 30)
    {
        return $query->where('status', self::STATUS_SCHEDULED)
                    ->where('start_date', '<=', now()->addDays($days))
                    ->where('start_date', '>=', now())
                    ->orderBy('start_date');
    }

    /**
     * Scope a query to only include overdue maintenance.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED)
                    ->where('start_date', '<', now())
                    ->orderBy('start_date');
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
            case self::STATUS_SCHEDULED:
                return 'primary';
            case self::STATUS_IN_PROGRESS:
                return 'warning';
            case self::STATUS_COMPLETED:
                return 'success';
            case self::STATUS_CANCELLED:
                return 'danger';
            default:
                return 'secondary';
        }
    }

    /**
     * Get duration in days
     *
     * @return int|null
     */
    public function getDurationInDaysAttribute()
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInDays($this->end_date);
        }
        return null;
    }

    /**
     * Check if maintenance is overdue
     *
     * @return bool
     */
    public function isOverdue()
    {
        return $this->status === self::STATUS_SCHEDULED && $this->start_date < now();
    }

    /**
     * Check if maintenance is upcoming
     *
     * @param int $days
     * @return bool
     */
    public function isUpcoming($days = 30)
    {
        return $this->status === self::STATUS_SCHEDULED 
            && $this->start_date >= now() 
            && $this->start_date <= now()->addDays($days);
    }

    /**
     * Complete the maintenance record
     *
     * @param array $data
     * @return bool
     */
    public function complete($data = [])
    {
        $this->status = self::STATUS_COMPLETED;
        $this->end_date = $data['end_date'] ?? now();
        
        if (isset($data['cost'])) {
            $this->cost = $data['cost'];
        }
        
        if (isset($data['notes'])) {
            $this->notes = $data['notes'];
        }
        
        return $this->save();
    }

    /**
     * Cancel the maintenance record
     *
     * @param string|null $reason
     * @return bool
     */
    public function cancel($reason = null)
    {
        $this->status = self::STATUS_CANCELLED;
        
        if ($reason) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . "Lý do hủy: " . $reason;
        }
        
        return $this->save();
    }

    /**
     * Start the maintenance
     *
     * @return bool
     */
    public function start()
    {
        $this->status = self::STATUS_IN_PROGRESS;
        return $this->save();
    }

    /**
     * Get total cost by maintenance type for a vehicle
     *
     * @param int $vehicleId
     * @param string $maintenanceType
     * @return float
     */
    public static function getTotalCostByType($vehicleId, $maintenanceType)
    {
        return self::where('vehicle_id', $vehicleId)
                   ->where('maintenance_type', $maintenanceType)
                   ->where('status', self::STATUS_COMPLETED)
                   ->sum('cost');
    }

    /**
     * Get maintenance summary for a vehicle
     *
     * @param int $vehicleId
     * @return array
     */
    public static function getMaintenanceSummary($vehicleId)
    {
        return [
            'total_cost' => self::where('vehicle_id', $vehicleId)
                               ->where('status', self::STATUS_COMPLETED)
                               ->sum('cost'),
            'total_count' => self::where('vehicle_id', $vehicleId)
                               ->where('status', self::STATUS_COMPLETED)
                               ->count(),
            'upcoming_count' => self::where('vehicle_id', $vehicleId)
                                   ->upcoming()
                                   ->count(),
            'overdue_count' => self::where('vehicle_id', $vehicleId)
                                  ->overdue()
                                  ->count()
        ];
    }
}
