<?php

use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Support\Facades\Route;
use Sh4msi\FilamentOtp\Http\Middleware\TokenGuard;

Route::name('filament-otp.')
    ->group(function () {
        $configuredPanels = config('filament-otp.panels');

        foreach (Filament::getPanels() as $panel) {
            /** @var Panel $panel */
            $panelId = $panel->getId();

            if (is_array($configuredPanels)) {
                if (! in_array($panelId, $configuredPanels, true)) {
                    continue;
                }
            } else {
                $panelsWithPlugin = array_filter(
                    Filament::getPanels(),
                    fn (Panel $p): bool => $p->hasPlugin('filament-otp')
                );

                if (! empty($panelsWithPlugin) && ! $panel->hasPlugin('filament-otp')) {
                    continue;
                }
            }

            $domains = $panel->getDomains();

            foreach ((empty($domains) ? [null] : $domains) as $domain) {
                Route::domain($domain)
                    ->middleware($panel->getMiddleware())
                    ->name("{$panelId}." . ((filled($domain) && (count($domains) > 1)) ? "{$domain}." : ''))
                    ->prefix($panel->getPath())
                    ->group(function () {
                        Route::get('/login/otp/confirm', config('filament-otp.confirm_token_component'))
                            ->middleware([TokenGuard::class])
                            ->name('confirm');

                        Route::get('/login/otp', config('filament-otp.login_otp_component'))
                            ->name('login');
                    });
            }
        }
    });
