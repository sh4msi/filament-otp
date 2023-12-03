<x-filament-panels::page.simple>
    <x-filament-panels::form wire:submit.prevent="authenticate">
        {{ $this->form }}

        <x-filament::button type="submit" form="authenticate" class="w-full">
            {{ __('filament-otp::filament-otp.login.buttons.submit.label') }}
        </x-filament::button>
    </x-filament-panels::form>
</x-filament-panels::page.simple>
