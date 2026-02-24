<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Clinic Management') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-[#f8fafc] min-h-screen" x-data="{ 
        search: '',
        showWhatsAppModal: false,
        showPlanModal: false,
        selectedClinic: null,
        
        openWhatsApp(clinic) {
            this.selectedClinic = clinic;
            this.showWhatsAppModal = true;
        },
        openPlan(clinic) {
            this.selectedClinic = clinic;
            this.showPlanModal = true;
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                    <div class="p-3 rounded-xl bg-indigo-50 text-indigo-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Clinics</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $clinics->count() }}</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                    <div class="p-3 rounded-xl bg-emerald-50 text-emerald-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Active Clinics</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $clinics->where('is_active', true)->count() }}
                        </p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                    <div class="p-3 rounded-xl bg-amber-50 text-amber-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Premium Subscriptions</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $clinics->where('subscription_status', 'active')->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
                <!-- Toolbar -->
                <div
                    class="p-6 border-b border-gray-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white">
                    <div class="relative max-w-md w-full">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input type="text" x-model="search" placeholder="Search clinics by name, phone or email..."
                            class="block w-full pl-10 pr-4 py-2.5 border-gray-100 bg-gray-50/50 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all duration-200">
                    </div>
                    <div class="flex items-center gap-3">
                        <select
                            class="text-sm border-gray-100 bg-gray-50/50 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 py-2.5 px-4 mr-2">
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <a href="{{ route('super-admin.clinics.create') }}"
                            class="inline-flex items-center px-6 py-2.5 bg-indigo-600 border border-transparent rounded-xl font-bold text-sm text-white hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 whitespace-nowrap">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Clinic
                        </a>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50">
                        <thead class="bg-gray-50/30">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Clinic Details</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Growth Metrics</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Service Plan</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    WhatsApp</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Activation</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @foreach($clinics as $clinic)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-200"
                                    x-show="search === '' || '{{ addslashes(strtolower($clinic->name)) }}'.includes(search.toLowerCase()) || '{{ $clinic->phone }}'.includes(search)">
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="h-10 w-10 flex-shrink-0 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 font-bold">
                                                {{ substr($clinic->name, 0, 1) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900">{{ $clinic->name }}</div>
                                                <div class="flex items-center gap-3 mt-0.5">
                                                    <span class="text-xs text-gray-400 flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                            </path>
                                                        </svg>
                                                        {{ $clinic->phone }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-4">
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-xs text-gray-400 uppercase font-medium tracking-tighter">Docs</span>
                                                <span
                                                    class="text-sm font-bold text-gray-700">{{ $clinic->doctors_count }}</span>
                                            </div>
                                            <div class="flex flex-col border-l border-gray-100 pl-4">
                                                <span
                                                    class="text-xs text-gray-400 uppercase font-medium tracking-tighter">Patients</span>
                                                <span
                                                    class="text-sm font-bold text-gray-700">{{ $clinic->patients_count }}</span>
                                            </div>
                                            <div class="flex flex-col border-l border-gray-100 pl-4 text-indigo-600">
                                                <span
                                                    class="text-xs text-gray-400 uppercase font-medium tracking-tighter">Appts</span>
                                                <span class="text-sm font-bold">{{ $clinic->appointments_count }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-center">
                                        <button @click="openPlan({{ json_encode($clinic) }})" class="group">
                                            @if($clinic->plan)
                                                                            <span
                                                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold transition-all
                                                                                                                                                                {{ $clinic->plan->slug === 'testing' ? 'bg-amber-50 text-amber-700 border border-amber-200 group-hover:bg-amber-100' :
                                                ($clinic->plan->slug === 'pro' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200 group-hover:bg-indigo-100' :
                                                    ($clinic->plan->slug === 'basic' ? 'bg-blue-50 text-blue-700 border border-blue-200 group-hover:bg-blue-100' :
                                                        'bg-gray-50 text-gray-600 border border-gray-200 group-hover:bg-gray-100')) }}">
                                                                                {{ $clinic->plan->name }}
                                                                            </span>
                                            @else
                                                <span
                                                    class="text-xs text-gray-400 font-medium italic group-hover:text-indigo-600">+
                                                    Assign Plan</span>
                                            @endif
                                        </button>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-center">
                                        <button @click="openWhatsApp({{ json_encode($clinic) }})" class="group">
                                            <div class="flex flex-col items-center">
                                                <span
                                                    class="text-[10px] uppercase font-bold {{ in_array('js_api', $clinic->allowed_whatsapp_providers ?? []) ? 'text-indigo-600' : 'text-gray-400' }}">
                                                    {{ in_array('js_api', $clinic->allowed_whatsapp_providers ?? []) ? 'JS API' : 'Meta Meta' }}
                                                </span>
                                                <span
                                                    class="text-[9px] text-gray-300 group-hover:text-indigo-500 underline">Configure</span>
                                            </div>
                                        </button>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-center">
                                        <form action="{{ route('super-admin.clinics.toggle-status', $clinic) }}"
                                            method="POST" id="toggle-form-{{ $clinic->id }}">
                                            @csrf
                                            @method('PATCH')
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" class="sr-only peer" {{ $clinic->is_active ? 'checked' : '' }}
                                                    onchange="document.getElementById('toggle-form-{{ $clinic->id }}').submit()">
                                                <div
                                                    class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600 shadow-inner">
                                                </div>
                                            </label>
                                        </form>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-right text-sm">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('super-admin.clinics.edit', $clinic) }}"
                                                class="text-gray-400 hover:text-indigo-600 transition p-1"
                                                title="Edit Clinic">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                    </path>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($clinics->isEmpty())
                    <div class="py-20 flex flex-col items-center justify-center bg-gray-50/50">
                        <div class="p-6 bg-white rounded-3xl shadow-sm border border-gray-100 mb-4 opacity-50">
                            <svg class="w-16 h-16 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">No clinics found</h3>
                        <p class="text-gray-500 mt-1 max-w-xs text-center px-4">Get started by creating your first clinic to
                            provide your healthcare platform services.</p>
                        <a href="{{ route('super-admin.clinics.create') }}"
                            class="mt-6 text-indigo-600 font-bold hover:underline flex items-center">
                            Add a clinic now
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- WhatsApp Modal -->
        <template x-if="showWhatsAppModal && selectedClinic">
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                        @click="showWhatsAppModal = false" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div
                        class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200">
                        <form
                            :action="'{{ url('super-admin/clinics') }}/' + selectedClinic.id + '/update-providers'"
                            method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div
                                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-indigo-50 text-indigo-600 sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                            WhatsApp Configuration
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-1" x-text="selectedClinic.name"></p>

                                        <div class="mt-6 space-y-6">
                                            <div class="flex flex-col gap-3">
                                                <label
                                                    class="text-xs font-bold text-gray-400 uppercase tracking-wider">Select
                                                    Provider</label>
                                                <div class="grid grid-cols-2 gap-4">
                                                    <label
                                                        class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition"
                                                        :class="selectedClinic.allowed_whatsapp_providers?.includes('meta') ? 'border-indigo-600 bg-indigo-50/30' : 'border-gray-200'">
                                                        <input type="radio" name="provider" value="meta" class="sr-only"
                                                            x-model="selectedClinic.allowed_whatsapp_providers[0]">
                                                        <div class="flex flex-col">
                                                            <span class="text-sm font-bold text-gray-900">Meta Cloud
                                                                API</span>
                                                            <span class="text-[10px] text-gray-500">Official Production
                                                                Path</span>
                                                        </div>
                                                        <div class="ml-auto"
                                                            x-show="selectedClinic.allowed_whatsapp_providers?.includes('meta')">
                                                            <svg class="h-5 w-5 text-indigo-600" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                        </div>
                                                    </label>

                                                    <label
                                                        class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition"
                                                        :class="selectedClinic.allowed_whatsapp_providers?.includes('js_api') ? 'border-indigo-600 bg-indigo-50/30' : 'border-gray-200'">
                                                        <input type="radio" name="provider" value="js_api"
                                                            class="sr-only"
                                                            x-model="selectedClinic.allowed_whatsapp_providers[0]">
                                                        <div class="flex flex-col">
                                                            <span class="text-sm font-bold text-gray-900">JS Gateway
                                                                API</span>
                                                            <span class="text-[10px] text-gray-500">Local Instance
                                                                Scan</span>
                                                        </div>
                                                        <div class="ml-auto"
                                                            x-show="selectedClinic.allowed_whatsapp_providers?.includes('js_api')">
                                                            <svg class="h-5 w-5 text-indigo-600" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="space-y-4 pt-4 border-t border-gray-100"
                                                x-show="selectedClinic.allowed_whatsapp_providers?.includes('js_api')">
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-gray-700 uppercase mb-1">Custom
                                                        API URL</label>
                                                    <input type="text" name="js_api_url"
                                                        :value="selectedClinic.whatsapp_settings?.js_api_url"
                                                        placeholder="http://127.0.0.1:3001"
                                                        class="block w-full border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                    <p class="mt-1 text-[10px] text-gray-400 italic">Leave empty to use
                                                        system default</p>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-gray-700 uppercase mb-1">Session
                                                        ID</label>
                                                    <input type="text" name="js_session_id"
                                                        :value="selectedClinic.whatsapp_settings?.js_session_id"
                                                        placeholder="e.g. clinic_main_branch"
                                                        class="block w-full border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3 mt-4">
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-bold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm transition">
                                    Save Configuration
                                </button>
                                <button type="button" @click="showWhatsAppModal = false"
                                    class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm transition">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

        <!-- Plan Modal -->
        <template x-if="showPlanModal && selectedClinic">
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                        @click="showPlanModal = false" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div
                        class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-200">
                        <form :action="'{{ url('super-admin/clinics') }}/' + selectedClinic.id + '/plan'"
                            method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-center">
                                <div
                                    class="mx-auto flex items-center justify-center h-12 w-12 rounded-xl bg-amber-50 text-amber-600 mb-4">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">
                                    Subscription Plan
                                </h3>
                                <p class="text-sm text-gray-500 mt-2" x-text="'Change plan for ' + selectedClinic.name">
                                </p>

                                <div class="mt-6">
                                    <div class="grid grid-cols-1 gap-3">
                                        @foreach($plans as $plan)
                                            <label
                                                class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition"
                                                :class="selectedClinic.plan_id == {{ $plan->id }} ? 'border-amber-500 bg-amber-50/20' : 'border-gray-200'">
                                                <input type="radio" name="plan_id" value="{{ $plan->id }}" class="sr-only"
                                                    :checked="selectedClinic.plan_id == {{ $plan->id }}">
                                                <div class="flex flex-col text-left">
                                                    <span class="text-sm font-bold text-gray-900">{{ $plan->name }}</span>
                                                    <span
                                                        class="text-[10px] text-gray-500">{{ $plan->description ?? 'Full access to system features' }}</span>
                                                </div>
                                                <div class="ml-auto" x-show="selectedClinic.plan_id == {{ $plan->id }}">
                                                    <svg class="h-5 w-5 text-amber-500" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </label>
                                        @endforeach

                                        <label
                                            class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition"
                                            :class="!selectedClinic.plan_id ? 'border-gray-500 bg-gray-50' : 'border-gray-200'">
                                            <input type="radio" name="plan_id" value="" class="sr-only"
                                                :checked="!selectedClinic.plan_id">
                                            <div class="flex flex-col text-left">
                                                <span class="text-sm font-bold text-gray-900">No Active Plan</span>
                                                <span class="text-[10px] text-gray-500">Restrict access to core
                                                    features</span>
                                            </div>
                                            <div class="ml-auto" x-show="!selectedClinic.plan_id">
                                                <svg class="h-5 w-5 text-gray-500" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3 mt-4">
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-bold text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:w-auto sm:text-sm transition">
                                    Update Plan
                                </button>
                                <button type="button" @click="showPlanModal = false"
                                    class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm transition">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: {!! json_encode(session('success')) !!},
                timer: 3000,
                timerProgressBar: true,
                confirmButtonColor: '#4f46e5',
                customClass: {
                    popup: 'rounded-2xl border-none'
                }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: {!! json_encode(session('error')) !!},
                confirmButtonColor: '#4f46e5',
                customClass: {
                    popup: 'rounded-2xl border-none'
                }
            });
        @endif
    </script>
</x-app-layout>