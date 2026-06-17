<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Actions\Action;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Enums\UserMenuPosition;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Navigation\MenuItem;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession; 
use Filament\Support\Icons\Heroicon;
use Filament\Pages\Auth\EditProfile;
use BackedEnum;

class AdminPanelProvider extends PanelProvider
{
    // protected static string | BackedEnum | null $activeNavigationIcon = 'heroicon-s-home';
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->font('Plus Jakarta Sans')
            ->login()
            ->brandLogo(asset('logo/horizontal-light.png'))
            ->darkModeBrandLogo(asset('logo/horizontal-dark.png'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('logo/logo-512.png'))
            ->brandName('ALBIRRU TRANS')
            ->globalSearch(false)
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('Pengaturan Akun')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn (): string => route('filament.admin.auth.profile')),
                    ])
            ->profile()
            ->collapsibleNavigationGroups(false)
            ->colors([
                'primary' => Color::hex('#196FEB'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                // AccountWidget::class,
                // FilamentInfoWidget::class,
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
            ])
            ->renderHook(
                'panels::head.end',
                fn () => new \Illuminate\Support\HtmlString("
                    <style>
                        .fi-sidebar-item-active a {
                            background-color: #196FEB !important;
                            color: white !important;
                            border-radius: 0.5rem;
                        }
    
                        .fi-sidebar-item-active a svg {
                            color: white !important;
                        }

                        .fi-sidebar-item-active a:hover {
                            filter: brightness(110%);
                        }
                    </style>
                "),
            );;
    }
}
