<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'action',
        'old_status',
        'new_status',
        'description',
        'changes',
        'performed_by',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    // Relationships
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    // Accessors
    public function getActionLabelAttribute(): string
    {
        $labels = [
            'created' => 'Tạo mới',
            'updated' => 'Cập nhật',
            'sent' => 'Gửi báo giá',
            'approved' => 'Phê duyệt',
            'rejected' => 'Từ chối',
            'expired' => 'Hết hạn',
            'converted' => 'Chuyển đổi',
            'status_changed' => 'Thay đổi trạng thái',
            'price_updated' => 'Cập nhật giá',
            'attachment_added' => 'Thêm tệp đính kèm',
            'attachment_removed' => 'Xóa tệp đính kèm',
        ];

        return $labels[$this->action] ?? $this->action;
    }

    public function getActionIconAttribute(): string
    {
        $icons = [
            'created' => 'fas fa-plus-circle text-success',
            'updated' => 'fas fa-edit text-primary',
            'sent' => 'fas fa-paper-plane text-info',
            'approved' => 'fas fa-check-circle text-success',
            'rejected' => 'fas fa-times-circle text-danger',
            'expired' => 'fas fa-clock text-warning',
            'converted' => 'fas fa-exchange-alt text-info',
            'status_changed' => 'fas fa-sync text-primary',
            'price_updated' => 'fas fa-dollar-sign text-success',
            'attachment_added' => 'fas fa-paperclip text-info',
            'attachment_removed' => 'fas fa-trash text-danger',
        ];

        return $icons[$this->action] ?? 'fas fa-info-circle text-muted';
    }

    public function getStatusLabelAttribute(?string $status): ?string
    {
        if (!$status) return null;

        $labels = [
            'draft' => 'Bản nháp',
            'sent' => 'Đã gửi',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
            'expired' => 'Hết hạn',
            'converted' => 'Đã chuyển đổi',
        ];

        return $labels[$status] ?? $status;
    }
}