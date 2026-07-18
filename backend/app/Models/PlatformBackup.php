<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformBackup extends Model
{
    protected $fillable = [
        'filename',
        'disk',
        'size_bytes',
        'status',
    ];
}
