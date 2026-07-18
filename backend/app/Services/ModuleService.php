<?php

namespace App\Services;

use App\Models\PlatformModule;

class ModuleService
{
    /**
     * Verify if a module is enabled.
     */
    public function isEnabled(string $name): bool
    {
        $module = PlatformModule::where('name', $name)->first();
        if ($module) {
            return $module->is_enabled;
        }

        return false;
    }

    /**
     * Enable a module.
     */
    public function enableModule(string $name): void
    {
        PlatformModule::updateOrCreate(
            ['name' => $name],
            ['is_enabled' => true]
        );
    }

    /**
     * Disable a module.
     */
    public function disableModule(string $name): void
    {
        PlatformModule::updateOrCreate(
            ['name' => $name],
            ['is_enabled' => false]
        );
    }
}
