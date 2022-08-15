<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <x-label for="name" :value="__('Name')" />

                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-label for="email" :value="__('Email')" />

                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Password')" />

                <x-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-label for="password_confirmation" :value="__('Confirm Password')" />

                <x-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required />
            </div>

            <div class="mt-4">
                <x-label for="phone_number" :value="__('Phone Number')" />

                <x-input id="phone_number" class="block mt-1 w-full"
                         type="text"
                         name="phone_number" required />
            </div>

            <div class="mt-4">
                <x-label for="notification_method" :value="__('Notification Method')" />

                <x-label for="notification_web" :value="__('Web Only')" />
                <x-input id="notification_web" class="block mt-1"
                         type="radio"
                         name="notification_method" value="web" />

                <x-label for="notification_web" :value="__('Voice')" />
                <x-input id="notification_voice" class="block mt-1"
                         type="radio"
                         name="notification_method" value="voice" />

                <x-label for="notification_sms" :value="__('SMS')" />
                <x-input id="notification_sms" class="block mt-1"
                         type="radio"
                         name="notification_method" value="sms" />

                <x-label for="notification_whatsapp" :value="__('WhatsApp')" />
                <x-input id="notification_whatsapp" class="block mt-1"
                         type="radio"
                         name="notification_method" value="whatsapp" />

                <x-label for="notification_whatsapp" :value="__('Viber')" />
                <x-input id="notification_whatsapp" class="block mt-1"
                         type="radio"
                         name="notification_method" value="viber" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ml-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
