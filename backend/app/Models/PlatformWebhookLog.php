<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformWebhookLog extends Model
{
    protected $fillable = [
        'webhook_id',
        'payload',
        'response_status',
        'response_body',
        'is_success',
    ];

    protected $casts = [
        'payload' => 'array',
        'is_success' => 'boolean',
    ];

    /**
     * Get webhook details.
     */
    public function webhook(): BelongsTo
    {
        return $this->belongsTo(PlatformWebhook::class, 'webhook_id');
    }
}
