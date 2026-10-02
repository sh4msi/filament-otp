<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use Sh4msi\FilamentOtp\Events\TokenSent;
use Sh4msi\FilamentOtp\FilamentOtp;
use Sh4msi\FilamentOtp\Http\Livewire\Auth\ConfirmOTP;
use Sh4msi\FilamentOtp\Tests\Fixtures\FilamentUser;
use Sh4msi\FilamentOtp\Tests\Fixtures\User;

beforeEach(function () {
    $this->user = User::create([
        'name' => 'Bob Test',
        'email' => 'bob@example.com',
    ]);
});

it('redirects to login when session has no loginId', function () {
    Session::forget('loginId');

    Livewire::test(ConfirmOTP::class)
        ->assertRedirect(route('filament.app.auth.login'));
});

it('redirects authenticated user to panel url on mount', function () {
    Session::put('loginId', 'bob@example.com');
    Filament::auth()->login($this->user);

    Livewire::test(ConfirmOTP::class)
        ->assertRedirect(Filament::getUrl());
});

it('renders component successfully when loginId exists in session', function () {
    Session::put('loginId', 'bob@example.com');
    Session::put('token', '123456');
    Session::put('token_expiry', now()->addMinutes(15)->timestamp);

    Livewire::test(ConfirmOTP::class)
        ->assertSuccessful()
        ->assertSee(__('filament-otp::filament-otp.confirm.heading'));
});

it('validates token field is required', function () {
    Session::put('loginId', 'bob@example.com');
    Session::put('token', '123456');
    Session::put('token_expiry', now()->addMinutes(15)->timestamp);

    Livewire::test(ConfirmOTP::class)
        ->fillForm(['token' => ''])
        ->call('authenticate')
        ->assertHasFormErrors(['token' => 'required']);
});

it('fails validation when wrong token is submitted', function () {
    Session::put('loginId', 'bob@example.com');
    Session::put('token', '123456');
    Session::put('token_expiry', now()->addMinutes(15)->timestamp);

    Livewire::test(ConfirmOTP::class)
        ->fillForm(['token' => '999999'])
        ->call('authenticate')
        ->assertHasErrors(['token']);
});

it('handles expired token by clearing session and redirecting to login', function () {
    Session::put('loginId', 'bob@example.com');
    Session::put('token', '123456');
    Session::put('token_expiry', now()->subMinutes(1)->timestamp);

    Livewire::test(ConfirmOTP::class)
        ->fillForm(['token' => '123456'])
        ->call('authenticate')
        ->assertRedirect(route('filament.app.auth.login'));

    expect(Session::has('token'))->toBeFalse();
    expect(Session::has('token_expiry'))->toBeFalse();
    expect(Filament::auth()->check())->toBeFalse();
});

it('authenticates user and clears token session on valid token', function () {
    Session::put('loginId', 'bob@example.com');
    Session::put('token', '123456');
    Session::put('token_expiry', now()->addMinutes(15)->timestamp);

    Livewire::test(ConfirmOTP::class)
        ->fillForm(['token' => '123456'])
        ->call('authenticate')
        ->assertHasNoFormErrors();

    expect(Filament::auth()->check())->toBeTrue();
    expect(Filament::auth()->user()->id)->toBe($this->user->id);
    expect(Session::has('token'))->toBeFalse();
    expect(Session::has('token_expiry'))->toBeFalse();
});

it('rejects authentication if user cannot access panel', function () {
    config()->set('filament-otp.user_model', FilamentUser::class);
    app()->forgetInstance(FilamentOtp::class);
    FilamentUser::$canAccessPanel = false;

    $restrictedUser = FilamentUser::create([
        'name' => 'Restricted User',
        'email' => 'restricted@example.com',
    ]);

    Session::put('loginId', 'restricted@example.com');
    Session::put('token', '543210');
    Session::put('token_expiry', now()->addMinutes(15)->timestamp);

    Livewire::test(ConfirmOTP::class)
        ->fillForm(['token' => '543210'])
        ->call('authenticate')
        ->assertHasErrors(['loginId']);

    expect(Filament::auth()->check())->toBeFalse();

    FilamentUser::$canAccessPanel = true;
});

it('dispatches countdown and fires TokenSent on resentToken', function () {
    Event::fake();
    Session::put('loginId', 'bob@example.com');
    config()->set('filament-otp.resent_token_countdown_time', 90);

    Livewire::test(ConfirmOTP::class)
        ->call('resentToken')
        ->assertDispatched('startCountdown', 90);

    Event::assertDispatched(TokenSent::class, function ($event) {
        return $event->user->id === $this->user->id;
    });
});

it('applies rate limiting on repeated resentToken calls', function () {
    Session::put('loginId', 'bob@example.com');
    config()->set('filament-otp.rate_limit_count', 2);
    config()->set('filament-otp.rate_limit_decay_seconds', 60);

    $component = Livewire::test(ConfirmOTP::class);

    // Call 1
    $component->call('resentToken');

    // Call 2
    $component->call('resentToken');

    // Call 3 should be throttled
    $component->call('resentToken')
        ->assertHasErrors(['token']);
});

it('does not allow empty token to bypass authentication when session token is empty', function () {
    Session::put('loginId', 'bob@example.com');
    Session::forget('token');
    Session::put('token_expiry', now()->addMinutes(15)->timestamp);

    Livewire::test(ConfirmOTP::class)
        ->fillForm(['token' => ''])
        ->call('authenticate')
        ->assertHasErrors(['token']);

    expect(Filament::auth()->check())->toBeFalse();
});

it('clears loginId from session upon successful authentication', function () {
    Session::put('loginId', 'bob@example.com');
    Session::put('token', '123456');
    Session::put('token_expiry', now()->addMinutes(15)->timestamp);

    Livewire::test(ConfirmOTP::class)
        ->fillForm(['token' => '123456'])
        ->call('authenticate')
        ->assertHasNoFormErrors();

    expect(Session::has('loginId'))->toBeFalse();
});
