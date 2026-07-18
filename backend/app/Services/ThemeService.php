<?php

namespace App\Services;

use App\Models\PlatformTheme;

class ThemeService
{
    /**
     * Get active layout custom styling properties.
     */
    public function getActiveThemeStyles(): array
    {
        $theme = PlatformTheme::where('is_active', true)->first();
        if ($theme) {
            return [
                'variables' => $theme->variables,
                'css' => $theme->custom_css,
                'js' => $theme->custom_js,
            ];
        }

        return [
            'variables' => [
                'primary_color' => '#ff3f6c',
                'font_family' => 'Outfit',
            ],
            'css' => '',
            'js' => '',
        ];
    }
}
