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
                @if(Auth::user()->isSuperAdmin() && !$selectedClinicId)
                    <div class="text-center py-8">
                        <p class="text-gray-500 italic">Please select a clinic above to view or manage WhatsApp settings.
                        </p>
                    </div>
                @else
                    @if($selectedClinicId)
                        <input type="hidden" name="clinic_id" value="{{ $selectedClinicId }}">
                    @endif

                    @php
                        $clinic_to_check = Auth::user()->isSuperAdmin() ? \App\Models\Clinic::find($selectedClinicId) : Auth::user()->clinic;
                        $allowedProviders = $clinic_to_check?->allowed_whatsapp_providers ?? ['meta'];
                        $defaultProvider = in_array($settings->provider ?? 'meta', $allowedProviders) ? ($settings->provider ?? 'meta') : ($allowedProviders[0] ?? 'meta');
                    @endphp

                    <form method="POST" action="{{ route('whatsapp.settings.update') }}" class="space-y-6" x-data="{ 
                                                    provider: '{{ $defaultProvider }}',
                                                    messageType: '{{ $settings->message_type ?? 'template' }}'
                                                }" autocomplete="off">
                        @csrf

                        <!-- Provider Selection -->
                        @if(Auth::user()->isSuperAdmin() || count($allowedProviders) > 1)
                            <div class="mb-8">
                                <x-input-label for="provider" value="WhatsApp Provider" />
                                <select id="provider" name="provider" x-model="provider"
                                    @change="if(provider === 'js_api') { setTimeout(() => loadQrCode(), 100) }"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @foreach($allowedProviders as $p)
                                        <option value="{{ $p }}">
                                            {{ $p === 'meta' ? 'Meta WhatsApp Cloud API (Official)' : 'WhatsApp JS API (QR Code Scan)' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="provider" x-bind:value="provider" x-model="provider">
                        @endif

                        @if(count($allowedProviders) === 0 && !Auth::user()->isSuperAdmin())
                            <p class="mt-1 text-sm text-red-500 italic">No providers enabled for your clinic. Please contact support.</p>
                        @endif

                            <!-- Meta Specific Fields -->
                            <div x-show="provider === 'meta'" class="space-y-6">
                                @if(Auth::user()->isSuperAdmin())
                                    <div>
                                        <x-input-label for="phone_number_id" value="WhatsApp Phone Number ID" />
                                        <x-text-input id="phone_number_id" name="phone_number_id" type="text"
                                            class="mt-1 block w-full" :value="old('phone_number_id', $settings->phone_number_id)" />
                                        <x-input-error class="mt-2" :messages="$errors->get('phone_number_id')" />
                                    </div>

                                    <div>
                                        <x-input-label for="access_token" value="Permanent Access Token" />
                                        <x-text-input id="access_token" name="access_token" type="password"
                                            class="mt-1 block w-full" :value="old('access_token', $settings->access_token)" />
                                        <x-input-error class="mt-2" :messages="$errors->get('access_token')" />
                                    </div>
                                @else
                                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 italic text-gray-500 text-sm">
                                        Meta Cloud API parameters are managed by the administrator.
                                    </div>
                                @endif
                            </div>

                            <!-- JS API Specific Fields -->
                            <div x-show="provider === 'js_api'" class="space-y-6">
                                @if(Auth::user()->isSuperAdmin())
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <x-input-label for="js_api_url" value="JS API Server URL" />
                                            <x-text-input id="js_api_url" name="js_api_url" type="text" class="mt-1 block w-full"
                                                :value="old('js_api_url', $settings->js_api_url)"
                                                placeholder="https://api.whatsapp-js.com" />
                                            <x-input-error class="mt-2" :messages="$errors->get('js_api_url')" />
                                        </div>
                                        <div>
                                            <x-input-label for="js_session_id" value="Session ID (Custom Name)" />
                                            <x-text-input id="js_session_id" name="js_session_id" type="text"
                                                class="mt-1 block w-full" :value="old('js_session_id', $settings->js_session_id ?? 'clinic_' . ($selectedClinicId ?? Auth::user()->clinic_id))" />
                                            <x-input-error class="mt-2" :messages="$errors->get('js_session_id')" />
                                        </div>
                                    </div>
                                    <div>
                                        <x-input-label for="js_api_key" value="JS API Secret Key (Optional)" />
                                        <x-text-input id="js_api_key" name="js_api_key" type="password" class="mt-1 block w-full"
                                            :value="old('js_api_key', $settings->js_api_key)" />
                                        <x-input-error class="mt-2" :messages="$errors->get('js_api_key')" />
                                    </div>
                                @endif

                                <!-- Connection Status & QR Code -->
                                <div class="bg-indigo-50 p-6 rounded-xl border border-indigo-100 mt-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-indigo-900 font-bold flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Connection Status
                                        </h4>
                                        <span id="connection-status-badge"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $settings->js_connection_status === 'connected' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($settings->js_connection_status ?? 'disconnected') }}
                                        </span>
                                    </div>

                                    @php
                                    $hasJsUrl = !empty($settings->js_api_url) || !empty(config('services.whatsapp.js_api_url'));
                                @endphp

                                @if(in_array('js_api', $allowedProviders) || Auth::user()->isSuperAdmin())
                                    <div class="flex flex-col items-center" x-show="provider === 'js_api'">
                                        <div id="qr-container" class="bg-white p-4 rounded-lg shadow-inner mb-4">
                                            @if($hasJsUrl)
                                                <div class="w-48 h-48 bg-gray-100 flex items-center justify-center text-gray-400 italic text-sm">
                                                    Loading QR Code...
                                                </div>
                                            @else
                                                <div class="w-48 h-48 flex flex-col items-center justify-center text-gray-500 p-4 text-center text-xs">
                                                    <i class="fas fa-exclamation-triangle mb-2 text-amber-500"></i>
                                                    JS API Server URL not configured globally or for this clinic.
                                                </div>
                                            @endif
                                        </div>
                                        @if($hasJsUrl)
                                            <button type="button" onclick="loadQrCode()"
                                                class="text-indigo-600 text-xs font-bold hover:underline">
                                                Refresh QR Code
                                            </button>
                                        @endif
                                    </div>
                                    
                                    <script>
                                         let statusPollingInterval;

                                         function loadQrCode() {
                                             const container = document.getElementById('qr-container');
                                             
                                             if (!container) return;
                                             
                                             if (!{{ $hasJsUrl ? 'true' : 'false' }}) {
                                                 return;
                                             }

                                             fetch('{{ route('whatsapp.qr') }}?clinic_id={{ $selectedClinicId ?? Auth::user()->clinic_id }}')
                                                 .then(response => response.json())
                                                 .then(data => {
                                                     if (data.status === 'connected') {
                                                         container.innerHTML = `
                                                             <div class="w-48 h-48 flex flex-col items-center justify-center text-green-600 p-4 text-center">
                                                                 <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                 </svg>
                                                                 <p class="font-bold text-sm">WhatsApp Connected!</p>
                                                                 <p class="text-xs text-gray-500 mt-1">You are ready to send messages.</p>
                                                             </div>
                                                         `;
                                                         // Update status badge if exists
                                                         const badge = document.getElementById('connection-status-badge');
                                                         if (badge) {
                                                             badge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';
                                                             badge.innerText = 'Connected';
                                                         }
                                                         
                                                         if (statusPollingInterval) clearInterval(statusPollingInterval);
                                                     } else if (data.qr) {
                                                         container.innerHTML = `<img src="${data.qr}" alt="WhatsApp QR Code" class="w-48 h-48" />`;
                                                         
                                                         // Start polling if not already polling
                                                         if (!statusPollingInterval) {
                                                             statusPollingInterval = setInterval(loadQrCode, 5000);
                                                         }
                                                     } else {
                                                         container.innerHTML = '<div class="w-48 h-48 flex flex-col items-center justify-center text-red-500 p-4 text-center text-xs">Failed to load QR. Error: ' + (data.error || 'Waiting for gateway...') + '</div>';
                                                     }
                                                 })
                                                 .catch(err => {
                                                     container.innerHTML = '<div class="w-48 h-48 flex items-center justify-center text-red-500 text-xs text-center p-2">Error connecting to server. Please check JS API configuration.</div>';
                                                 });
                                         }
                                         document.addEventListener('DOMContentLoaded', function() {
                                             if ({{ $hasJsUrl ? 'true' : 'false' }}) {
                                                 loadQrCode();
                                             }
                                         });
                                    </script>
                                @endif
                                </div>
                            </div>

                            <!-- Message Configuration -->
                            <div class="mt-8 border-t border-gray-100 pt-8">
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
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Test Integration</h3>
                            <p class="text-sm text-gray-600 mb-6">Send a sample message to verify your connection is working correctly.
                            </p>

                            <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                                <form method="POST" action="{{ route('whatsapp.test') }}" class="space-y-4">
                                    @csrf
                                    @if($selectedClinicId)
                                        <input type="hidden" name="clinic_id" value="{{ $selectedClinicId }}">
                                    @endif
                                    
                                    <div class="max-w-md">
                                        <x-input-label for="test_phone" value="Recipient Phone Number" />
                                        <div class="flex mt-1 gap-2">
                                            <x-text-input id="test_phone" name="test_phone" type="text" 
                                                class="block w-full" placeholder="e.g. 923001234567" 
                                                required :value="old('test_phone')" />
                                            <x-secondary-button type="submit" class="whitespace-nowrap">
                                                {{ __('Send Test') }}
                                            </x-secondary-button>
                                        </div>
                                        <x-input-error :messages="$errors->get('test_phone')" class="mt-2" />
                                        <p class="mt-2 text-[10px] text-gray-500">
                                            Enter the number with country code (e.g., 92 for Pakistan).
                                        </p>
                                    </div>
                                </form>

                                @if(Auth::user()->isSuperAdmin())
                                    <div class="mt-6 pt-6 border-t border-gray-200">
                                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Developer Tools</h4>
                                        <form method="POST" action="{{ route('whatsapp.test.appointment') }}">
                                            @csrf
                                            @if($selectedClinicId)
                                                <input type="hidden" name="clinic_id" value="{{ $selectedClinicId }}">
                                            @endif
                                            <x-secondary-button type="submit" class="text-xs">
                                                {{ __('Create Future Test Appointment (to test Scheduler)') }}
                                            </x-secondary-button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>