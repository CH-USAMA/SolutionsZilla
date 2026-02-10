<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('WhatsApp Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('whatsapp.settings.update') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="phone_number_id" value="WhatsApp Phone Number ID" />
                        <x-text-input id="phone_number_id" name="phone_number_id" type="text" class="mt-1 block w-full"
                            :value="old('phone_number_id', $settings->phone_number_id)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('phone_number_id')" />
                        <p class="mt-1 text-sm text-gray-500">Found in your Meta App Dashboard under WhatsApp > Setup.
                        </p>
                    </div>

                    <div>
                        <x-input-label for="access_token" value="Permanent Access Token" />
                        <x-text-input id="access_token" name="access_token" type="password" class="mt-1 block w-full"
                            :value="old('access_token', $settings->access_token)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('access_token')" />
                        <p class="mt-1 text-sm text-gray-500">Your Meta System User Access Token. It will be stored
                            encrypted.</p>
                    </div>

                    <div>
                        <x-input-label for="default_template" value="Default Template Name" />
                        <x-text-input id="default_template" name="default_template" type="text"
                            class="mt-1 block w-full" :value="old('default_template', $settings->default_template ?? 'appointment_reminder')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('default_template')" />
                        <p class="mt-1 text-sm text-gray-500">The name of the approved template in your Meta account.
                        </p>
                    </div>

                    <div>
                        <x-input-label for="reminder_hours_before" value="Send Reminder (Hours Before)" />
                        <x-text-input id="reminder_hours_before" name="reminder_hours_before" type="number"
                            class="mt-1 block w-full" :value="old('reminder_hours_before', $settings->reminder_hours_before ?? 24)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('reminder_hours_before')" />
                        <p class="mt-1 text-sm text-gray-500">Configure how many hours before the appointment the
                            message should be sent (e.g., 2 or 24).</p>
                    </div>

                    <div class="flex items-center">
                        <input id="is_active" name="is_active" type="checkbox" value="1"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_active', $settings->is_active) ? 'checked' : '' }}>
                        <x-input-label for="is_active" value="Enabled" class="ml-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save Settings') }}</x-primary-button>
                    </div>
                </form>

                <hr class="my-8">

                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Test Integration</h3>
                    <p class="text-sm text-gray-600 mb-4">Send a sample template message to
                        <strong>923038004684</strong> to verify your credentials.
                    </p>

                    <div class="space-y-4">
                        <form method="POST" action="{{ route('whatsapp.test') }}">
                            @csrf
                            <x-secondary-button type="submit">
                                {{ __('Send Individual Test Message') }}
                            </x-secondary-button>
                        </form>

                        <form method="POST" action="{{ route('whatsapp.test.appointment') }}">
                            @csrf
                            <x-secondary-button type="submit">
                                {{ __('Create Test Appointment for Scheduler') }}
                            </x-secondary-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>