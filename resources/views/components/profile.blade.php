<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    My Profile
                </div>
            </div>
        </div>
    </div>

    <div class="py-1">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('profile.update', $user->id) }}">
                    @csrf
                        <div>
                            <x-label for="name" :value="__('Name')" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="$user->name" required autofocus />
                        </div>

                        <div class="mt-4">
                            <x-label for="email" :value="__('Email')" />
                            <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="$user->email" required />
                        </div>

                        <div class="mt-4">
                            <x-label for="phone_number" :value="__('Phone Number')" />
                            <x-input id="phone_number" class="block mt-1 w-full"
                                     type="text"
                                     name="phone_number" :value="$user->phone_number" required />
                        </div>
                        <br>
                        <x-label for="notification_method" :value="__('Notification Method')" class="font-bold" />
                        <div class="flex mb-4 mt-4">
                            <div>
                                <x-label for="notification_web" :value="__('Web Only')" />
                                    <x-input id="notification_web" class="block mt-1"
                                             type="radio"
                                             name="notification_method" value="web" />
                            </div>

                            <div class="px-2">
                                <x-label for="notification_web" :value="__('Voice')" />
                                <x-input id="notification_voice" class="block mt-1"
                                         type="radio"
                                         name="notification_method" value="voice" checked />
                            </div>

                            <div class="px-2">
                                <x-label for="notification_sms" :value="__('SMS')" />
                                    <x-input id="notification_sms" class="block mt-1"
                                             type="radio"
                                             name="notification_method" value="sms" />
                            </div>

                            <div class="px-2">
                                <x-label for="notification_whatsapp" :value="__('WhatsApp')" />
                                <x-input id="notification_whatsapp" class="block mt-1"
                                         type="radio"
                                         name="notification_method" value="whatsapp" />
                            </div>

                            <div class="px-2">
                                <x-label for="notification_whatsapp" :value="__('Viber')" />
                                <x-input id="notification_whatsapp" class="block mt-1"
                                         type="radio"
                                         name="notification_method" value="viber" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('Update') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
                <div class="overflow-hidden">
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
