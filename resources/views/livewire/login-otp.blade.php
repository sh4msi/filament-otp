<x-filament-panels::page.simple>
    <form wire:submit="authenticate" class="fi-form grid gap-y-6">
        {{ $this->form }}

        <x-filament::button type="submit" class="w-full">
            {{ __('filament-otp::filament-otp.login.buttons.submit.label') }}
        </x-filament::button>
    </form>
</x-filament-panels::page.simple>
