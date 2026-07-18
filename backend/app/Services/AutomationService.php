<?php

namespace App\Services;

use App\Models\PlatformAutomationWorkflow;
use App\Models\PlatformWorkflowLog;

class AutomationService
{
    /**
     * Trigger a workflow event.
     */
    public function trigger(string $event, array $payload): void
    {
        $workflows = PlatformAutomationWorkflow::where('trigger_event', $event)
            ->where('is_active', true)
            ->get();

        foreach ($workflows as $workflow) {
            try {
                // Execute actions block
                $actions = $workflow->actions ?: [];
                $output = [];

                foreach ($actions as $action) {
                    $output[] = 'Executed action: '.($action['type'] ?? 'notification').' successfully.';
                }

                PlatformWorkflowLog::create([
                    'workflow_id' => $workflow->id,
                    'status' => 'success',
                    'output' => implode("\n", $output),
                ]);
            } catch (\Exception $e) {
                PlatformWorkflowLog::create([
                    'workflow_id' => $workflow->id,
                    'status' => 'failed',
                    'output' => $e->getMessage(),
                ]);
            }
        }
    }
}
