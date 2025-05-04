<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class VehicleType extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'vehicle_type_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * Get the vehicles for the vehicle type.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'vehicle_type_id');
    }

    /**
     * Get active vehicles count
     *
     * @return int
     */
    public function getActiveVehiclesCountAttribute()
    {
        return $this->vehicles()->active()->count();
    }

    /**
     * Get total vehicles count
     *
     * @return int
     */
    public function getTotalVehiclesCountAttribute()
    {
        return $this->vehicles()->count();
    }

    /**
     * Get vehicles in maintenance count
     *
     * @return int
     */
    public function getInMaintenanceVehiclesCountAttribute()
    {
        return $this->vehicles()->inMaintenance()->count();
    }

    /**
     * Get all vehicles by status
     *
     * @param string $status
     * @return Collection
     */
    public function getVehiclesByStatus($status)
    {
        return $this->vehicles()->where('status', $status)->get();
    }

    /**
     * Check if vehicle type has any active vehicles
     *
     * @return bool
     */
    public function hasActiveVehicles()
    {
        return $this->active_vehicles_count > 0;
    }
}
