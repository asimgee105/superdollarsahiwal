<x-filament-panels::page>
    <div class="space-y-8 select-none">
        
        {/* Custom Header Greeting */}
        <div class="flex items-center justify-between pb-4 border-b border-zinc-200/60 dark:border-zinc-800/60">
            <div>
                <h1 class="text-xl font-extrabold tracking-tight text-zinc-900 dark:text-white font-heading">
                    Welcome to AURA Workspace
                </h1>
                <p class="text-xs text-zinc-400 mt-0.5">
                    Real-time operational dashboard and site metrics.
                </p>
            </div>
            
            {/* Quick Refresh Indicators */}
            <div class="text-[10px] bg-emerald-500/10 text-emerald-500 px-2 py-0.5 rounded-sm font-bold flex items-center gap-1 uppercase tracking-wider">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                System Live
            </div>
        </div>

        {/* Render the default Stats Widget at the top */}
        <div class="grid gap-6">
            @livewire(\App\Filament\Widgets\StatsOverviewWidget::class)
        </div>

        {/* Custom Grid Cards (Stripe / Vercel layout style) */}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 space-y-6">
                @livewire(\App\Filament\Widgets\LatestUsersWidget::class)
                @livewire(\App\Filament\Widgets\ActivityLogWidget::class)
            </div>

            {/* Right Card: Quick Actions and System settings */}
            <div class="bg-white/70 dark:bg-zinc-900/60 backdrop-blur-xl border border-zinc-200/60 dark:border-zinc-800/80 rounded-xl p-5 shadow-xs flex flex-col gap-4">
                <h3 class="text-xs font-black uppercase tracking-wider text-zinc-800 dark:text-zinc-200">
                    Quick Actions
                </h3>
                
                <div class="flex flex-col gap-2.5 mt-2">
                    <a href="/admin/manage-settings" class="block w-full border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 p-3 rounded-lg text-xs font-bold text-zinc-700 dark:text-zinc-350 transition-colors">
                        ⚙️ Configure Site Settings
                    </a>
                    <a href="/admin/profile" class="block w-full border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 p-3 rounded-lg text-xs font-bold text-zinc-700 dark:text-zinc-350 transition-colors">
                        👤 Edit Admin Profile
                    </a>
                    <a href="/" target="_blank" class="block w-full border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 p-3 rounded-lg text-xs font-bold text-zinc-700 dark:text-zinc-350 transition-colors">
                        🌐 Visit Main Website
                    </a>
                </div>
            </div>

        </div>

    </div>
</x-filament-panels::page>
