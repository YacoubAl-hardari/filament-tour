<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use YacoubAlhaidari\FilamentTour\FilamentTourPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
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
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                    FilamentTourPlugin::make()
                    ->tourButtonIcon('heroicon-o-academic-cap')
                    ->tourButtonColor('info')
                    ->tourButtonTooltip('الجولة التعريفية')
                    
                    //  تخصيص الألوان
                    ->headerColor('linear-gradient(135deg, #282835 0%, #282835 100%)')
                    ->primaryButtonColor('linear-gradient(135deg, #282835 0%, #282835 100%)')
                    ->secondaryButtonColor('linear-gradient(135deg,  #282835 0%,  #282835 100%)')
                    ->textColor('#1f2937')
                    ->backgroundColor('linear-gradient(135deg, #282835 0%, #282835 100%)')

                    ->contentBackgroundColor('#282835')
                    ->footerBackgroundColor('linear-gradient(180deg, #282835 0%, #282835 100%)')
                    ->primaryButtonHoverColor('linear-gradient(135deg, #ea580c 0%, #dc2626 100%)')
                    ->secondaryButtonHoverColor('linear-gradient(135deg, #282835 0%, #282835 100%)')
                    ->primaryButtonTextColor('#ffff')
                    ->secondaryButtonTextColor('#ffff')
                    ->footerBorderColor('rgba(0, 0, 0, 0.1)')
                    
                    ->welcomeStep([
                        'id' => 'welcome',
                        'title' => ' مرحباً مخصص!',
                        'text' => '<strong>رسالة ترحيب مخصصة</strong>',
                        'buttons' => [
                            ['text' => 'تخطي', 'action' => 'cancel', 'secondary' => true],
                            ['text' => 'ابدأ', 'action' => 'next', 'secondary' => false],
                        ]
                    ])
                    ->finishStep([
                        'id' => 'finish',
                        'title' => ' تهانينا مخصص!',
                        'text' => '<strong>رسالة إنهاء مخصصة</strong>',
                        'buttons' => [
                            ['text' => 'السابق', 'action' => 'back', 'secondary' => true],
                            ['text' => 'إنهاء', 'action' => 'complete', 'secondary' => false],
                        ]
                    ])

            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
