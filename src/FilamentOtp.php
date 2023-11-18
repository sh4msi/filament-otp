<?php

namespace Sh4msi\FilamentOtp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class FilamentOtp
{
    protected string $model;

    public function __construct()
    {
        $this->model = config('filament-otp.user_model');
    }

    public function getUser(string $loginId = null): ?Model
    {
        $loginId = $loginId ?: Session::get('loginId');

        if (! $loginId) {
            return null;
        }

        return $this->model::query()
            ->where($this->getLoginKey(), $loginId)
            ->first();
    }

    public function TokenExpiry(): int
    {
        return config('filament-otp.token_expiry');
    }

    private function getLoginKey(): string
    {
        return config('filament-otp.login_key');
    }
}
