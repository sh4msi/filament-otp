<?php

namespace Sh4msi\FilamentOtp\Tests\Fixtures;

use Filament\Panel;
use Filament\PanelProvider;
use Sh4msi\FilamentOtp\FilamentOtpPlugin;

class TestPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path('app')
            ->login()
            ->plugin(FilamentOtpPlugin::make());
    }
}
