<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

class Post extends Model
{
    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'short_description',
        'body',
        'image_url',
        'reading_time',
        'is_published',
        'published_at',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }

    /**
     * Get the author.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get categories.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(PostCategory::class, 'post_category_pivot', 'post_id', 'category_id');
    }
}
