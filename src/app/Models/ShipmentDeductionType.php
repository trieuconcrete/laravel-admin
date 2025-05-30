<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentDeductionType extends Model
{
    use HasFactory;
    protected $table = 'shipment_deduction_types';
    protected $fillable = [
        'name', 'type', 'status'
    ];
}
