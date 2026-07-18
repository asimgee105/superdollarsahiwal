<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-filament::card>
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-success-500/10 text-success-500 rounded-full">
                    <x-filament::icon alias="panels::pages.dashboard.actions.filter" icon="heroicon-o-check-circle" class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Core Platform Status</h3>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">Active & Healthy</p>
                </div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-primary-500/10 text-primary-500 rounded-full">
                    <x-filament::icon alias="panels::pages.dashboard.actions.filter" icon="heroicon-o-server" class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Database Status</h3>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $metrics['database'] }}</p>
                </div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-warning-500/10 text-warning-500 rounded-full">
                    <x-filament::icon alias="panels::pages.dashboard.actions.filter" icon="heroicon-o-circle-stack" class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Free Disk Space</h3>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $metrics['disk_free_gb'] }} GB ({{ $metrics['disk_usage_percent'] }}% used)</p>
                </div>
            </div>
        </x-filament::card>
    </div>

    <x-filament::card class="mt-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Environment Diagnostics Information</h2>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <div class="flex justify-between py-3">
                <span class="text-gray-500">Platform Edition Version</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $metrics['app_version'] }}</span>
            </div>
            <div class="flex justify-between py-3">
                <span class="text-gray-500">Laravel Core Version</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $metrics['laravel_version'] }}</span>
            </div>
            <div class="flex justify-between py-3">
                <span class="text-gray-500">PHP Runtime Version</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $metrics['php_version'] }}</span>
            </div>
            <div class="flex justify-between py-3">
                <span class="text-gray-500">Default Cache Store Driver</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $metrics['cache_driver'] }}</span>
            </div>
            <div class="flex justify-between py-3">
                <span class="text-gray-500">Active Queue Pipeline Driver</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $metrics['queue_connection'] }}</span>
            </div>
        </div>
    </x-filament::card>
</x-filament-panels::page>
