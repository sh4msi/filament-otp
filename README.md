# Filament OTP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sh4msi/filament-otp.svg?style=flat-square)](https://packagist.org/packages/sh4msi/filament-otp)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/sh4msi/filament-otp/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/sh4msi/filament-otp/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/sh4msi/filament-otp/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/sh4msi/filament-otp/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/sh4msi/filament-otp.svg?style=flat-square)](https://packagist.org/packages/sh4msi/filament-otp)

Filament OTP is a package for **Filament 3** that allows users to login with a One-Time Password.
(OTP and Passwordless are similar, but they differ in some aspects!)

## Installation

You can install the package via composer:

```bash
composer require sh4msi/filament-otp
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-otp-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-otp-views"
```

This is the contents of the published config file:

```php
return [
    /**
     * The authentication model to use.
     */
    'user_model' => \App\Models\User::class,

    /**
     * login columns
     */
    'login_key' => 'email',

    'login_key_rule' => ['email', 'required'],

    /**
     * token resend countdown time
     */
    'resent_token_countdown_time' => 60,

    /**
     * token count
     */
    'token_count' => 5,

    /**
     * token type
     * number, string, etc
     */
    'token_type' => 'number',

    /**
     * token expiry (minutes)
     */
    'token_expiry' => 15,

    /**
     * Rate limit count
     */
    'rate_limit_count' => 3,

    /**
     * Rate limit decay seconds
     */
    'rate_limit_decay_seconds' => 30,

    /**
     * Token generator class must implement TokenGeneratorInterface
     */
    'token_generator' => \Sh4msi\FilamentOtp\Utility\TokenGenerator::class,

    /**
     * Token notification class
     */
    'token_notification' => \Sh4msi\FilamentOtp\Notifications\NotificationOTP::class,

    /**
     * Login confirmation page component
     *
     * If you want to change something, place your component here.
     */
    'confirm_token_component' => \Sh4msi\FilamentOtp\Http\Livewire\Auth\ConfirmOTP::class,

    'login_otp_component' => \Sh4msi\FilamentOtp\Http\Livewire\Auth\LoginOTP::class,

];
```

## Usage

Add the Sh4msi\FilamentOtp\Traits\OtpLogin trait to your User model(s):
```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Sh4msi\FilamentOtp\Traits\OtpLogin;

class User extends Authenticatable
{
    use OtpLogin;

    // ...
}
```

You can use the renderHook() method in the panel configuration object to display the "login with one-time password" button on the login page.
```php
use Filament\Panel;
use Illuminate\Contracts\View\View;
 
public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->renderHook(
            'panels::auth.login.form.after',
            fn (): View => View('filament-otp::livewire.login-otp-btn'),
        )
}
```

OR call by route
```php
route('filament-otp.login')
```


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Saeed Shamsi](https://github.com/sh4msi)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
