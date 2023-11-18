<?php

namespace Sh4msi\FilamentOtp\Http\Livewire\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;
use Sh4msi\FilamentOtp\Events\TokenSent;
use Sh4msi\FilamentOtp\FilamentOtp;

/**
 * @property Form $form
 */
class LoginOTP extends SimplePage
{
    use WithRateLimiting;

    protected static string $view = 'filament-otp::livewire.login';

    public $loginId = '';

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill();
    }

    /**
     * @throws ValidationException
     */
    public function authenticate()
    {
        $this->doRateLimit();

        $data = $this->form->getState();
        session()->put('loginId', $data['loginId']);
        $user = app(FilamentOtp::class)->getUser();

        if (! $user) {
            $this->throwFailureValidationException();
        }

        event(new TokenSent($user));

        Notification::make()
            ->title(__('filament-otp::filament-otp.login.messages.token_sent'))
            ->seconds(12)
            ->success()
            ->send();

        return to_route('filament-otp.confirm');
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('loginId')
                ->label(__('filament-otp::filament-otp.login.fields.loginId.label'))
                ->required()
                ->email(config('filament-otp.login_key') == 'email')
                ->rules(config('filament-otp.login_key_rule'))
                ->autofocus()
                ->autocomplete(config('filament-otp.login_key')),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return __('filament-otp::filament-otp.login.heading');
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

            Notification::make()
                ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists(
                    'body',
                    __('filament-panels::pages/auth/login.notifications.throttled') ?: []
                )
                    ? __('filament-panels::pages/auth/login.notifications.throttled.body', [
                        'seconds' => $exception->secondsUntilAvailable,
                        'minutes' => ceil($exception->secondsUntilAvailable / 60),
                    ]) : null)
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'loginId' => __('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]),
            ]);
        }
    }

    /**
     * @throws ValidationException
     */
    private function throwFailureValidationException()
    {
        throw ValidationException::withMessages([
            'loginId' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
