<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'color', 'order', 'is_active'];
    
    public function posts() {
        return $this->hasMany(Post::class);
    }
}
