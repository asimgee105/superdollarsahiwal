<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class HomepageSection extends Model
{
    protected $fillable = [
        'layout_id',
        'section_key',
        'title',
        'subtitle',
        'description',
        'background_type',
        'background_color',
        'background_image',
        'background_video',
        'padding',
        'margin',
        'width',
        'animation',
        'button_text',
        'button_url',
        'layout_variation',
        'is_enabled',
        'show_on_mobile',
        'show_on_desktop',
        'start_date',
        'end_date',
        'sort_order',
        'settings',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'show_on_mobile' => 'boolean',
        'show_on_desktop' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'settings' => 'array',
    ];

    protected static function booted()
    {
        static::saved(function () {
            Cache::flush();
        });

        static::deleted(function () {
            Cache::flush();
        });
    }

    /**
     * Get the layout that owns the section.
     */
    public function layout(): BelongsTo
    {
        return $this->belongsTo(HomepageLayout::class, 'layout_id');
    }
}
