<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentDeductionType extends Model
{
    use HasFactory;
    protected $table = 'shipment_deduction_types';

    const TYPE_DRIVER_AND_BUSBOY = 'driver_and_busboy';
    const TYPE_EXPENSE = 'expense';
    
    protected $fillable = [
        'name', 'type', 'status'
    ];
}
