<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DriverLicense extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'license_number',
        'license_type',
        'issue_date',
        'expiry_date',
        'issued_by',
        'license_file',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date'
    ];

    /**
     * Status constants
     */
    const STATUS_VALID = 'valid';
    const STATUS_EXPIRED = 'expired';
    const STATUS_EXPIRING_SOON = 'expiring_soon';
    const STATUS_REVOKED = 'revoked';

    /**
     * License type constants
     */
    const TYPE_A1 = 'A1'; // Xe máy dưới 50cc
    const TYPE_A2 = 'A2'; // Xe máy dưới 175cc
    const TYPE_A3 = 'A3'; // Xe máy lớn hơn 175cc
    const TYPE_A4 = 'A4'; // Máy kéo
    const TYPE_B1 = 'B1'; // Ô tô không kinh doanh vận tải < 9 chỗ
    const TYPE_B2 = 'B2'; // Ô tô kinh doanh vận tải < 9 chỗ
    const TYPE_C = 'C';   // Ô tô tải, kể cả kinh doanh
    const TYPE_D = 'D';   // Ô tô chở người từ 10-30 chỗ
    const TYPE_E = 'E';   // Ô tô chở người > 30 chỗ
    const TYPE_F = 'F';   // Ô tô đầu kéo

    /**
     * Get all available statuses
     *
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_VALID => 'Còn hiệu lực',
            self::STATUS_EXPIRED => 'Hết hạn',
            self::STATUS_EXPIRING_SOON => 'Sắp hết hạn',
            self::STATUS_REVOKED => 'Đã thu hồi'
        ];
    }

    /**
     * Get all license types
     *
     * @return array
     */
    public static function getLicenseTypes()
    {
        return [
            self::TYPE_A1 => 'A1 - Xe máy dưới 50cc',
            self::TYPE_A2 => 'A2 - Xe máy dưới 175cc',
            self::TYPE_A3 => 'A3 - Xe máy trên 175cc',
            self::TYPE_A4 => 'A4 - Máy kéo',
            self::TYPE_B1 => 'B1 - Ô tô không kinh doanh vận tải dưới 9 chỗ',
            self::TYPE_B2 => 'B2 - Ô tô kinh doanh vận tải dưới 9 chỗ',
            self::TYPE_C => 'C - Ô tô tải, kể cả kinh doanh',
            self::TYPE_D => 'D - Ô tô chở người từ 10-30 chỗ',
            self::TYPE_E => 'E - Ô tô chở người trên 30 chỗ',
            self::TYPE_F => 'F - Ô tô đầu kéo'
        ];
    }

    /**
     * Get all Car license types
     *
     * @return array
     */
    public static function getCarLicenseTypes()
    {
        return [
            self::TYPE_A4 => 'A4 - Máy kéo',
            self::TYPE_B1 => 'B1 - Ô tô không kinh doanh vận tải dưới 9 chỗ',
            self::TYPE_B2 => 'B2 - Ô tô kinh doanh vận tải dưới 9 chỗ',
            self::TYPE_C => 'C - Ô tô tải, kể cả kinh doanh',
            self::TYPE_D => 'D - Ô tô chở người từ 10-30 chỗ',
            self::TYPE_E => 'E - Ô tô chở người trên 30 chỗ',
            self::TYPE_F => 'F - Ô tô đầu kéo'
        ];
    }

    /**
     * Get the driver that owns the license.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope a query to only include valid licenses.
     */
    public function scopeValid($query)
    {
        return $query->where('status', self::STATUS_VALID);
    }

    /**
     * Scope a query to only include expired licenses.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    /**
     * Scope a query to only include expiring soon licenses.
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>', now())
                    ->where('status', '!=', self::STATUS_EXPIRED);
    }

    /**
     * Get status label
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        return self::getStatuses()[$this->status] ?? '';
    }

    /**
     * Get license type label
     *
     * @return string
     */
    public function getLicenseTypeLabelAttribute()
    {
        return self::getLicenseTypes()[$this->license_type] ?? '';
    }

    /**
     * Get status badge class
     *
     * @return string
     */
    public function getStatusBadgeClassAttribute()
    {
        switch ($this->status) {
            case self::STATUS_VALID:
                return 'success';
            case self::STATUS_EXPIRED:
                return 'danger';
            case self::STATUS_EXPIRING_SOON:
                return 'warning';
            case self::STATUS_REVOKED:
                return 'secondary';
            default:
                return 'primary';
        }
    }

    /**
     * Get license file URL
     *
     * @return string|null
     */
    public function getLicenseFileUrlAttribute()
    {
        if ($this->license_file) {
            return Storage::url('driver_licenses/' . $this->license_file);
        }
        return null;
    }

    /**
     * Check if license is expired
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date < now();
    }

    /**
     * Check if license is expiring soon
     *
     * @param int $days
     * @return bool
     */
    public function isExpiringSoon($days = 30)
    {
        return $this->expiry_date 
            && $this->expiry_date > now() 
            && $this->expiry_date <= now()->addDays($days);
    }

    /**
     * Update license status based on expiry date
     *
     * @return bool
     */
    public function updateStatus()
    {
        if ($this->isExpired()) {
            $this->status = self::STATUS_EXPIRED;
        } elseif ($this->isExpiringSoon()) {
            $this->status = self::STATUS_EXPIRING_SOON;
        } elseif ($this->status !== self::STATUS_REVOKED) {
            $this->status = self::STATUS_VALID;
        }
        
        return $this->save();
    }

    /**
     * Revoke the license
     *
     * @param string|null $reason
     * @return bool
     */
    public function revoke($reason = null)
    {
        $this->status = self::STATUS_REVOKED;
        
        if ($reason) {
            $this->notes = ($this->notes ?? '') . "\nLý do thu hồi: " . $reason;
        }
        
        return $this->save();
    }

    /**
     * Get days until expiry
     *
     * @return int|null
     */
    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->expiry_date) {
            return null;
        }
        
        return now()->diffInDays($this->expiry_date, false);
    }

    /**
     * Delete license file when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($license) {
            if ($license->license_file) {
                Storage::delete('driver_licenses/' . $license->license_file);
            }
        });
    }

    /**
     * Check if driver has higher or equal license level
     *
     * @param string $requiredType
     * @return bool
     */
    public function meetsRequirement($requiredType)
    {
        $licenseHierarchy = [
            self::TYPE_A1 => 1,
            self::TYPE_A2 => 2,
            self::TYPE_A3 => 3,
            self::TYPE_A4 => 3,
            self::TYPE_B1 => 4,
            self::TYPE_B2 => 5,
            self::TYPE_C => 6,
            self::TYPE_D => 7,
            self::TYPE_E => 8,
            self::TYPE_F => 9
        ];

        // Kiểm tra nếu bằng lái còn hiệu lực
        if ($this->status !== self::STATUS_VALID) {
            return false;
        }

        // Kiểm tra cấp độ
        $currentLevel = $licenseHierarchy[$this->license_type] ?? 0;
        $requiredLevel = $licenseHierarchy[$requiredType] ?? 999;

        return $currentLevel >= $requiredLevel;
    }
}
