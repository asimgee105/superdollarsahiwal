<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect404Log extends Model
{
    protected $table = 'redirect_404_logs';

    protected $fillable = [
        'url',
        'referrer',
        'ip_address',
        'hit_count',
    ];
}
