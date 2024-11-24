<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\ResetPassword as AuthResetPassword;
use App\Filament\Resources\DashboardResource\Widgets\LowStockSuppy;
use App\Filament\Resources\DashboardResource\Widgets\SummaryOverview;
use App\Filament\Resources\EquipmentResource\Pages\ListEquipment;
use App\Livewire\DeleteArchive;
use CustomLoginPage;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Pages\Auth\PasswordReset\ResetPassword;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Vite;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandLogo(Vite::asset('resources/images/iTrackLogo.png'))
            ->brandLogoHeight('100px')
            ->login()
            ->brandName('iTracking')
            ->colors([
                'primary' => '#0B592D',
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('User Manual')
                    ->url('/user-manual')
                    ->icon('heroicon-o-arrow-down-tray')
            ])
            ->passwordReset()
            ->databaseNotifications()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                SummaryOverview::class,
                LowStockSuppy::class
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
