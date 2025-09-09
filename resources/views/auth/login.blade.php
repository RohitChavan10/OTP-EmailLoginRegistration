<x-guest-layout>
    <h2 class="text-2xl font-bold mb-4">Login with OTP</h2>

    <!-- Phone -->
    <div class="mb-4 flex items-center gap-2">
        <div class="flex-1">
            <x-input-label for="phone" :value="__('Phone')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" required autofocus />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>
        <button type="button" id="send-otp-btn" class="bg-blue-500 text-black px-4 py-2 rounded mt-6">
            Send OTP
        </button>
    </div>

    <!-- OTP Form -->
    <form id="otp-login-form" method="POST" action="{{ route('otp.login.verify') }}">
        @csrf

        <input type="hidden" id="hidden-phone" name="phone">

        <!-- OTP -->
        <div class="mb-4">
            <x-input-label for="code" :value="__('Enter OTP')" />
            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" required />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <p id="otp-timer" class="text-sm text-gray-600 mb-2"></p>

        <x-primary-button class="w-full">
            {{ __('Login') }}
        </x-primary-button>
    </form>

    <script>
        let otpTimer;
        let countdown = 300; // 5 minutes

        document.getElementById('send-otp-btn').addEventListener('click', function() {
            const phone = document.getElementById('phone').value.trim();
            if (!phone) {
                alert('Please enter your phone number.');
                return;
            }

            // Send OTP
            fetch("{{ route('otp.login.send') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({ phone: phone })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                alert(data.message || 'OTP sent!');
                document.getElementById('hidden-phone').value = phone;
                startOtpTimer();
            })
            .catch(err => {
                console.error(err);
                alert('Error sending OTP');
            });
        });

        function startOtpTimer() {
            countdown = 300;
            const timerEl = document.getElementById('otp-timer');
            clearInterval(otpTimer);

            otpTimer = setInterval(() => {
                let minutes = Math.floor(countdown / 60);
                let seconds = countdown % 60;
                timerEl.textContent = `OTP expires in ${minutes}:${seconds.toString().padStart(2,'0')}`;
                countdown--;

                if (countdown < 0) {
                    clearInterval(otpTimer);
                    timerEl.textContent = 'OTP expired. Please resend.';
                }
            }, 1000);
        }
    </script>
</x-guest-layout>
