<?php

namespace Sh4msi\FilamentOtp;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Sh4msi\FilamentOtp\Events\TokenSent;
use Sh4msi\FilamentOtp\Listeners\TokenListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TokenSent::class => [
            TokenListener::class,
        ],
    ];
}
