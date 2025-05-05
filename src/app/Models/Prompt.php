<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prompt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'content',
        'example_output',
        'variable_descriptions',
        'is_public',
        'view_count',
        'use_count',
    ];

    protected $casts = [
        'variable_descriptions' => 'array',
        'is_public' => 'boolean',
        
    ];
    protected static function booted()
    {
        static::created(function ($prompt) {
            $prompt->category->increment('prompt_count');
        });

        static::deleted(function ($prompt) {
            $prompt->category->decrement('prompt_count');
        });

        // Nếu prompt có thể chuyển category
        static::updating(function ($prompt) {
            if ($prompt->isDirty('category_id')) {
                // Giảm count ở category cũ
                $oldCategory = Category::find($prompt->getOriginal('category_id'));
                if ($oldCategory) {
                    $oldCategory->decrement('prompt_count');
                }
                
                // Tăng count ở category mới
                $newCategory = Category::find($prompt->category_id);
                if ($newCategory) {
                    $newCategory->increment('prompt_count');
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'prompt_tags');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function savedBy()
    {
        return $this->belongsToMany(User::class, 'saved', 'prompt_id', 'user_id')
            ->withTimestamps();
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeTrending($query, $days = 7)
    {
        return $query->public()
            ->withCount(['likes' => function ($query) use ($days) {
                $query->where('created_at', '>=', now()->subDays($days));
            }])
            ->orderByDesc('likes_count');
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
        return $this;
    }

    public function incrementUseCount()
    {
        $this->increment('use_count');
        return $this;
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'user_id' => $this->user_id,
            'category_id' => $this->category_id,
            'is_public' => $this->is_public,
        ];
    }
}
