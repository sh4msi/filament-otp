<?php

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Sh4msi\FilamentOtp\Notifications\NotificationOTP;
use Sh4msi\FilamentOtp\Tests\Fixtures\User;

it('implements ShouldQueue interface', function () {
    $notification = new NotificationOTP('12345');

    expect($notification)->toBeInstanceOf(ShouldQueue::class);
});

it('uses mail delivery channel', function () {
    $user = new User(['email' => 'test@example.com']);
    $notification = new NotificationOTP('12345');

    expect($notification->via($user))->toBe(['mail']);
});

it('builds mail message with subject and token', function () {
    config()->set('app.name', 'Acme Panel');
    config()->set('filament-otp.token_expiry', 15);

    $user = new User(['email' => 'test@example.com']);
    $notification = new NotificationOTP('98765');
    $mail = $notification->toMail($user);

    expect($mail)->toBeInstanceOf(MailMessage::class);
    expect($mail->subject)->toBe('Your login code for Acme Panel');

    $rendered = implode("\n", $mail->introLines);
    expect($rendered)->toContain('98765');
    expect($rendered)->toContain('15 minutes');
});
