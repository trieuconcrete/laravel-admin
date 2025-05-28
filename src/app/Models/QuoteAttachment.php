<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class QuoteAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    // Relationships
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    // Accessors
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('quotes.attachments.download', $this->id);
    }

    public function getFileIconAttribute(): string
    {
        $extension = pathinfo($this->file_name, PATHINFO_EXTENSION);
        
        $icons = [
            'pdf' => 'fas fa-file-pdf text-danger',
            'doc' => 'fas fa-file-word text-primary',
            'docx' => 'fas fa-file-word text-primary',
            'xls' => 'fas fa-file-excel text-success',
            'xlsx' => 'fas fa-file-excel text-success',
            'jpg' => 'fas fa-file-image text-warning',
            'jpeg' => 'fas fa-file-image text-warning',
            'png' => 'fas fa-file-image text-warning',
            'gif' => 'fas fa-file-image text-warning',
            'zip' => 'fas fa-file-archive text-secondary',
            'rar' => 'fas fa-file-archive text-secondary',
        ];

        return $icons[$extension] ?? 'fas fa-file text-muted';
    }

    // Methods
    public function delete()
    {
        // Xóa file vật lý
        if (Storage::exists($this->file_path)) {
            Storage::delete($this->file_path);
        }

        return parent::delete();
    }

    /**
     * Summary of getDocumentFileUrlAttribute
     * @return string|null
     */
    public function getDocumentFileUrlAttribute()
    {
        if ($this->file_path) {
            return Storage::url('/' . $this->file_path);
        }
        return null;
    }
}