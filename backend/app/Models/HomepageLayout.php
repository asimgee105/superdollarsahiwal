<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class HomepageLayout extends Model
{
    protected $fillable = [
        'name',
        'header_style',
        'hero_style',
        'category_style',
        'product_card_style',
        'footer_style',
        'colors',
        'typography',
        'is_active',
    ];

    protected $casts = [
        'colors' => 'array',
        'typography' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(function ($model) {
            if ($model->is_active) {
                // Deactivate all other layouts
                static::where('id', '!=', $model->id)->update(['is_active' => false]);
            }
            Cache::flush();
        });

        static::deleted(function () {
            Cache::flush();
        });
    }

    /**
     * Get the sections for this layout.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(HomepageSection::class, 'layout_id')->orderBy('sort_order');
    }
}
