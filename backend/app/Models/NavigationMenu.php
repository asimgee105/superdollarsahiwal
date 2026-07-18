<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class NavigationMenu extends Model
{
    protected $fillable = ['name', 'key', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
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
     * Get the root items for this menu.
     */
    public function items(): HasMany
    {
        return $this->hasMany(NavigationItem::class, 'menu_id')
            ->whereNull('parent_id')
            ->orderBy('sort_order');
    }
}
