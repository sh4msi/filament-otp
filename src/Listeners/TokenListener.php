<?php

namespace Sh4msi\FilamentOtp\Listeners;

use Illuminate\Support\Facades\Session;
use Sh4msi\FilamentOtp\FilamentOtp;
use Sh4msi\FilamentOtp\Utility\TokenGenerator;

class TokenListener
{
    protected TokenGenerator $generator;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->generator = new (config('filament-otp.token_generator'));
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(object $event): void
    {
        $token = $this->generator->getToken(config('filament-otp.token_count'));
        $tokenExpiry = app(FilamentOtp::class)->tokenExpiry();

        Session::put('token', $token);
        Session::put('token_expiry', now()->addMinutes($tokenExpiry)->timestamp);
        
        $event->user->notifyOtpToken($token);
    }
}
