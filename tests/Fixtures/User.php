<?php

namespace Sh4msi\FilamentOtp\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Sh4msi\FilamentOtp\Traits\OtpLogin;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use OtpLogin;

    protected $table = 'users';

    protected $guarded = [];
}
