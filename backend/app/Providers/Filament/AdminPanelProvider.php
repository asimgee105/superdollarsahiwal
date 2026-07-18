<?php

namespace App\Providers\Filament;

use App\Livewire\Auth\CustomLogin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(CustomLogin::class)
            ->profile(\App\Filament\Pages\Auth\EditProfile::class)
            ->brandName('AURA ENTERPRISE')
            ->font('Outfit')
            ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->navigationGroups([
                'Catalog Attributes',
                'Product Catalog',
                'CMS & Blog',
                'OMS & CRM',
                'Site Builder',
                'Platform Core',
                'User Management',
                'System Documentation',
            ])
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function boot(): void
    {
        FilamentView::registerRenderHook(
            'panels::body.start',
            fn (): string => '<canvas id="web-grid-canvas" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 0; pointer-events: none;"></canvas>',
        );

        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): string => '
                <script>
                    (function() {
                        // Apply stored width before DOM compiles to prevent flicker
                        const savedWidth = localStorage.getItem("aura-sidebar-width") || "230px";
                        document.documentElement.style.setProperty("--sidebar-width", savedWidth);

                        function initSidebarResizer() {
                            const sidebar = document.querySelector(".fi-sidebar");
                            if (!sidebar || document.getElementById("sidebar-resizer")) return;

                            const resizer = document.createElement("div");
                            resizer.id = "sidebar-resizer";
                            resizer.style.position = "absolute";
                            resizer.style.top = "0";
                            resizer.style.right = "0";
                            resizer.style.width = "6px";
                            resizer.style.height = "100%";
                            resizer.style.cursor = "col-resize";
                            resizer.style.zIndex = "50";
                            resizer.style.borderRight = "2px solid transparent";
                            resizer.style.transition = "border-color 0.2s";

                            resizer.addEventListener("mouseenter", () => resizer.style.borderRightColor = "#6366f1");
                            resizer.addEventListener("mouseleave", () => resizer.style.borderRightColor = "transparent");

                            sidebar.appendChild(resizer);

                            resizer.addEventListener("mousedown", (e) => {
                                e.preventDefault();
                                document.body.style.cursor = "col-resize";
                                
                                const doDrag = (dragEvent) => {
                                    let newWidth = dragEvent.clientX;
                                    if (newWidth < 180) newWidth = 180;
                                    if (newWidth > 400) newWidth = 400;

                                    const widthStr = newWidth + "px";
                                    document.documentElement.style.setProperty("--sidebar-width", widthStr);
                                    localStorage.setItem("aura-sidebar-width", widthStr);
                                };

                                const stopDrag = () => {
                                    document.body.style.cursor = "default";
                                    window.removeEventListener("mousemove", doDrag);
                                    window.removeEventListener("mouseup", stopDrag);
                                };

                                window.addEventListener("mousemove", doDrag);
                                window.addEventListener("mouseup", stopDrag);
                            });
                        }

                        document.addEventListener("DOMContentLoaded", initSidebarResizer);
                        document.addEventListener("livewire:navigated", initSidebarResizer);
                        // Continuous check to catch lazy loaded aside elements
                        let interval = setInterval(initSidebarResizer, 100);
                        setTimeout(() => clearInterval(interval), 5000);
                    })();
                </script>
            '
        );

        FilamentView::registerRenderHook(
            'panels::head.end',
            fn (): string => '
                <style>
                    /* Premium Dark SaaS Theme Style for Filament Panel */
                    .fi-sidebar {
                        background-color: rgba(9, 9, 11, 0.96) !important;
                        border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
                    }
                    .fi-sidebar-item-button-active {
                        background: linear-gradient(135deg, rgba(99, 102, 241, 0.15) 0%, rgba(236, 72, 153, 0.1) 100%) !important;
                        border-left: 3px solid #6366f1 !important;
                        border-radius: 8px !important;
                    }
                    .fi-topbar {
                        background-color: rgba(9, 9, 11, 0.9) !important;
                        backdrop-filter: blur(12px) !important;
                        -webkit-backdrop-filter: blur(12px) !important;
                        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
                    }
                    .fi-card, .fi-ta, .fi-section {
                        background: rgba(17, 24, 39, 0.8) !important;
                        backdrop-filter: blur(15px) !important;
                        -webkit-backdrop-filter: blur(15px) !important;
                        border: 1px solid rgba(255, 255, 255, 0.08) !important;
                        border-radius: 16px !important;
                        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
                    }
                    .fi-ta-header {
                        background-color: rgba(20, 20, 25, 0.7) !important;
                    }
                </style>
            ',
        );
    }
}
