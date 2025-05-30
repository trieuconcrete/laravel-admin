<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentGood extends Model
{
    protected $fillable = [
        'shipment_id',
        'name',
        'quantity',
        'unit',
        'weight',
        'notes'
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}
