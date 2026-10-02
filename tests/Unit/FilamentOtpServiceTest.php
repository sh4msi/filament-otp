<?php

use Illuminate\Support\Facades\Session;
use Sh4msi\FilamentOtp\Facades\FilamentOtp as FilamentOtpFacade;
use Sh4msi\FilamentOtp\FilamentOtp;
use Sh4msi\FilamentOtp\Tests\Fixtures\User;

beforeEach(function () {
    $this->service = app(FilamentOtp::class);
    $this->user = User::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '09123456789',
    ]);
});

it('finds user by explicit login id', function () {
    $user = $this->service->getUser('john@example.com');

    expect($user)
        ->not->toBeNull()
        ->id->toBe($this->user->id)
        ->email->toBe('john@example.com');
});

it('finds user from session when no login id is passed', function () {
    Session::put('loginId', 'john@example.com');

    $user = $this->service->getUser();

    expect($user)
        ->not->toBeNull()
        ->id->toBe($this->user->id);
});

it('returns null when user does not exist', function () {
    $user = $this->service->getUser('unknown@example.com');

    expect($user)->toBeNull();
});

it('returns null when no login id is given and session is empty', function () {
    Session::forget('loginId');

    $user = $this->service->getUser();

    expect($user)->toBeNull();
});

it('finds user using custom login_key configuration', function () {
    config()->set('filament-otp.login_key', 'phone');
    $service = new FilamentOtp;

    $user = $service->getUser('09123456789');

    expect($user)
        ->not->toBeNull()
        ->id->toBe($this->user->id)
        ->phone->toBe('09123456789');
});

it('returns token expiry from configuration', function () {
    config()->set('filament-otp.token_expiry', 20);

    expect($this->service->TokenExpiry())->toBe(20);
});

it('proxies methods through FilamentOtp facade', function () {
    Session::put('loginId', 'john@example.com');

    expect(FilamentOtpFacade::getUser())
        ->not->toBeNull()
        ->id->toBe($this->user->id);

    expect(FilamentOtpFacade::TokenExpiry())->toBe(config('filament-otp.token_expiry'));
});
