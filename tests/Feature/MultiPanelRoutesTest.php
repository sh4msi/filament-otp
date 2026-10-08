<?php

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\PanelRegistry;
use Illuminate\Support\Facades\Route;
use Sh4msi\FilamentOtp\Facades\FilamentOtp;
use Sh4msi\FilamentOtp\FilamentOtpPlugin;

beforeEach(function () {
    config()->set('filament-otp.panels', null);
});

it('generates unique route names across multiple panels without collision', function () {
    $adminPanel = Panel::make()
        ->id('admin')
        ->path('admin')
        ->plugin(FilamentOtpPlugin::make());

    app(PanelRegistry::class)->register($adminPanel);

    require __DIR__ . '/../../routes/web.php';
    app('router')->getRoutes()->refreshNameLookups();

    expect(Route::has('filament-otp.app.login'))->toBeTrue();
    expect(Route::has('filament-otp.app.confirm'))->toBeTrue();
    expect(Route::has('filament-otp.admin.login'))->toBeTrue();
    expect(Route::has('filament-otp.admin.confirm'))->toBeTrue();

    // Verify no duplicate route names exist (which triggers serialization exception)
    $routeNames = [];
    foreach (Route::getRoutes() as $route) {
        $name = $route->getName();
        if ($name) {
            expect(isset($routeNames[$name]))->toBeFalse("Duplicate route name detected: {$name}");
            $routeNames[$name] = true;
        }
    }
});

it('resolves correct login and confirm routes and urls dynamically based on current panel', function () {
    $adminPanel = Panel::make()
        ->id('admin')
        ->path('admin')
        ->plugin(FilamentOtpPlugin::make());

    app(PanelRegistry::class)->register($adminPanel);

    require __DIR__ . '/../../routes/web.php';
    app('router')->getRoutes()->refreshNameLookups();

    // When current panel is app
    Filament::setCurrentPanel(Filament::getPanel('app'));
    expect(FilamentOtp::getCurrentPanelId())->toBe('app');
    expect(FilamentOtp::getLoginRouteName())->toBe('filament-otp.app.login');
    expect(FilamentOtp::getConfirmRouteName())->toBe('filament-otp.app.confirm');
    expect(FilamentOtp::getLoginUrl())->toBe(route('filament-otp.app.login'));
    expect(FilamentOtp::getConfirmUrl())->toBe(route('filament-otp.app.confirm'));

    // When current panel is admin
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    expect(FilamentOtp::getCurrentPanelId())->toBe('admin');
    expect(FilamentOtp::getLoginRouteName())->toBe('filament-otp.admin.login');
    expect(FilamentOtp::getConfirmRouteName())->toBe('filament-otp.admin.confirm');
    expect(FilamentOtp::getLoginUrl())->toBe(route('filament-otp.admin.login'));
    expect(FilamentOtp::getConfirmUrl())->toBe(route('filament-otp.admin.confirm'));
});

it('only registers routes for panels specified in filament-otp.panels config', function () {
    $customerPanel = Panel::make()
        ->id('customer')
        ->path('customer')
        ->plugin(FilamentOtpPlugin::make());

    $staffPanel = Panel::make()
        ->id('staff')
        ->path('staff')
        ->plugin(FilamentOtpPlugin::make());

    app(PanelRegistry::class)->register($customerPanel);
    app(PanelRegistry::class)->register($staffPanel);

    // Only allow customer panel in config
    config()->set('filament-otp.panels', ['customer']);

    require __DIR__ . '/../../routes/web.php';
    app('router')->getRoutes()->refreshNameLookups();

    expect(Route::has('filament-otp.customer.login'))->toBeTrue();
    expect(Route::has('filament-otp.customer.confirm'))->toBeTrue();
    expect(Route::has('filament-otp.staff.login'))->toBeFalse();
    expect(Route::has('filament-otp.staff.confirm'))->toBeFalse();
});

it('does not register routes for panels without plugin when plugin is used on another panel', function () {
    $portalPanel = Panel::make()
        ->id('portal')
        ->path('portal')
        ->plugin(FilamentOtpPlugin::make());

    $apiPanel = Panel::make()
        ->id('api')
        ->path('api'); // No FilamentOtpPlugin

    app(PanelRegistry::class)->register($portalPanel);
    app(PanelRegistry::class)->register($apiPanel);

    config()->set('filament-otp.panels', null);

    require __DIR__ . '/../../routes/web.php';
    app('router')->getRoutes()->refreshNameLookups();

    expect(Route::has('filament-otp.portal.login'))->toBeTrue();
    expect(Route::has('filament-otp.portal.confirm'))->toBeTrue();
    expect(Route::has('filament-otp.api.login'))->toBeFalse();
    expect(Route::has('filament-otp.api.confirm'))->toBeFalse();
});
