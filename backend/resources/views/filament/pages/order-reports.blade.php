<x-filament-panels::page>
    <form wire:submit.prevent="exportCsv">
        {{ $this->form }}

        <div class="mt-6 flex gap-4">
            <x-filament::button type="submit" color="success" size="lg">
                Export report to CSV
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
