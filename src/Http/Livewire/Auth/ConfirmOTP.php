<?php

namespace Sh4msi\FilamentOtp\Http\Livewire\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\View;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Sh4msi\FilamentOtp\Events\TokenSent;
use Sh4msi\FilamentOtp\FilamentOtp;

/**
 * @property Form $form
 */
#[\AllowDynamicProperties]
class ConfirmOTP extends SimplePage
{
    use InteractsWithForms;
    use WithRateLimiting;

    public $token = '';

    public function getView(): string
    {
        return 'filament-otp::livewire.confirm-otp';
    }

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        if (! session()->has('loginId')) {
            $this->redirect($this->getLoginUrl());

            return;
        }

        $this->form->fill();
    }

    public function getLoginUrl(): string
    {
        $panel = Filament::getCurrentOrDefaultPanel();

        if ($panel && method_exists($panel, 'hasLogin') && $panel->hasLogin()) {
            return $panel->getLoginUrl();
        }

        if (Route::has('filament-otp.login')) {
            return route('filament-otp.login');
        }

        return url('/');
    }

    protected function getFormSchema(): array
    {
        $gridClass = class_exists(Grid::class)
            ? Grid::class
            : 'Filament\Forms\Components\Grid';

        $viewClass = class_exists(View::class)
            ? View::class
            : 'Filament\Forms\Components\View';

        return [
            $gridClass::make([
                'default' => 12,
            ])
                ->columnSpanFull()
                ->schema([
                    TextInput::make('token')
                        ->label(__('filament-otp::filament-otp.confirm.fields.token.label'))
                        ->required()
                        ->columnSpan(session()->has('token') ? 8 : 12)
                        ->numeric(config('filament-otp.token_type') == 'numeric')
                        ->length(config('filament-otp.token_count')),

                    $viewClass::make('filament-otp::livewire.resend-token')
                        ->visible(session()->has('token'))
                        ->columnSpan(4),
                ]),
        ];
    }

    /**
     * @throws ValidationException
     */
    public function authenticate()
    {
        $this->doRateLimit();

        $data = $this->form->getState();
        $tokenExpiry = session()->get('token_expiry');

        if ($tokenExpiry < now()->timestamp) {
            $this->handleExpiredToken();

            return $this->redirect($this->getLoginUrl());
        }

        $this->hasValidToken((string) $data['token']);
        $user = app(FilamentOtp::class)->getUser();
        Filament::auth()->login($user);
        $this->clearSession(request());

        if (
            $user instanceof FilamentUser &&
            ! $user->canAccessPanel(Filament::getCurrentPanel())
        ) {
            Filament::auth()->logout();
            $this->throwFailureValidationException();
        }

        session()->regenerate();

        $loginResponseClass = interface_exists(\Filament\Auth\Http\Responses\Contracts\LoginResponse::class)
            ? \Filament\Auth\Http\Responses\Contracts\LoginResponse::class
            : (interface_exists(LoginResponse::class)
                ? LoginResponse::class
                : LoginResponse::class);

        return app($loginResponseClass);
    }

    /**
     * @throws ValidationException
     */
    public function resentToken()
    {
        $this->doResentRateLimit();
        $this->dispatchCountdown();

        $user = app(FilamentOtp::class)->getUser();

        if (! $user) {
            $this->redirect($this->getLoginUrl());

            return;
        }

        event(new TokenSent($user));

        Notification::make()
            ->title(__('filament-otp::filament-otp.confirm.messages.token_resent'))
            ->seconds(12)
            ->success()
            ->send();
    }

    public function getTitle(): string | Htmlable
    {
        return __('filament-otp::filament-otp.confirm.heading');
    }

    // ------ private methods ------

    /**
     * @throws ValidationException
     */
    private function doRateLimit()
    {
        try {
            $this->rateLimit(
                config('filament-otp.rate_limit_count', 3),
                config('filament-otp.rate_limit_decay_seconds', 60)
            );
        } catch (TooManyRequestsException $exception) {
            $throttledKey = Lang::has('filament-panels::auth/pages/login.notifications.throttled.title')
                ? 'filament-panels::auth/pages/login.notifications.throttled'
                : 'filament-panels::pages/auth/login.notifications.throttled';

            Notification::make()
                ->title(__("{$throttledKey}.title", [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __($throttledKey) ?: [])
                    ? __("{$throttledKey}.body", [
                        'seconds' => $exception->secondsUntilAvailable,
                        'minutes' => ceil($exception->secondsUntilAvailable / 60),
                    ]) : null)
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'token' => __("{$throttledKey}.title", [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]),
            ]);
        }
    }

    /**
     * @throws ValidationException
     */
    private function doResentRateLimit()
    {
        try {
            $this->rateLimit(
                config('filament-otp.rate_limit_count', 3),
                config('filament-otp.rate_limit_decay_seconds', 60)
            );
        } catch (TooManyRequestsException $exception) {
            $throttledBodyKey = Lang::has('filament-panels::auth/pages/login.notifications.throttled.body')
                ? 'filament-panels::auth/pages/login.notifications.throttled.body'
                : 'filament-panels::pages/auth/login.notifications.throttled.body';

            $throttledKey = Lang::has('filament-panels::auth/pages/login.notifications.throttled')
                ? 'filament-panels::auth/pages/login.notifications.throttled'
                : 'filament-panels::pages/auth/login.notifications.throttled';

            Notification::make()
                ->title(__('filament-otp::filament-otp.confirm.messages.throttled', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __($throttledKey) ?: [])
                    ? __($throttledBodyKey, [
                        'seconds' => $exception->secondsUntilAvailable,
                        'minutes' => ceil($exception->secondsUntilAvailable / 60),
                    ]) : null)
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'token' => __('filament-otp::filament-otp.confirm.messages.throttled', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]),
            ]);
        }
    }

    private function dispatchCountdown(): void
    {
        $this->dispatch(
            'startCountdown',
            config('filament-otp.resent_token_countdown_time')
        );
    }

    /**
     * @throws ValidationException
     */
    private function hasValidToken(string $token): void
    {
        $sessionToken = (string) session()->get('token');

        if ($sessionToken === '' || ! hash_equals($sessionToken, $token)) {
            throw ValidationException::withMessages([
                'token' => [__('filament-otp::validation.wrong_token')],
            ]);
        }
    }

    private function handleExpiredToken(): void
    {
        if (Filament::auth()->check()) {
            Filament::auth()->logout();
        }

        $this->clearSession(request());
        Notification::make()
            ->title(__('filament-otp::validation.expired_token'))
            ->seconds(9)
            ->danger()
            ->send();
    }

    private function clearSession($request = null): void
    {
        $keys = ['token', 'token_expiry', 'loginId'];

        if ($request && method_exists($request, 'hasSession') && $request->hasSession()) {
            $request->session()->forget($keys);
        } else {
            session()->forget($keys);
        }
    }

    /**
     * @throws ValidationException
     */
    private function throwFailureValidationException()
    {
        $failedKey = Lang::has('filament-panels::auth/pages/login.messages.failed')
            ? 'filament-panels::auth/pages/login.messages.failed'
            : 'filament-panels::pages/auth/login.messages.failed';

        throw ValidationException::withMessages([
            'loginId' => __($failedKey),
        ]);
    }
}
