<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

class PostCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }

    /**
     * Get the articles belonging to this category.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_category_pivot', 'category_id', 'post_id');
    }
}
