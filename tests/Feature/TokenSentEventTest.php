<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Sh4msi\FilamentOtp\Events\TokenSent;
use Sh4msi\FilamentOtp\Listeners\TokenListener;
use Sh4msi\FilamentOtp\Notifications\NotificationOTP;
use Sh4msi\FilamentOtp\Tests\Fixtures\User;

beforeEach(function () {
    $this->user = User::create([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
    ]);
});

it('instantiates TokenSent event with user', function () {
    $event = new TokenSent($this->user);

    expect($event->user)->toBe($this->user);
});

it('is registered with TokenListener in EventServiceProvider', function () {
    Event::fake();

    event(new TokenSent($this->user));

    Event::assertDispatched(TokenSent::class, function ($event) {
        return $event->user->id === $this->user->id;
    });

    Event::assertListening(TokenSent::class, TokenListener::class);
});

it('generates session token and notifies user when TokenSent event is handled', function () {
    Notification::fake();
    config()->set('filament-otp.token_count', 6);
    config()->set('filament-otp.token_expiry', 10);
    config()->set('filament-otp.token_type', 'number');

    event(new TokenSent($this->user));

    expect(Session::has('token'))->toBeTrue();
    expect(Session::get('token'))
        ->toBeString()
        ->toHaveLength(6);

    expect(Session::has('token_expiry'))->toBeTrue();
    $expectedExpiry = now()->addMinutes(10)->timestamp;
    expect(abs(Session::get('token_expiry') - $expectedExpiry))->toBeLessThanOrEqual(2);

    Notification::assertSentTo(
        $this->user,
        NotificationOTP::class,
        function (NotificationOTP $notification) {
            return $notification->token === Session::get('token');
        }
    );
});
