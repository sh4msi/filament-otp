<?php

namespace Sh4msi\FilamentOtp;

use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class FilamentOtp
{
    protected string $model;

    public function __construct()
    {
        $this->model = config('filament-otp.user_model');
    }

    public function getUser(?string $loginId = null): ?Model
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

    public function getCurrentPanelId(): ?string
    {
        try {
            $panel = Filament::getCurrentPanel() ?? Filament::getDefaultPanel();

            return $panel?->getId();
        } catch (\Throwable) {
            return null;
        }
    }

    public function resolvePanel(?string $panelId = null): ?Panel
    {
        try {
            if ($panelId) {
                return Filament::getPanel($panelId);
            }

            return Filament::getCurrentPanel() ?? Filament::getDefaultPanel();
        } catch (\Throwable) {
            return null;
        }
    }

    public function getLoginRouteName(?string $panelId = null): string
    {
        $panelId = $panelId ?? $this->getCurrentPanelId();

        if ($panelId && Route::has("filament-otp.{$panelId}.login")) {
            return "filament-otp.{$panelId}.login";
        }

        return $panelId ? "filament-otp.{$panelId}.login" : 'filament-otp.login';
    }

    public function getConfirmRouteName(?string $panelId = null): string
    {
        $panelId = $panelId ?? $this->getCurrentPanelId();

        if ($panelId && Route::has("filament-otp.{$panelId}.confirm")) {
            return "filament-otp.{$panelId}.confirm";
        }

        return $panelId ? "filament-otp.{$panelId}.confirm" : 'filament-otp.confirm';
    }

    public function getLoginUrl(?string $panelId = null): string
    {
        $routeName = $this->getLoginRouteName($panelId);

        if (Route::has($routeName)) {
            return route($routeName);
        }

        if (Route::has('filament-otp.login')) {
            return route('filament-otp.login');
        }

        $panel = $this->resolvePanel($panelId);
        if ($panel && $panel->hasLogin()) {
            return $panel->getLoginUrl();
        }

        return url('/');
    }

    public function getConfirmUrl(?string $panelId = null): string
    {
        $routeName = $this->getConfirmRouteName($panelId);

        if (Route::has($routeName)) {
            return route($routeName);
        }

        if (Route::has('filament-otp.confirm')) {
            return route('filament-otp.confirm');
        }

        return url('/');
    }

    private function getLoginKey(): string
    {
        return config('filament-otp.login_key');
    }
}
