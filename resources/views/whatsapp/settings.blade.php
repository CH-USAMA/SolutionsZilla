<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('WhatsApp Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(Auth::user()->isSuperAdmin())
                <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <form method="GET" action="{{ route('whatsapp.settings') }}" class="flex items-end gap-4">
                        <div class="flex-1">
                            <x-input-label for="clinic_id" value="Select Clinic to Manage Settings" />
                            <select id="clinic_id" name="clinic_id" onchange="this.form.submit()"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Choose Clinic --</option>
                                @foreach($clinics as $clinic)
                                    <option value="{{ $clinic->id }}" {{ $selectedClinicId == $clinic->id ? 'selected' : '' }}>
                                        {{ $clinic->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('success') }}
                    </div>
                @endif

                @if(Auth::user()->isSuperAdmin() && !$selectedClinicId)
                    <div class="text-center py-8">
                        <p class="text-gray-500 italic">Please select a clinic above to view or manage WhatsApp settings.
                        </p>
                    </div>
                @else
                    <form method="POST" action="{{ route('whatsapp.settings.update') }}" class="space-y-6">
                        @csrf
                        @if($selectedClinicId)
                            <input type="hidden" name="clinic_id" value="{{ $selectedClinicId }}">
                        @endif

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

                        <!-- Message Configuration -->
                        <div class="mt-8 border-t border-gray-100 pt-8"
                            x-data="{ messageType: '{{ $settings->message_type ?? 'template' }}' }">
                            <h3 class="text-lg font-medium text-brand-900 mb-4">{{ __('Message Customization') }}</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Message Type -->
                                <div>
                                    <x-input-label for="message_type" :value="__('Reminder Message Type')" />
                                    <select id="message_type" name="message_type" x-model="messageType"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="template">
                                            {{ __('WhatsApp Template (Required by Meta for first message)') }}
                                        </option>
                                        <option value="text">{{ __('Simple Text (Flexible, no Meta approval needed)') }}
                                        </option>
                                    </select>
                                    <p class="mt-2 text-xs text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        {{ __('Note: Simple Text works best after a patient has replied to your clinic.') }}
                                    </p>
                                </div>

                                <!-- Template Name (Only if template selected) -->
                                <div x-show="messageType === 'template'">
                                    <x-input-label for="default_template" :value="__('Meta Template Name')" />
                                    <x-text-input id="default_template" name="default_template" type="text"
                                        class="mt-1 block w-full" :value="old('default_template', $settings->default_template)" placeholder="e.g., appointment_reminder" />
                                    <x-input-error :messages="$errors->get('default_template')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Custom Message Text (Only if Text selected) -->
                            <div class="mt-6" x-show="messageType === 'text'">
                                <div class="flex justify-between items-end">
                                    <x-input-label for="custom_message" :value="__('Custom Message Content')" />
                                    <div class="text-xs text-brand-600 font-medium cursor-help"
                                        title="Copy and paste these tags into your message">
                                        {{ __('Available Tags:') }}
                                        <span
                                            class="bg-brand-50 px-1 py-0.5 rounded border border-brand-100">{patient_name}</span>
                                        <span
                                            class="bg-brand-50 px-1 py-0.5 rounded border border-brand-100">{clinic_name}</span>
                                        <span
                                            class="bg-brand-50 px-1 py-0.5 rounded border border-brand-100">{doctor_name}</span>
                                        <span class="bg-brand-50 px-1 py-0.5 rounded border border-brand-100">{date}</span>
                                        <span class="bg-brand-50 px-1 py-0.5 rounded border border-brand-100">{time}</span>
                                    </div>
                                </div>
                                <textarea id="custom_message" name="custom_message" rows="6"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm font-mono text-sm leading-relaxed"
                                    placeholder="Write your custom message here...">{{ old('custom_message', $settings->custom_message) }}</textarea>
                                <x-input-error :messages="$errors->get('custom_message')" class="mt-2" />
                                <p class="mt-2 text-xs text-gray-500 italic">
                                    {{ __('Your message will be automatically populated with patient and appointment details.') }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-8 border-t border-gray-100 pt-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="reminder_hours_before" :value="__('Send Reminder Before (Hours)')" />
                                    <x-text-input id="reminder_hours_before" name="reminder_hours_before" type="number"
                                        class="mt-1 block w-full" :value="old('reminder_hours_before', $settings->reminder_hours_before ?? 24)" required />
                                    <x-input-error :messages="$errors->get('reminder_hours_before')" class="mt-2" />
                                </div>

                                <div class="flex items-center mt-6">
                                    <input id="is_active" name="is_active" type="checkbox" value="1"
                                        class="rounded border-gray-300 text-brand-600 shadow-sm focus:ring-brand-500" {{ old('is_active', $settings->is_active) ? 'checked' : '' }}>
                                    <x-input-label for="is_active" :value="__('Enable WhatsApp Reminders')" class="ml-2" />
                                </div>
                            </div>
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
                @endif
            </div>
        </div>
    </div>
</x-app-layout>