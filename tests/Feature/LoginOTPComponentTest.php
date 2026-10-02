<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use Sh4msi\FilamentOtp\Events\TokenSent;
use Sh4msi\FilamentOtp\Http\Livewire\Auth\LoginOTP;
use Sh4msi\FilamentOtp\Tests\Fixtures\User;

beforeEach(function () {
    $this->user = User::create([
        'name' => 'Alice Test',
        'email' => 'alice@example.com',
    ]);
});

it('renders the login-otp component successfully', function () {
    Livewire::test(LoginOTP::class)
        ->assertSuccessful()
        ->assertSee(__('filament-otp::filament-otp.login.heading'));
});

it('redirects authenticated user to panel url on mount', function () {
    Filament::auth()->login($this->user);

    Livewire::test(LoginOTP::class)
        ->assertRedirect(Filament::getUrl());
});

it('validates loginId field is required', function () {
    Livewire::test(LoginOTP::class)
        ->fillForm(['loginId' => ''])
        ->call('authenticate')
        ->assertHasFormErrors(['loginId' => 'required']);
});

it('validates email format when login_key is email', function () {
    config()->set('filament-otp.login_key', 'email');
    config()->set('filament-otp.login_key_rule', ['required', 'email']);

    Livewire::test(LoginOTP::class)
        ->fillForm(['loginId' => 'invalid-email-format'])
        ->call('authenticate')
        ->assertHasFormErrors(['loginId' => 'email']);
});

it('fails validation when user is not found in database', function () {
    Livewire::test(LoginOTP::class)
        ->fillForm(['loginId' => 'notfound@example.com'])
        ->call('authenticate')
        ->assertHasErrors(['loginId']);
});

it('stores loginId in session, fires TokenSent event and redirects on success', function () {
    Event::fake();

    Livewire::test(LoginOTP::class)
        ->fillForm(['loginId' => 'alice@example.com'])
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertRedirect(route('filament-otp.confirm'));

    expect(Session::get('loginId'))->toBe('alice@example.com');

    Event::assertDispatched(TokenSent::class, function ($event) {
        return $event->user->id === $this->user->id;
    });
});

it('applies rate limiting on repeated authentication calls', function () {
    config()->set('filament-otp.rate_limit_count', 2);
    config()->set('filament-otp.rate_limit_decay_seconds', 60);

    $component = Livewire::test(LoginOTP::class)
        ->fillForm(['loginId' => 'alice@example.com']);

    // Attempt 1
    $component->call('authenticate');

    // Attempt 2
    $component->call('authenticate');

    // Attempt 3 should be blocked by rate limit
    $component->call('authenticate')
        ->assertHasErrors(['loginId']);
});
