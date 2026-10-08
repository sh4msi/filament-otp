<?php

it('has all expected default configuration keys', function () {
    $config = config('filament-otp');

    expect($config)->toHaveKeys([
        'user_model',
        'login_key',
        'login_key_rule',
        'resent_token_countdown_time',
        'token_count',
        'token_type',
        'token_expiry',
        'rate_limit_count',
        'rate_limit_decay_seconds',
        'token_generator',
        'token_notification',
        'confirm_token_component',
        'login_otp_component',
        'panels',
    ]);
});

it('has valid translation keys for English and Persian', function () {
    $keys = [
        'filament-otp::filament-otp.login.heading',
        'filament-otp::filament-otp.login.fields.loginId.label',
        'filament-otp::filament-otp.login.messages.token_sent',
        'filament-otp::filament-otp.confirm.heading',
        'filament-otp::filament-otp.confirm.fields.token.label',
        'filament-otp::filament-otp.confirm.messages.token_resent',
        'filament-otp::validation.wrong_token',
        'filament-otp::validation.expired_token',
    ];

    foreach (['en', 'fa'] as $locale) {
        app()->setLocale($locale);

        foreach ($keys as $key) {
            $translation = __($key);
            expect($translation)
                ->toBeString()
                ->not->toBeEmpty()
                ->not->toBe($key, "Translation key {$key} is missing in locale {$locale}");
        }
    }
});
