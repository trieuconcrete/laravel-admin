<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class VehicleDocument extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'document_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'document_type',
        'issue_date',
        'expiry_date',
        'document_number',
        'document_file',
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
     * Document type constants
     */
    const TYPE_REGISTRATION = 'registration';  // Đăng ký xe
    const TYPE_INSPECTION = 'inspection';      // Đăng kiểm
    const TYPE_INSURANCE = 'insurance';        // Bảo hiểm
    const TYPE_PERMIT = 'permit';              // Giấy phép vận tải
    const TYPE_ROAD_TAX = 'road_tax';          // Phí đường bộ
    const TYPE_EMISSION = 'emission';          // Kiểm tra khí thải
    const TYPE_OWNERSHIP = 'ownership';        // Giấy tờ sở hữu
    const TYPE_OTHER = 'other';                // Khác

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
     * Get all document types
     *
     * @return array
     */
    public static function getDocumentTypes()
    {
        return [
            self::TYPE_REGISTRATION => 'Đăng ký xe',
            self::TYPE_INSPECTION => 'Đăng kiểm',
            self::TYPE_INSURANCE => 'Bảo hiểm',
            self::TYPE_PERMIT => 'Giấy phép vận tải',
            self::TYPE_ROAD_TAX => 'Phí đường bộ',
            self::TYPE_EMISSION => 'Kiểm tra khí thải',
            self::TYPE_OWNERSHIP => 'Giấy tờ sở hữu',
            self::TYPE_OTHER => 'Khác'
        ];
    }

    /**
     * Get the vehicle that owns the document.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    /**
     * Scope a query to only include valid documents.
     */
    public function scopeValid($query)
    {
        return $query->where('status', self::STATUS_VALID);
    }

    /**
     * Scope a query to only include expired documents.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    /**
     * Scope a query to only include expiring soon documents.
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>', now())
                    ->where('status', '!=', self::STATUS_EXPIRED);
    }

    /**
     * Scope a query to filter by document type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('document_type', $type);
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
     * Get document type label
     *
     * @return string
     */
    public function getDocumentTypeLabelAttribute()
    {
        return self::getDocumentTypes()[$this->document_type] ?? '';
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
     * Get document file URL
     *
     * @return string|null
     */
    public function getDocumentFileUrlAttribute()
    {
        if ($this->document_file) {
            return Storage::url('/' . $this->document_file);
        }
        return null;
    }

    /**
     * Check if document is expired
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date < now();
    }

    /**
     * Check if document is expiring soon
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
     * Update document status based on expiry date
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
     * Revoke the document
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
     * Delete document file when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($document) {
            if ($document->document_file) {
                Storage::delete('vehicle_documents/' . $document->document_file);
            }
        });
    }

    /**
     * Get all documents expiring soon for a vehicle
     *
     * @param int $vehicleId
     * @param int $days
     * @return Collection
     */
    public static function getExpiringSoonForVehicle($vehicleId, $days = 30)
    {
        return self::where('vehicle_id', $vehicleId)
                   ->expiringSoon($days)
                   ->get();
    }

    /**
     * Get document summary for a vehicle
     *
     * @param int $vehicleId
     * @return array
     */
    public static function getDocumentSummary($vehicleId)
    {
        return [
            'total' => self::where('vehicle_id', $vehicleId)->count(),
            'valid' => self::where('vehicle_id', $vehicleId)
                          ->where('status', self::STATUS_VALID)
                          ->count(),
            'expired' => self::where('vehicle_id', $vehicleId)
                           ->where('status', self::STATUS_EXPIRED)
                           ->count(),
            'expiring_soon' => self::where('vehicle_id', $vehicleId)
                                  ->where('status', self::STATUS_EXPIRING_SOON)
                                  ->count()
        ];
    }
}
