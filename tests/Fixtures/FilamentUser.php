<?php

namespace Sh4msi\FilamentOtp\Tests\Fixtures;

use Filament\Models\Contracts\FilamentUser as FilamentUserContract;
use Filament\Panel;

class FilamentUser extends User implements FilamentUserContract
{
    public static bool $canAccessPanel = true;

    public function canAccessPanel(Panel $panel): bool
    {
        return static::$canAccessPanel;
    }
}
