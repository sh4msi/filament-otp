<?php

namespace Sh4msi\FilamentOtp\Commands;

use Illuminate\Console\Command;

class FilamentOtpCommand extends Command
{
    public $signature = 'filament-otp';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
