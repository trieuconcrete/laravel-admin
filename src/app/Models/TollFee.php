<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TollFee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_log_id',
        'shipment_id',
        'station_name',
        'transaction_code',
        'fee_amount',
        'notes'
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
    ];

    /**
     * Lấy thông tin nhật ký xe (backward compatibility)
     */
    public function vehicleLog()
    {
        return $this->belongsTo(CarRentalVehicleLog::class, 'vehicle_log_id');
    }

    /**
     * Lấy thông tin chuyến hàng
     */
    public function shipment()
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }

    /**
     * Scope để lấy toll fees theo shipment
     */
    public function scopeForShipment($query, $shipmentId)
    {
        return $query->where('shipment_id', $shipmentId);
    }

    /**
     * Scope để lấy toll fees theo vehicle log (backward compatibility)
     */
    public function scopeForVehicleLog($query, $vehicleLogId)
    {
        return $query->where('vehicle_log_id', $vehicleLogId);
    }
} 