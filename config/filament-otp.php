<?php

// config for Sh4msi/FilamentOtp

return [
    /**
     * The authentication model to use.
     */
    'user_model' => \App\Models\User::class,

    /**
     * login columns
     */
    'login_key' => 'email',

    'login_key_rule' => ['email', 'required'],

    /**
     * token resend countdown time
     */
    'resent_token_countdown_time' => 20,

    /**
     * token count
     */
    'token_count' => 5,

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
    'token_generator' => \Sh4msi\FilamentOtp\Utility\TokenGenerator::class,

    /**
     * Token notification class
     */
    'token_notification' => \Sh4msi\FilamentOtp\Notifications\NotificationOTP::class,

    /**
     * Login confirmation page component
     *
     * If you want to change something, place your component here.
     */
    'confirm_token_component' => \Sh4msi\FilamentOtp\Http\Livewire\Auth\ConfirmOTP::class,

    'login_otp_component' => \Sh4msi\FilamentOtp\Http\Livewire\Auth\LoginOTP::class,

];
