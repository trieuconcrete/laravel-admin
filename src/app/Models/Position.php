<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use HasFactory;

    /**
     * Position constants
     */
    const POSITION_GD = 'GD';
    const POSITION_TP = 'TP';
    const POSITION_KT = 'KT';
    const POSITION_TX = 'TX';
    const POSITION_NV = 'NV';
    const POSITION_DP = 'DP';
    const POSITION_NK = 'NK';
    const POSITION_KTV = 'KTV';
    const POSITION_CSKH = 'CSKH';
    const POSITION_PX = 'PX';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'salary_min',
        'salary_max',
        'department',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the users for the position.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'position_id');
    }

    /**
     * Get next employee code
     *
     * @return string
     */
    public function getNextEmployeeCode(): string
    {
        // Get the last employee with this position code
        $lastEmployee = User::where('employee_code', 'like', $this->code . '%')
                            ->orderBy('employee_code', 'desc')
                            ->first();

        if (!$lastEmployee) {
            // No employee with this code, start from 001
            return $this->code . '001';
        }

        // Extract the numeric part
        $numericPart = (int) substr($lastEmployee->employee_code, strlen($this->code));
        
        // Increment and format with leading zeros
        return $this->code . str_pad($numericPart + 1, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Scope a query to only include active positions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
