<x-filament-panels::page.simple>
    <x-filament-panels::form wire:submit.prevent="authenticate">
        {{ $this->form }}

        <x-filament::button type="submit" form="authenticate" class="w-full">
            {{ __('filament-otp::filament-otp.confirm.buttons.submit.label') }}
        </x-filament::button>

        <div class="text-center">
            <a href="{{ route('filament.app.auth.login') }}" class="text-primary-600 hover:text-primary-700">
                <x-filament::button type="button" color="gray" class="text-sm">
                    {{ __('filament-otp::filament-otp.confirm.buttons.sign_in.label') }}
                </x-filament::button>
            </a>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page.simple>
