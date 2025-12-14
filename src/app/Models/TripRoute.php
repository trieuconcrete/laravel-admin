<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripRoute extends Model
{
    protected $table = 'trip_routes';

    protected $fillable = [
        'origin_name',
        'destination_name',
        'tons',
        'price',
    ];

    protected $casts = [
        'price' => 'float',
        'tons' => 'float',
    ];
}
