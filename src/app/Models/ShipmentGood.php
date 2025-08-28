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
        'amount',
        'notes'
    ];
    protected $casts = [
        'quantity' => 'integer',
        'weight' => 'float',
        'amount' => 'decimal:2',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}
