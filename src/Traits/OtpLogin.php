<?php

namespace Sh4msi\FilamentOtp\Traits;


trait OtpLogin
{
    public function notifyOtpToken(string $token)
    {
        $notification = config('filament-otp.token_notification');
        $this->notify(new $notification($token));
    }
}
