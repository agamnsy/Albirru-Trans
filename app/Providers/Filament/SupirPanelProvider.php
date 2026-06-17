<?php

namespace App\Providers\Filament;

use App\Filament\Supir\Widgets\SupirStatsOverview;
use App\Filament\Supir\Widgets\TugasAktifTable;
use App\Filament\Supir\Widgets\RiwayatTugasTerbaruTable;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
// use Filament\Pages\Auth\EditProfile;
use Filament\Navigation\MenuItem;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SupirPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('supir')
            ->path('supir')
            ->font('Plus Jakarta Sans')
            ->login()
            ->brandLogo(asset('logo/horizontal-light.png'))
            ->darkModeBrandLogo(asset('logo/horizontal-dark.png'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('logo/logo-512.png'))
            ->globalSearch(false)
            ->collapsibleNavigationGroups(false)
            ->colors([
                'primary' => Color::hex('#196FEB'),
            ])
            ->discoverResources(in: app_path('Filament/Supir/Resources'), for: 'App\Filament\Supir\Resources')
            ->discoverPages(in: app_path('Filament/Supir/Pages'), for: 'App\Filament\Supir\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Supir/Widgets'), for: 'App\Filament\Supir\Widgets')
            ->widgets([
                // AccountWidget::class,
                // FilamentInfoWidget::class,
                SupirStatsOverview::class,
                TugasAktifTable::class,
                RiwayatTugasTerbaruTable::class,
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
