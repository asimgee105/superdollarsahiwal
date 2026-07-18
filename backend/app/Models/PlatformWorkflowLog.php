<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformWorkflowLog extends Model
{
    protected $fillable = [
        'workflow_id',
        'status',
        'output',
    ];

    /**
     * Get associated workflow.
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(PlatformAutomationWorkflow::class, 'workflow_id');
    }
}
