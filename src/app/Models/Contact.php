<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Contact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'ip_address',
        'user_agent',
        'status',
        'read_at',
        'replied_at',
        'admin_notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'read_at' => 'datetime',
        'replied_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Status constants
     */
    const STATUS_NEW = 'new';
    const STATUS_READ = 'read';
    const STATUS_REPLIED = 'replied';
    const STATUS_SPAM = 'spam';

    /**
     * Get all available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_READ => 'Read',
            self::STATUS_REPLIED => 'Replied',
            self::STATUS_SPAM => 'Spam'
        ];
    }

    /**
     * Scope a query to only include new contacts.
     */
    public function scopeNew($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }

    /**
     * Scope a query to only include non-spam contacts.
     */
    public function scopeNotSpam($query)
    {
        return $query->where('status', '!=', self::STATUS_SPAM);
    }

    /**
     * Mark contact as read
     */
    public function markAsRead(): void
    {
        if ($this->status === self::STATUS_NEW) {
            $this->update([
                'status' => self::STATUS_READ,
                'read_at' => now()
            ]);
        }
    }

    /**
     * Mark contact as replied
     */
    public function markAsReplied(): void
    {
        $this->update([
            'status' => self::STATUS_REPLIED,
            'replied_at' => now()
        ]);
    }

    /**
     * Mark contact as spam
     */
    public function markAsSpam(): void
    {
        $this->update(['status' => self::STATUS_SPAM]);
    }

    /**
     * Get status badge color
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            self::STATUS_NEW => 'blue',
            self::STATUS_READ => 'yellow',
            self::STATUS_REPLIED => 'green',
            self::STATUS_SPAM => 'red',
            default => 'gray'
        };
    }

    /**
     * Get formatted created date
     */
    protected function formattedDate(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at->format('d/m/Y H:i')
        );
    }

    /**
     * Get excerpt of message
     */
    protected function messageExcerpt(): Attribute
    {
        return Attribute::make(
            get: fn () => str()->limit($this->message, 100)
        );
    }

    /**
     * Check if contact is new
     */
    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    /**
     * Check if contact has been read
     */
    public function isRead(): bool
    {
        return in_array($this->status, [self::STATUS_READ, self::STATUS_REPLIED]);
    }

    /**
     * Check if contact has been replied
     */
    public function isReplied(): bool
    {
        return $this->status === self::STATUS_REPLIED;
    }

    /**
     * Check if contact is spam
     */
    public function isSpam(): bool
    {
        return $this->status === self::STATUS_SPAM;
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Set IP and User Agent when creating
        static::creating(function ($contact) {
            if (empty($contact->ip_address)) {
                $contact->ip_address = request()->ip();
            }
            if (empty($contact->user_agent)) {
                $contact->user_agent = request()->userAgent();
            }
        });
    }
}