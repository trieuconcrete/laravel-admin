<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_code',
        'customer_id',
        'pickup_address',
        'delivery_address',
        'distance',
        'cargo_weight',
        'cargo_volume',
        'cargo_type',
        'cargo_description',
        'vehicle_type',
        'vehicle_quantity',
        'pickup_datetime',
        'delivery_datetime',
        'is_round_trip',
        'base_price',
        'fuel_surcharge',
        'loading_fee',
        'insurance_fee',
        'additional_fee',
        'additional_fee_description',
        'discount',
        'total_price',
        'vat_rate',
        'vat_amount',
        'final_price',
        'status',
        'valid_until',
        'notes',
        'terms_conditions',
        'created_by',
        'assigned_to',
    ];

    protected $casts = [
        'pickup_datetime' => 'datetime',
        'delivery_datetime' => 'datetime',
        'valid_until' => 'datetime',
        'is_round_trip' => 'boolean',
        'distance' => 'decimal:2',
        'cargo_weight' => 'decimal:2',
        'cargo_volume' => 'decimal:2',
        'base_price' => 'decimal:2',
        'fuel_surcharge' => 'decimal:2',
        'loading_fee' => 'decimal:2',
        'insurance_fee' => 'decimal:2',
        'additional_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'final_price' => 'decimal:2',
    ];

    /**
     * Quan hệ với khách hàng
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relationships
    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(QuoteAttachment::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(QuoteHistory::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
                    ->orWhere('valid_until', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('valid_until', '<=', now()->addDays($days))
                    ->where('valid_until', '>', now())
                    ->whereIn('status', ['sent', 'approved']);
    }

    // Accessors & Mutators
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'draft' => 'Bản nháp',
            'sent' => 'Đã gửi',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
            'expired' => 'Hết hạn',
            'converted' => 'Đã chuyển đổi',
        ];

        return $labels[$this->status] ?? 'Không xác định';
    }

    public function getStatusColorAttribute(): string
    {
        $colors = [
            'draft' => 'secondary',
            'sent' => 'primary',
            'approved' => 'success',
            'rejected' => 'danger',
            'expired' => 'warning',
            'converted' => 'info',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->valid_until < now();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->valid_until <= now()->addDays(7) && $this->valid_until > now();
    }

    public function getVehicleTypeTextAttribute(): string
    {
        $types = [
            'truck' => 'Xe tải',
            'container' => 'Container',
            'motorcycle' => 'Xe máy',
            'van' => 'Xe tải nhỏ',
        ];

        return $types[$this->vehicle_type] ?? $this->vehicle_type;
    }

    // Methods
    public function generateQuoteNumber(): string
    {
        $prefix = 'BG';
        $date = now()->format('Ymd');
        $lastQuote = static::whereDate('created_at', today())
                          ->where('quote_number', 'like', $prefix . $date . '%')
                          ->orderBy('quote_number', 'desc')
                          ->first();

        if ($lastQuote) {
            $lastNumber = intval(substr($lastQuote->quote_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $date . $newNumber;
    }

    public function calculateTotalPrice(): void
    {
        $this->total_price = $this->base_price 
                           + $this->fuel_surcharge 
                           + $this->loading_fee 
                           + $this->insurance_fee 
                           + $this->additional_fee 
                           - $this->discount;

        $this->vat_amount = $this->total_price * ($this->vat_rate / 100);
        $this->final_price = $this->total_price + $this->vat_amount;
    }

    public function addHistory(string $action, ?string $oldStatus = null, ?string $newStatus = null, ?string $description = null, ?array $changes = null, ?string $performedBy = null): void
    {
        $this->histories()->create([
            'action' => $action,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'description' => $description,
            'changes' => $changes,
            'performed_by' => $performedBy ?? auth()->user()?->name,
        ]);
    }

    public function markAsExpired(): void
    {
        if ($this->status !== 'expired') {
            $oldStatus = $this->status;
            $this->update(['status' => 'expired']);
            $this->addHistory('expired', $oldStatus, 'expired', 'Báo giá đã hết hạn');
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quote) {
            if (empty($quote->quote_number)) {
                $quote->quote_number = $quote->generateQuoteNumber();
            }
        });

        static::created(function ($quote) {
            $quote->addHistory('created', null, $quote->status, 'Báo giá được tạo');
        });

        static::updating(function ($quote) {
            if ($quote->isDirty('status')) {
                $quote->addHistory(
                    'status_changed',
                    $quote->getOriginal('status'),
                    $quote->status,
                    'Trạng thái báo giá được thay đổi'
                );
            }
        });
    }
}