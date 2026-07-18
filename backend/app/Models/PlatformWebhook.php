<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlatformWebhook extends Model
{
    protected $fillable = [
        'name',
        'url',
        'event',
        'secret',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get webhook logs.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(PlatformWebhookLog::class, 'webhook_id');
    }
}
