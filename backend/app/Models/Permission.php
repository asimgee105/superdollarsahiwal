<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = ['name', 'guard_name'];

    /**
     * The roles that belong to the permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'role_has_permissions',
            'permission_id',
            'role_id'
        );
    }

    /**
     * The users that belong to the permission.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'model_has_permissions',
            'permission_id',
            'model_id'
        )->withPivotValue(['model_type' => User::class]);
    }
}
