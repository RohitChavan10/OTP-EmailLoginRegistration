<x-guest-layout>
    <h2 class="text-2xl font-bold mb-4">OTP Registration</h2>

    @if(session('status'))
        <div class="mb-4 text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form id="otp-registration-form" method="POST" action="{{ route('otp.register.verify') }}">
        @csrf

        <!-- Name -->
        <div class="mb-4">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Phone -->
        <div class="mb-4 flex items-center gap-2">
            <div class="flex-1">
                <x-input-label for="phone" :value="__('Phone')" />
                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
            <button type="button" id="send-otp-btn" class="bg-blue-500 text-Black px-4 py-2 rounded mt-6">
                Send OTP
            </button>
        </div>

        <!-- OTP -->
        <div class="mb-4">
            <x-input-label for="code" :value="__('OTP')" />
            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" required />
            <p id="otp-timer" class="text-sm text-gray-600 mt-1"></p>
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <!-- Email (optional) -->
        <div class="mb-4">
            <x-input-label for="email" :value="__('Email (Optional)')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mb-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ml-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        let otpTimer;
        let countdown = 300; // 5 minutes in seconds

        document.getElementById('send-otp-btn').addEventListener('click', function() {
            const phone = document.getElementById('phone').value.trim();
            if (!phone) {
                alert('Please enter your phone number.');
                return;
            }

            // Disable button
            this.disabled = true;

            fetch("{{ route('otp.register.send') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({ phone: phone })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message || 'OTP sent!');
                startOtpTimer();
            })
            .catch(err => {
                console.error(err);
                alert('Error sending OTP');
                this.disabled = false;
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
                    document.getElementById('send-otp-btn').disabled = false;
                }
            }, 1000);
        }
    </script>
</x-guest-layout>
