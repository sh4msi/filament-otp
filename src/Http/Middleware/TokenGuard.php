<?php

namespace Sh4msi\FilamentOtp\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Sh4msi\FilamentOtp\Facades\FilamentOtp;

class TokenGuard
{
    /**
     * Handle an incoming request.
     *
     * if the user's session contains a token then we need to direct the user to the
     * token confirm route instead.
     * need to allow the user through to any login routes
     */
    public function handle(Request $request, Closure $next)
    {
        if (is_null($this->getTokenFromSession($request))) {
            return $next($request);
        }

        // token, still valid?
        if ($this->isTokenExpired($request)) {
            $this->clearToken($request);
            Filament::auth()->logout();

            return redirect('/');
        }

        if ($this->isLoginRoute($request)) {
            return $next($request);
        }

        $panel = Filament::getCurrentPanel() ?? Filament::getDefaultPanel();
        $confirmRoute = "filament-otp.{$panel->getId()}.confirm";

        if (Route::has($confirmRoute)) {
            return to_route($confirmRoute);
        }

        if (Route::has('filament-otp.confirm')) {
            return to_route('filament-otp.confirm');
        }

        return redirect()->to(FilamentOtp::getConfirmUrl());
    }

    private function getTokenFromSession(Request $request)
    {
        return $request->session()->get('token');
    }

    private function isTokenExpired(Request $request): bool
    {
        $expiry = $request->session()->get('token_expiry');

        return $expiry < now()->timestamp;
    }

    private function clearToken(Request $request): void
    {
        $request->session()->forget(['token', 'token_expiry', 'loginId']);
    }

    private function isLoginRoute(Request $request): bool
    {
        return (bool) $request->route()?->named('filament-otp.*');
    }
}
