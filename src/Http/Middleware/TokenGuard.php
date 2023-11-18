<?php

namespace Sh4msi\FilamentOtp\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;

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

        return to_route('filament-otp.confirm');
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
        $request->session()->forget(['token', 'token_expiry']);
    }

    private function isLoginRoute(Request $request): bool
    {
        return $request->route()->named('filament-otp.*');
        //        return $request->route()->named('filament.*.auth.login.*');
    }
}
