<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ControlPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('dashboard')
            ->path('dashboard')
            ->login()
            ->brandName('نتيجتي')
            ->brandLogo(fn () => view('filament.brand-logo'))
            ->favicon(asset('favicon.ico'))
            ->colors([
                'primary' => \Filament\Support\Colors\Color::Emerald,
                'danger' => \Filament\Support\Colors\Color::Rose,
                'warning' => \Filament\Support\Colors\Color::Amber,
                'success' => \Filament\Support\Colors\Color::Green,
                'info' => \Filament\Support\Colors\Color::Sky,
                'gray' => \Filament\Support\Colors\Color::Slate,
            ])
            ->font('Cairo')
            ->darkMode(true)
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn (): string => '
                    <link rel="preconnect" href="https://fonts.googleapis.com">
                    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
                    <link rel="stylesheet" href="' . asset('css/filament/control/theme.css') . '?v=' . time() . '" />
                ',
            )
            ->maxContentWidth('full')
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make()
                    ->label('الرئيسية')
                    ->icon('heroicon-o-home'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label('إدارة البيانات')
                    ->icon('heroicon-o-circle-stack')
                    ->collapsed(),
                \Filament\Navigation\NavigationGroup::make()
                    ->label('النتائج')
                    ->icon('heroicon-o-document-chart-bar'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label('إعدادات الموقع')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
                \Filament\Navigation\NavigationGroup::make()
                    ->label('إعدادات السوشيال')
                    ->icon('heroicon-o-share')
                    ->collapsed(),
                \Filament\Navigation\NavigationGroup::make()
                    ->label('إعدادات الإعلانات')
                    ->icon('heroicon-o-megaphone')
                    ->collapsed(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\UpcomingResultsWidget::class,
                \App\Filament\Widgets\RecentUploadsWidget::class,
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
}
