<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class FilterConfig extends Model
{
    protected $table = 'filter_configs';

    protected $fillable = [
        'category_id',
        'filter_key',
        'label',
        'sort_order',
        'is_enabled',
        'style',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }

    /**
     * Get the associated category record.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
