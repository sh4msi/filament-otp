<?php

use App\Models\User;
use Sh4msi\FilamentOtp\Http\Livewire\Auth\ConfirmOTP;
use Sh4msi\FilamentOtp\Http\Livewire\Auth\LoginOTP;
use Sh4msi\FilamentOtp\Notifications\NotificationOTP;
use Sh4msi\FilamentOtp\Utility\TokenGenerator;

// config for Sh4msi/FilamentOtp

return [
    /**
     * The authentication model to use.
     */
    'user_model' => User::class,

    /**
     * login columns
     */
    'login_key' => 'email',

    'login_key_rule' => ['email', 'required'],

    /**
     * token resend countdown time
     */
    'resent_token_countdown_time' => 120,

    /**
     * token count
     */
    'token_count' => 6,

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
    'token_generator' => TokenGenerator::class,

    /**
     * Token notification class
     */
    'token_notification' => NotificationOTP::class,

    /**
     * Login confirmation page component
     *
     * If you want to change something, place your component here.
     */
    'confirm_token_component' => ConfirmOTP::class,

    'login_otp_component' => LoginOTP::class,

];
