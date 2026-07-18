<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlatformAutomationWorkflow extends Model
{
    protected $fillable = [
        'name',
        'trigger_event',
        'conditions',
        'actions',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'conditions' => 'array',
        'actions' => 'array',
    ];

    /**
     * Get associated execution logs.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(PlatformWorkflowLog::class, 'workflow_id');
    }
}
