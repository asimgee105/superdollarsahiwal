<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    protected $fillable = [
        'user_id',
        'channel',
        'recipient',
        'subject',
        'body',
        'status',
        'error_message',
        'retry_count',
    ];

    /**
     * Get the associated user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
