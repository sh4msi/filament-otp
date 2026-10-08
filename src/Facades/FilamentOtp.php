<?php

namespace Sh4msi\FilamentOtp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Database\Eloquent\Model|null getUser(?string $loginId = null)
 * @method static int TokenExpiry()
 * @method static string|null getCurrentPanelId()
 * @method static \Filament\Panel|null resolvePanel(?string $panelId = null)
 * @method static string getLoginRouteName(?string $panelId = null)
 * @method static string getConfirmRouteName(?string $panelId = null)
 * @method static string getLoginUrl(?string $panelId = null)
 * @method static string getConfirmUrl(?string $panelId = null)
 *
 * @see \Sh4msi\FilamentOtp\FilamentOtp
 */
class FilamentOtp extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Sh4msi\FilamentOtp\FilamentOtp::class;
    }
}
