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
        'station_name',
        'transaction_code',
        'fee_amount',
        'notes'
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
    ];

    /**
     * Lấy thông tin nhật ký xe
     */
    public function vehicleLog()
    {
        return $this->belongsTo(CarRentalVehicleLog::class, 'vehicle_log_id');
    }
} 