<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_id',
        'locale',
        'title',
        'description'
    ];

    /**
     * Get the service that owns this translation.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
