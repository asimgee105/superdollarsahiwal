<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

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
     * Get setting value by key helper.
     */
    public function getValueAttribute($value)
    {
        // Try decoding as JSON if it represents a JSON payload
        $decoded = json_decode($value, true);

        return (json_last_error() === JSON_ERROR_NONE) ? $decoded : $value;
    }

    /**
     * Helper to get a setting value.
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Helper to set a setting value.
     */
    public static function set(string $key, $value): self
    {
        $val = is_array($value) ? json_encode($value) : $value;

        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $val]
        );
    }
}
