<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformLicense extends Model
{
    protected $fillable = [
        'license_key',
        'domain',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
