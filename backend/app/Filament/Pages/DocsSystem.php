<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Str;

class DocsSystem extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cloud-arrow-up';

    protected static ?string $title = '9. SysAdmin & Deploy';

    protected static ?string $navigationGroup = 'System Admin Guide';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.documentation-viewer';

    public function getViewData(): array
    {
        $path = 'C:\\Users\\Laptop Mart\\.gemini\\antigravity\\brain\\22962752-469b-4abe-8eff-4d11088c426d\\docs\\system_guide.md';
        $html = file_exists($path) ? Str::markdown(file_get_contents($path)) : '<p class="text-gray-400 italic">Documentation not found.</p>';
        return ['html' => $html];
    }
}
