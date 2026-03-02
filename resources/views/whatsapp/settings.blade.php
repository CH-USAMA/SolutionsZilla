<x-app-layout>
    <div x-data="{ 
        provider: '{{ $settings->provider ?? 'meta' }}',
        messageType: '{{ $settings->message_type ?? 'template' }}',
        selectedClinic: '{{ $selectedClinicId }}'
    }" class="min-h-screen bg-[#f8fafc] py-8">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">WhatsApp Settings</h2>
                    <p class="text-slate-500 mt-1">Configure your communication gateway and automated reminders.</p>
                </div>

                @if(Auth::user()->isSuperAdmin())
                    <div
                        class="w-full md:w-72 bg-white/80 backdrop-blur-sm p-1.5 rounded-2xl shadow-sm border border-slate-100">
                        <form method="GET" action="{{ route('whatsapp.settings') }}" class="relative">
                            <select name="clinic_id" onchange="this.form.submit()"
                                class="block w-full pl-3 pr-10 py-2 text-sm border-transparent focus:border-indigo-500 focus:ring-0 bg-transparent rounded-xl text-slate-700 font-medium">
                                <option value="">-- Select Clinic --</option>
                                @foreach($clinics as $clinic)
                                    <option value="{{ $clinic->id }}" {{ $selectedClinicId == $clinic->id ? 'selected' : '' }}>
                                        {{ $clinic->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            @if(Auth::user()->isSuperAdmin() && !$selectedClinicId)
                <div class="bg-white rounded-3xl p-12 text-center border border-slate-100 shadow-sm">
                    <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">Select a Clinic</h3>
                    <p class="text-slate-500 mt-2 max-w-sm mx-auto">Please select a clinic from the dropdown above to manage
                        its WhatsApp configuration.</p>
                </div>
            @else
                    @php
                        $clinic_to_check = Auth::user()->isSuperAdmin() ? \App\Models\Clinic::find($selectedClinicId) : Auth::user()->clinic;
                        $allowedProviders = $clinic_to_check?->allowed_whatsapp_providers ?? ['meta'];
                    @endphp

                    <form method="POST" action="{{ route('whatsapp.settings.update') }}">
                        @csrf
                        @if($selectedClinicId) <input type="hidden" name="clinic_id" value="{{ $selectedClinicId }}"> @endif

                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                            <!-- Left Column: Primary Config -->
                            <div class="lg:col-span-7 space-y-8">

                                <!-- Provider Card -->
                                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                                    <div
                                        class="px-6 py-5 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="font-bold text-slate-900">Gateway Provider</h3>
                                                <p class="text-[11px] text-slate-500 uppercase tracking-wider font-semibold">
                                                    Technical Infrastructure</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <span
                                                :class="provider === 'meta' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500'"
                                                class="px-3 py-1 rounded-full text-[10px] font-bold uppercase transition-colors duration-200">Meta</span>
                                            <span
                                                :class="provider === 'js_api' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500'"
                                                class="px-3 py-1 rounded-full text-[10px] font-bold uppercase transition-colors duration-200">JS
                                                API</span>
                                        </div>
                                    </div>

                                    <div class="p-8 space-y-6">
                                        @if(Auth::user()->isSuperAdmin() || count($allowedProviders) > 1)
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Select Connection
                                                    Method</label>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                    @foreach($allowedProviders as $p)
                                                        <label
                                                            class="relative flex flex-col p-4 border-2 rounded-2xl cursor-pointer transition-all duration-200"
                                                            :class="provider === '{{ $p }}' ? 'border-indigo-600 bg-indigo-50/30' : 'border-slate-100 hover:border-slate-200 bg-white'">
                                                            <input type="radio" name="provider" value="{{ $p }}" x-model="provider"
                                                                @change="if($event.target.value === 'js_api') { setTimeout(() => loadQrCodePremium(), 200) }"
                                                                class="sr-only">
                                                            <span
                                                                class="font-bold text-slate-900">{{ $p === 'meta' ? 'Meta Cloud API' : 'JS Gateway (QR)' }}</span>
                                                            <span
                                                                class="text-xs text-slate-500 mt-1">{{ $p === 'meta' ? 'Official Business API' : 'Direct Device Connection' }}</span>
                                                            <div x-show="provider === '{{ $p }}'"
                                                                class="absolute top-4 right-4 text-indigo-600">
                                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd"
                                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <input type="hidden" name="provider" value="{{ $allowedProviders[0] ?? 'meta' }}">
                                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 flex items-center gap-3">
                                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <p class="text-sm text-slate-600">Your clinic is pre-configured to use
                                                    <strong>{{ ($allowedProviders[0] ?? 'meta') === 'meta' ? 'Meta Cloud API' : 'JS Gateway' }}</strong>.
                                                </p>
                                            </div>
                                        @endif

                                        <!-- Technical Configuration (Super Admin Only) -->
                                        <div class="pt-6 border-t border-slate-100">
                                            <!-- Meta Fields -->
                                            <div x-show="provider === 'meta'" x-transition class="space-y-5">
                                                @if(Auth::user()->isSuperAdmin())
                                                    <div>
                                                        <x-input-label for="phone_number_id" value="Meta Phone Number ID"
                                                            class="text-xs font-bold uppercase text-slate-500" />
                                                        <x-text-input id="phone_number_id" name="phone_number_id" type="text"
                                                            class="mt-1 block w-full bg-slate-50/50" :value="old('phone_number_id', $settings->phone_number_id)" placeholder="012345678910111" />
                                                    </div>
                                                    <div>
                                                        <x-input-label for="access_token" value="Meta System User Access Token"
                                                            class="text-xs font-bold uppercase text-slate-500" />
                                                        <x-text-input id="access_token" name="access_token" type="password"
                                                            class="mt-1 block w-full bg-slate-50/50" :value="old('access_token', $settings->access_token)" placeholder="EAAB..." />
                                                    </div>
                                                @else
                                                    <div
                                                        class="flex items-center gap-4 p-4 rounded-xl bg-slate-50/80 border border-slate-100">
                                                        <div
                                                            class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                        <p class="text-sm text-slate-500 italic">Meta Cloud API parameters are
                                                            securely managed by the system administrator.</p>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- JS API Fields -->
                                            <div x-show="provider === 'js_api'" x-transition class="space-y-5">
                                                @if(Auth::user()->isSuperAdmin())
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                                        <div>
                                                            <x-input-label for="js_api_url" value="Gateway URL"
                                                                class="text-xs font-bold uppercase text-slate-500" />
                                                            <x-text-input id="js_api_url" name="js_api_url" type="text"
                                                                class="mt-1 block w-full bg-slate-50/50" :value="old('js_api_url', $settings->js_api_url)" placeholder="https://..." />
                                                        </div>
                                                        <div>
                                                            <x-input-label for="js_session_id" value="Gateway Session ID"
                                                                class="text-xs font-bold uppercase text-slate-500" />
                                                            <x-text-input id="js_session_id" name="js_session_id" type="text"
                                                                class="mt-1 block w-full bg-slate-50/50"
                                                                :value="old('js_session_id', $settings->js_session_id)" />
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <x-input-label for="js_api_key" value="Gateway Secret Key"
                                                            class="text-xs font-bold uppercase text-slate-500" />
                                                        <x-text-input id="js_api_key" name="js_api_key" type="password"
                                                            class="mt-1 block w-full bg-slate-50/50" :value="old('js_api_key', $settings->js_api_key)" />
                                                    </div>
                                                @else
                                                    <div class="p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <p
                                                                class="text-sm font-bold text-indigo-900 uppercase tracking-widest text-[10px]">
                                                                Session Key</p>
                                                            <span
                                                                class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-[9px] font-black uppercase rounded">Active</span>
                                                        </div>
                                                        <code
                                                            class="text-indigo-600 font-mono text-sm break-all select-all">{{ $settings->js_session_id }}</code>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Message Core Config -->
                                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                                    <div
                                        class="px-6 py-5 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center text-white">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="font-bold text-slate-900">Automation Logic</h3>
                                                <p class="text-[11px] text-slate-500 uppercase tracking-wider font-semibold">
                                                    Reminder Workflow</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-8 space-y-8">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Message Logic</label>
                                                <select name="message_type" x-model="messageType"
                                                    class="w-full border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
                                                    <option value="template">Official Meta Template</option>
                                                    <option value="text">Dynamic Custom Text</option>
                                                </select>
                                                <p class="mt-2 text-xs text-slate-400 font-medium">Official templates are
                                                    required for initial contact on Meta.</p>
                                            </div>

                                            <div x-show="messageType === 'template'" x-transition>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Approved Template
                                                    Name</label>
                                                <x-text-input name="default_template" type="text" class="w-full bg-slate-50/50"
                                                    :value="old('default_template', $settings->default_template)"
                                                    placeholder="e.g. appointment_reminder" />
                                            </div>

                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Lead Time
                                                    (Hours)</label>
                                                <div class="relative">
                                                    <x-text-input name="reminder_hours_before" type="number"
                                                        class="w-full pl-4 pr-12 bg-slate-50/50"
                                                        :value="old('reminder_hours_before', $settings->reminder_hours_before ?? 24)" />
                                                    <div
                                                        class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                                        <span class="text-xs font-bold text-slate-400">HRS</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div x-show="messageType === 'text'" x-transition
                                            class="space-y-4 pt-4 border-t border-slate-100">
                                            <div class="flex items-center justify-between">
                                                <label class="text-sm font-bold text-slate-700">Dynamic Content Editor</label>
                                                <div class="flex gap-1">
                                                    @foreach(['patient_name', 'clinic_name', 'date', 'time'] as $tag)
                                                        <span
                                                            @click="const area = document.getElementById('custom_message'); area.value = area.value.slice(0, area.selectionStart) + '{' + '{{ $tag }}' + '}' + area.value.slice(area.selectionStart); area.focus()"
                                                            class="cursor-pointer px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-bold rounded-lg border border-slate-200 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-100 transition shadow-sm">
                                                            {{ $tag }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <textarea id="custom_message" name="custom_message" rows="5"
                                                class="w-full border-slate-200 rounded-2xl text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/30 font-mono"
                                                placeholder="Write your message here...">{{ old('custom_message', $settings->custom_message) }}</textarea>
                                            <div
                                                class="flex items-center gap-2 p-3 bg-blue-50/50 border border-blue-100 rounded-xl text-blue-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <p class="text-[11px] font-medium leading-relaxed">Ensure you have connected
                                                    your device and received a reply from patients before using plain text
                                                    reminders on Meta.</p>
                                            </div>
                                        </div>

                                        <div
                                            class="flex items-center justify-between p-4 rounded-2xl bg-indigo-600/5 border border-indigo-100">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-800 text-sm">Automated Messaging</p>
                                                    <p class="text-xs text-slate-500">Enable/disable scheduler globally</p>
                                                </div>
                                            </div>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $settings->is_active) ? 'checked' : '' }}>
                                                <div
                                                    class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500">
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end">
                                    <button type="submit"
                                        class="px-8 py-3 bg-indigo-600 text-white rounded-2xl font-bold text-sm hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 focus:ring-4 focus:ring-indigo-100">
                                        Apply Configuration
                                    </button>
                                </div>
                            </div>
                    </form>

                    <!-- Right Column: Live Status & Diagnostics -->
                    <div class="lg:col-span-5 space-y-8">

                        <!-- Connection Health Card -->
                        <div x-show="provider === 'js_api'" x-transition
                            class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                            <div class="p-6 text-center border-b border-slate-50">
                                <h4 class="text-xs font-black uppercase text-slate-400 tracking-widest mb-4">Device Sync
                                </h4>

                                <div id="qr-container-premium"
                                    class="relative group mx-auto w-56 h-56 bg-white rounded-3xl shadow-inner border border-slate-50 flex items-center justify-center overflow-hidden">
                                    <div class="animate-pulse flex flex-col items-center">
                                        <div
                                            class="w-12 h-12 rounded-full border-4 border-slate-100 border-t-indigo-600 animate-spin mb-4">
                                        </div>
                                        <p class="text-xs font-bold text-slate-400 tracking-tight">Initializing...</p>
                                    </div>
                                </div>

                                <div class="mt-6 flex flex-col gap-3">
                                    <div id="sync-status"
                                        class="inline-flex items-center self-center px-4 py-1.5 rounded-full bg-slate-100 text-slate-600 text-[11px] font-black uppercase tracking-wider transition-all duration-300">
                                        <div class="w-2 h-2 rounded-full bg-slate-300 mr-2 animate-pulse"></div>
                                        Retrieving Status
                                    </div>
                                    <button type="button" onclick="loadQrCodePremium()"
                                        class="text-indigo-600 text-[10px] font-black uppercase tracking-widest hover:text-indigo-800 transition">Force
                                        Refresh</button>
                                </div>
                            </div>
                            <div class="px-6 py-4 bg-slate-50/50 flex items-center justify-center" id="logout-container"
                                style="display: none;">
                                <button type="button"
                                    onclick="if(confirm('Disconnect device? You will need to re-scan later.')) document.getElementById('whatsapp-logout-form').submit();"
                                    class="flex items-center gap-2 text-red-500 hover:text-red-700 font-bold text-xs transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Terminate Session
                                </button>
                            </div>
                        </div>

                        <!-- Meta Info Card -->
                        <div x-show="provider === 'meta'" x-transition
                            class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-3xl p-8 text-white shadow-xl shadow-indigo-100">
                            <div class="flex items-center gap-3 mb-6">
                                <div
                                    class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                                    </svg>
                                </div>
                                <h4 class="font-bold text-lg">Cloud API Status</h4>
                            </div>
                            <p class="text-indigo-100 text-sm leading-relaxed mb-6">Meta Cloud API status is handled via
                                the WhatsApp Manager dashboard. Ensure your application is "Live" and the phone number
                                is verified.</p>
                            <a href="https://developers.facebook.com" target="_blank"
                                class="block w-full text-center py-3 bg-white text-indigo-700 rounded-2xl font-bold text-xs hover:bg-indigo-50 transition">Open
                                Workspace</a>
                        </div>

                        <!-- Diagnostic Test Card -->
                        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                            <div class="px-6 py-5 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-white">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900">Health Check</h3>
                                        <p class="text-[11px] text-slate-500 uppercase tracking-wider font-semibold">
                                            Diagnostic Utility</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-8 space-y-6">
                                <p class="text-xs text-slate-500 leading-relaxed font-medium">Verify your connection by
                                    sending a real-time message to any destination. This bypasses the scheduler.</p>

                                <form method="POST" action="{{ route('whatsapp.test') }}" class="space-y-4">
                                    @csrf
                                    @if($selectedClinicId) <input type="hidden" name="clinic_id"
                                    value="{{ $selectedClinicId }}"> @endif

                                    <div>
                                        <label
                                            class="block text-[10px] font-black uppercase text-slate-400 mb-1.5 tracking-wider">Destination
                                            Number</label>
                                        <input type="text" name="test_phone" value="{{ old('test_phone') }}" required
                                            class="w-full border-slate-200 rounded-xl text-sm focus:ring-slate-800 focus:border-slate-800 bg-slate-50/50"
                                            placeholder="923xxxxxxxxx">
                                        @error('test_phone') <p class="mt-1 text-[10px] text-red-500 font-bold">
                                            {{ $message }}
                                        </p> @enderror
                                    </div>

                                    <div>
                                        <label
                                            class="block text-[10px] font-black uppercase text-slate-400 mb-1.5 tracking-wider">Custom
                                            Payload Content</label>
                                        <textarea name="test_message" rows="4" required
                                            class="w-full border-slate-200 rounded-xl text-sm focus:ring-slate-800 focus:border-slate-800 bg-slate-50/50 font-mono"
                                            placeholder="Type your test message content here...">{{ old('test_message') }}</textarea>
                                    </div>

                                    <button type="submit"
                                        class="w-full py-3 bg-slate-800 text-white rounded-2xl font-bold text-sm hover:bg-slate-900 transition flex items-center justify-center gap-2 group shadow-xl shadow-slate-200">
                                        <span>Transmit Test</span>
                                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </button>
                                </form>

                                @if(Auth::user()->isSuperAdmin())
                                    <div class="pt-6 border-t border-slate-50">
                                        <form method="POST" action="{{ route('whatsapp.test.appointment') }}">
                                            @csrf
                                            @if($selectedClinicId) <input type="hidden" name="clinic_id"
                                            value="{{ $selectedClinicId }}"> @endif
                                            <button type="submit"
                                                class="w-full py-2 bg-indigo-50 text-indigo-700 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-100 transition">
                                                Seed Future Trial Appointment
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            @endif
    </div>
    </div>

    <!-- Hidden Native Form for Logout -->
    <form id="whatsapp-logout-form" method="POST" action="{{ route('whatsapp.logout') }}" style="display: none;">
        @csrf
        @if($selectedClinicId ?? false)
            <input type="hidden" name="clinic_id" value="{{ $selectedClinicId }}">
        @endif
    </form>

    <script>
        let pollingActive = false;
        let pollingTimer;

        function loadQrCodePremium() {
            const container = document.getElementById('qr-container-premium');
            const statusBadge = document.getElementById('sync-status');
            const logoutContainer = document.getElementById('logout-container');

            if (!container) return;

            const clinicId = '{{ $selectedClinicId ?? Auth::user()->clinic_id }}';

            fetch(`{{ route('whatsapp.qr') }}?clinic_id=${clinicId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'connected') {
                        container.innerHTML = `
                            <div class="flex flex-col items-center justify-center text-emerald-500 p-8 text-center animate-in fade-in zoom-in duration-500">
                                <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                </div>
                                <p class="font-black uppercase tracking-widest text-[12px]">Device Synced</p>
                                <p class="text-[11px] text-slate-400 mt-1 font-semibold leading-tight">Ready to transmit automated healthcare reminders.</p>
                            </div>
                        `;
                        statusBadge.className = 'inline-flex items-center self-center px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-600 text-[11px] font-black uppercase tracking-wider shadow-sm border border-emerald-100';
                        statusBadge.innerHTML = '<div class="w-2 h-2 rounded-full bg-emerald-500 mr-2 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></div> Active';

                        if (logoutContainer) logoutContainer.style.display = 'flex';
                        if (pollingTimer) clearInterval(pollingTimer);
                        pollingActive = false;
                    }
                    else if (data.qr) {
                        container.innerHTML = `
                            <div class="relative p-2 bg-white rounded-2xl animate-in zoom-in duration-300">
                                <img src="${data.qr}" alt="Sync QR" class="w-48 h-48 rounded-xl ring-1 ring-slate-100 shadow-lg" />
                                <div class="absolute -top-3 -right-3 w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center shadow-lg animate-pulse ring-4 ring-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1l-3 3h6l-3-3V4z" /></svg>
                                </div>
                            </div>
                        `;
                        statusBadge.className = 'inline-flex items-center self-center px-4 py-1.5 rounded-full bg-indigo-50 text-indigo-600 text-[11px] font-black uppercase tracking-wider shadow-sm border border-indigo-100';
                        statusBadge.innerHTML = '<div class="w-2 h-2 rounded-full bg-indigo-500 mr-2 animate-pulse"></div> Scan Required';

                        if (logoutContainer) logoutContainer.style.display = 'none';
                        if (!pollingActive) startPolling();
                    }
                    else {
                        const msg = data.message || (data.error ? 'Configuration Error' : 'Gateway Offline');
                        container.innerHTML = `
                            <div class="flex flex-col items-center justify-center text-slate-400 p-8 text-center">
                                <svg class="w-12 h-12 mb-3 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <p class="text-[10px] font-black uppercase tracking-widest">${msg}</p>
                            </div>
                        `;
                        if (!pollingActive) startPolling();
                    }
                })
                .catch(err => {
                    console.error('Status fetch failed:', err);
                    if (!pollingActive) startPolling();
                });
        }

        function startPolling() {
            pollingActive = true;
            pollingTimer = setInterval(loadQrCodePremium, 5000);
        }

        document.addEventListener('DOMContentLoaded', () => {
            if ('{{ $settings->provider ?? 'meta' }}' === 'js_api') {
                loadQrCodePremium();
            }
        });

        // Re-load if provider changes to JS API
        window.addEventListener('hashchange', () => { }); // Just for trigger
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes zoom-in {
            from {
                transform: scale(0.95);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-in {
            animation-fill-mode: both;
        }

        .fade-in {
            animation-name: fade-in;
        }

        .zoom-in {
            animation-name: zoom-in;
        }

        .duration-300 {
            animation-duration: 300ms;
        }

        .duration-500 {
            animation-duration: 500ms;
        }
    </style>
</x-app-layout>