<?php

namespace Sh4msi\FilamentOtp;

use Livewire\Livewire;
use Sh4msi\FilamentOtp\Http\Livewire\Auth\ConfirmOTP;
use Sh4msi\FilamentOtp\Http\Livewire\Auth\LoginOTP;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentOtpServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-otp';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */

        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews()
            ->hasRoute('web');

    }

    public function packageRegistered()
    {
        $this->app->register(EventServiceProvider::class);
    }

    public function packageBooted(): void
    {
        Livewire::component('filament-otp.confirm', ConfirmOTP::class);
        Livewire::component('filament-otp.login-otp', LoginOTP::class);
    }
}
