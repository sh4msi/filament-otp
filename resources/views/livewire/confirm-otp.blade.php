<x-filament-panels::page.simple>
    <form wire:submit="authenticate" class="fi-form grid gap-y-6">
        {{ $this->form }}

        <x-filament::button type="submit" class="w-full">
            {{ __('filament-otp::filament-otp.confirm.buttons.submit.label') }}
        </x-filament::button>

        <div class="text-center">
            <a href="{{ $this->getLoginUrl() }}" class="text-primary-600 hover:text-primary-700">
                <x-filament::button type="button" color="gray" class="text-sm">
                    {{ __('filament-otp::filament-otp.confirm.buttons.sign_in.label') }}
                </x-filament::button>
            </a>
        </div>
    </form>
</x-filament-panels::page.simple>
