<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'author_name',
        'author_email',
        'content',
        'status',
        'ip_address',
        'user_agent',
        'likes',
        'dislikes',
        'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_SPAM = 'spam';
    const STATUS_TRASH = 'trash';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($comment) {
            if (empty($comment->ip_address)) {
                $comment->ip_address = request()->ip();
            }
            if (empty($comment->user_agent)) {
                $comment->user_agent = request()->userAgent();
            }
            
            // Auto-approve if user is authenticated
            if (auth()->check() && empty($comment->status)) {
                $comment->status = self::STATUS_APPROVED;
                $comment->approved_at = now();
            }
        });
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function approvedReplies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')
                    ->where('status', self::STATUS_APPROVED);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function approve(): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now()
        ]);
    }

    public function markAsSpam(): void
    {
        $this->update(['status' => self::STATUS_SPAM]);
    }

    public function trash(): void
    {
        $this->update(['status' => self::STATUS_TRASH]);
    }

    public function getAuthorNameAttribute(): string
    {
        if ($this->user) {
            return $this->user->name;
        }
        
        return $this->attributes['author_name'] ?? 'Anonymous';
    }

    public function getAuthorAvatarAttribute(): string
    {
        if ($this->user && $this->user->avatar) {
            return $this->user->avatar_url;
        }
        
        $email = $this->user ? $this->user->email : ($this->author_email ?? 'anonymous@example.com');
        return 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($email))) . '?s=200&d=mp';
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}
