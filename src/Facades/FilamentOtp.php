<?php

namespace Sh4msi\FilamentOtp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Sh4msi\FilamentOtp\FilamentOtp
 */
class FilamentOtp extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Sh4msi\FilamentOtp\FilamentOtp::class;
    }
}
