<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;
use Sh4msi\FilamentOtp\Http\Middleware\TokenGuard;

Route::name('filament-otp.')
    ->group(function () {
        foreach (Filament::getPanels() as $panel) {
            $domains = $panel->getDomains();

            foreach ((empty($domains) ? [null] : $domains) as $domain) {
                Route::domain($domain)
                    ->middleware($panel->getMiddleware())
//                    ->name($panel->getId() . '.')
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
