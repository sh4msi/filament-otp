<div>
    <br>
    <x-filament::button wire:click.prevent="resentToken" id="countdown" color="gray" class="mt-2 w-full">
        {{ __('filament-otp::filament-otp.confirm.buttons.resend.label') }}
    </x-filament::button>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('startCountdown', function (duration) {
                let timer = duration, minutes, seconds;
                let element = document.getElementById('countdown');

                function formatTime(time) {
                    return time < 10 ? "0" + time : time;
                }

                function updateCountdown() {
                    minutes = parseInt(timer / 60, 10);
                    seconds = parseInt(timer % 60, 10);

                    element.textContent = formatTime(minutes) + ":" + formatTime(seconds);
                    element.setAttribute('disabled', '');

                    if (--timer < 0) {
                        clearInterval(countdown);
                        element.textContent = 'ارسال مجدد';
                        element.removeAttribute('disabled');
                    }
                }

                updateCountdown();
                let countdown = setInterval(updateCountdown, 1000);
            });

        });
    </script>

</div>
