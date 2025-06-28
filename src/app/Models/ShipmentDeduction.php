<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentDeduction extends Model
{
    protected $fillable = [
        'shipment_id',
        'shipment_deduction_type_id',
        'user_id',
        'amount',
        'notes',
        'is_main_driver'
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function shipmentDeductionType()
    {
        return $this->belongsTo(ShipmentDeductionType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
