<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-filament::card>
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-2">System Cache Manager</h2>
            <p class="text-sm text-gray-500 mb-6">Flush route mapping files, compiled configurations, database buffers, and render views.</p>
            
            <x-filament::button wire:click="clearCache" color="danger" size="lg">
                Flush All System Caches
            </x-filament::button>
        </x-filament::card>

        <x-filament::card>
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Dummy Data Manager</h2>
            <p class="text-sm text-gray-500 mb-6">Delete all seeded catalog products, sales orders, customer profiles and reviews, or generate them.</p>
            
            <div class="flex gap-4">
                <x-filament::button wire:click="deleteDummyData" color="danger" size="lg" wire:confirm="Are you sure you want to delete all catalog, order, and customer dummy data? This cannot be undone.">
                    Delete Dummy Data
                </x-filament::button>
                <x-filament::button wire:click="generateDummyData" color="success" size="lg" wire:confirm="Are you sure you want to seed the database with catalog and sales dummy data?">
                    Generate Dummy Data
                </x-filament::button>
            </div>
        </x-filament::card>

        <x-filament::card>
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Environment Information</h2>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <div class="flex justify-between py-3">
                    <span class="text-gray-500">Environment Name</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ app()->environment() }}</span>
                </div>
                <div class="flex justify-between py-3">
                    <span class="text-gray-500">Debug Mode Status</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ config('app.debug') ? 'Enabled' : 'Disabled' }}</span>
                </div>
                <div class="flex justify-between py-3">
                    <span class="text-gray-500">Application URL</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ config('app.url') }}</span>
                </div>
            </div>
        </x-filament::card>
    </div>
</x-filament-panels::page>
