<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentDeductionType extends Model
{
    use HasFactory;
    protected $table = 'shipment_deduction_types';

    const TYPE_DRIVER = 'driver';
    const TYPE_EXPENSE = 'expense';
    const TYPE_BUS_DRIVER = 'bus_driver';
    
    protected $fillable = [
        'name', 
        'type', 
        'status',
        'is_main_driver'
    ];
}
