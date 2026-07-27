<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\DashboardCustom;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
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
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                DashboardCustom::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->maxContentWidth(\Filament\Support\Enums\Width::Full)
            ->topbar(false)
            ->sidebarFullyCollapsibleOnDesktop()
            ->renderHook(
                \Filament\View\PanelsRenderHook::PAGE_START,
                function (): string {
                    if (request()->routeIs('filament.admin.pages.dashboard-custom', 'filament.admin.pages.pos-kasir')) {
                        return '';
                    }

                    // Master Data & Setup pages do not require global date filter
                    $noDateFilterRoutes = [
                        'filament.admin.resources.categories.*',
                        'filament.admin.resources.customers.*',
                        'filament.admin.resources.products.*',
                        'filament.admin.resources.suppliers.*',
                        'filament.admin.resources.units.*',
                        'filament.admin.resources.users.*',
                    ];

                    $showDateFilter = ! request()->routeIs($noDateFilterRoutes);

                    return \Illuminate\Support\Facades\Blade::render(
                        '@livewire(\'global-header\', [\'showSearch\' => false, \'showDateFilter\' => ' . ($showDateFilter ? 'true' : 'false') . '])'
                    );
                },
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn (): string => '<style>
                    /* Fix Table Container Clipping & Z-Index when table has few rows */
                    .fi-ta-ctn, .fi-ta, .fi-ta-content {
                        min-height: 320px !important;
                    }
                    .fi-ta-ctn, .fi-ta-header-ctn, .fi-ta-header, .fi-ta-actions, .fi-ta-filters {
                        overflow: visible !important;
                    }
                    .fi-dropdown-panel, .fi-ta-filters-dropdown, .fi-popover-panel, [class*="fi-ta-filters"] {
                        z-index: 99999 !important;
                    }
                    .dark input, .dark select, .dark textarea {
                        color-scheme: dark !important;
                    }
                    .dark input:focus, .dark select:focus, .dark textarea:focus {
                        background-color: #27272a !important;
                        color: #f4f4f5 !important;
                    }
                    .dark input:-webkit-autofill,
                    .dark input:-webkit-autofill:hover, 
                    .dark input:-webkit-autofill:focus {
                        -webkit-text-fill-color: #f4f4f5 !important;
                        -webkit-box-shadow: 0 0 0px 1000px #27272a inset !important;
                        transition: background-color 5000s ease-in-out 0s;
                    }
                </style>'
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn (): string => '<script>
                    document.addEventListener("DOMContentLoaded", function () {
                        document.addEventListener("click", function (e) {
                            const btn = e.target.closest("button");
                            if (btn) {
                                const isFilterBtn = btn.closest(".fi-dropdown-panel, .fi-ta-filters-dropdown, [class*=\"filters\"]") || btn.innerText.includes("Apply") || btn.innerText.includes("Terapkan") || btn.innerText.includes("Reset");
                                if (isFilterBtn && (btn.getAttribute("type") === "submit" || btn.getAttribute("wire:click") || btn.closest("form"))) {
                                    const panel = btn.closest(".fi-dropdown-panel, .fi-ta-filters-dropdown, [x-ref=\"panel\"]");
                                    if (panel) {
                                        panel.style.display = "none";
                                    }
                                    setTimeout(() => {
                                        document.body.click();
                                        window.dispatchEvent(new KeyboardEvent("keydown", { key: "Escape" }));
                                    }, 50);
                                }
                            }
                        }, true);
                    });
                </script>'
            )
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
