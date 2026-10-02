<div>
    <br>
    <x-filament::button wire:click.prevent="resentToken" id="countdown" color="gray" class="mt-2 w-full">
        {{ __('filament-otp::filament-otp.confirm.buttons.resend.label') }}
    </x-filament::button>

    <script>
        const registerCountdownListener = () => {
            Livewire.on('startCountdown', function (duration) {
                let timer = Array.isArray(duration) ? duration[0] : (typeof duration === 'object' && duration !== null && 'duration' in duration ? duration.duration : duration);
                let element = document.getElementById('countdown');
                if (!element) return;
                let resendLabel = {{ \Illuminate\Support\Js::from(__('filament-otp::filament-otp.confirm.buttons.resend.label')) }};

                function formatTime(time) {
                    return time < 10 ? "0" + time : time;
                }

                let countdown = null;
                function updateCountdown() {
                    let minutes = parseInt(timer / 60, 10);
                    let seconds = parseInt(timer % 60, 10);

                    element.textContent = formatTime(minutes) + ":" + formatTime(seconds);
                    element.setAttribute('disabled', '');

                    if (--timer < 0) {
                        if (countdown) clearInterval(countdown);
                        element.textContent = resendLabel;
                        element.removeAttribute('disabled');
                    }
                }

                updateCountdown();
                countdown = setInterval(updateCountdown, 1000);
            });
        };

        if (window.Livewire) {
            registerCountdownListener();
        } else {
            document.addEventListener('livewire:init', registerCountdownListener);
            document.addEventListener('livewire:initialized', registerCountdownListener);
        }
    </script>

</div>
