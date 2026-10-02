<?php

use Filament\Facades\Filament;
use Filament\Panel;
use Sh4msi\FilamentOtp\FilamentOtpPlugin;

it('has correct plugin id', function () {
    $plugin = new FilamentOtpPlugin;

    expect($plugin->getId())->toBe('filament-otp');
});

it('can be instantiated using make static method', function () {
    $plugin = FilamentOtpPlugin::make();

    expect($plugin)->toBeInstanceOf(FilamentOtpPlugin::class);
});

it('can be registered to a panel and retrieved with get', function () {
    $plugin = FilamentOtpPlugin::make();

    $panel = Panel::make()
        ->id('custom')
        ->plugin($plugin);

    $panel->register();

    expect($panel->hasPlugin('filament-otp'))->toBeTrue();
    expect($panel->getPlugin('filament-otp'))->toBe($plugin);
});

it('can be retrieved using FilamentOtpPlugin::get() from current panel', function () {
    Filament::setCurrentPanel(Filament::getPanel('app'));

    $plugin = FilamentOtpPlugin::get();

    expect($plugin)->toBeInstanceOf(FilamentOtpPlugin::class);
    expect($plugin->getId())->toBe('filament-otp');
});
