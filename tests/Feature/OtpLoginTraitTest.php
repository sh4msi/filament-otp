<?php

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Sh4msi\FilamentOtp\Notifications\NotificationOTP;
use Sh4msi\FilamentOtp\Tests\Fixtures\User;

it('sends default NotificationOTP through OtpLogin trait', function () {
    NotificationFacade::fake();

    $user = User::create([
        'name' => 'John',
        'email' => 'john@test.com',
    ]);

    $user->notifyOtpToken('11223');

    NotificationFacade::assertSentTo(
        $user,
        NotificationOTP::class,
        fn (NotificationOTP $n) => $n->token === '11223'
    );
});

it('supports custom notification class from configuration', function () {
    NotificationFacade::fake();

    $customNotificationClass = new class('dummy') extends Notification
    {
        public function __construct(public string $token) {}

        public function via($notifiable): array
        {
            return ['mail'];
        }
    };

    config()->set('filament-otp.token_notification', get_class($customNotificationClass));

    $user = User::create([
        'name' => 'Custom User',
        'email' => 'custom@test.com',
    ]);

    $user->notifyOtpToken('99887');

    NotificationFacade::assertSentTo(
        $user,
        get_class($customNotificationClass),
        fn ($n) => $n->token === '99887'
    );
});
