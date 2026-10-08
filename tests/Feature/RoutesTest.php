<?php

use Illuminate\Support\Facades\Route;
use Sh4msi\FilamentOtp\Http\Middleware\TokenGuard;

it('registers otp login and confirm routes for the panel', function () {
    expect(Route::has('filament-otp.app.login'))->toBeTrue();
    expect(Route::has('filament-otp.app.confirm'))->toBeTrue();

    $loginRoute = Route::getRoutes()->getByName('filament-otp.app.login');
    $confirmRoute = Route::getRoutes()->getByName('filament-otp.app.confirm');

    expect($loginRoute->uri())->toBe('app/login/otp');
    expect($confirmRoute->uri())->toBe('app/login/otp/confirm');
});

it('attaches TokenGuard middleware to confirm route', function () {
    $confirmRoute = Route::getRoutes()->getByName('filament-otp.app.confirm');

    expect($confirmRoute->gatherMiddleware())->toContain(TokenGuard::class);
});
