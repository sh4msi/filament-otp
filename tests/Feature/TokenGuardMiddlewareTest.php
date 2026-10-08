<?php

use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Session;
use Sh4msi\FilamentOtp\Http\Middleware\TokenGuard;
use Sh4msi\FilamentOtp\Tests\Fixtures\User;

beforeEach(function () {
    $this->middleware = new TokenGuard;
});

it('allows request through when no token exists in session', function () {
    $request = Request::create('/app', 'GET');
    $request->setLaravelSession(app('session.store'));
    Session::forget('token');

    $response = $this->middleware->handle($request, function ($req) {
        return response('next_called');
    });

    expect($response->getContent())->toBe('next_called');
});

it('clears token, logs out user and redirects to root when token is expired', function () {
    $user = User::create([
        'name' => 'Expired User',
        'email' => 'expired@example.com',
    ]);
    Filament::auth()->login($user);

    $request = Request::create('/app', 'GET');
    $request->setLaravelSession(app('session.store'));
    Session::put('token', '12345');
    Session::put('token_expiry', now()->subMinute()->timestamp);

    $response = $this->middleware->handle($request, function () {
        return response('should_not_reach');
    });

    expect($response->getTargetUrl())->toBe(url('/'));
    expect(Session::has('token'))->toBeFalse();
    expect(Session::has('token_expiry'))->toBeFalse();
    expect(Filament::auth()->check())->toBeFalse();
});

it('allows request through when token is valid and route is a login route', function () {
    $request = Request::create('/app/login/otp', 'GET');
    $request->setLaravelSession(app('session.store'));
    Session::put('token', '12345');
    Session::put('token_expiry', now()->addMinutes(10)->timestamp);

    // Mock route naming
    $route = new Route('GET', '/app/login/otp', fn () => 'ok');
    $route->name('filament-otp.login');
    $request->setRouteResolver(fn () => $route);

    $response = $this->middleware->handle($request, function () {
        return response('login_allowed');
    });

    expect($response->getContent())->toBe('login_allowed');
});

it('redirects to confirm route when token is valid but accessed route is not a login route', function () {
    $request = Request::create('/app/dashboard', 'GET');
    $request->setLaravelSession(app('session.store'));
    Session::put('token', '12345');
    Session::put('token_expiry', now()->addMinutes(10)->timestamp);

    $route = new Route('GET', '/app/dashboard', fn () => 'dashboard');
    $route->name('filament.app.pages.dashboard');
    $request->setRouteResolver(fn () => $route);

    $response = $this->middleware->handle($request, function () {
        return response('should_not_reach');
    });

    expect($response->isRedirect(route('filament-otp.app.confirm')))->toBeTrue();
});
