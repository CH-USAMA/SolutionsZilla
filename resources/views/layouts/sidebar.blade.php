<aside
    class="fixed inset-y-0 left-0 z-50 w-[220px] bg-white border-r border-gray-100 transform transition-transform duration-300 lg:translate-x-0 flex flex-col h-screen shadow-sm"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    {{-- Header --}}
    <div class="flex items-center justify-between h-14 px-4 border-b border-gray-100">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <div class="h-7 w-7 bg-indigo-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-black text-xs">SZ</span>
            </div>
            <span class="font-bold text-sm text-gray-900 tracking-tight">ClinicFlow</span>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto text-[13px] font-medium" x-data="{
        reportingOpen: {{ request()->routeIs('reports.*') || request()->routeIs('whatsapp.logs') || request()->routeIs('sms.logs') || request()->routeIs('admin.logs.*') ? 'true' : 'false' }},
        commsOpen: {{ request()->routeIs('whatsapp.*') || request()->routeIs('sms.*') ? 'true' : 'false' }},
        settingsOpen: {{ request()->routeIs('clinic.*') || request()->routeIs('profile.*') || request()->routeIs('whatsapp.settings') ? 'true' : 'false' }}
    }">

        {{-- Main Navigation --}}
        <p class="px-3 pt-1 pb-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Main</p>

        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z">
                </path>
            </svg>
            Dashboard
        </a>

        <a href="{{ route('appointments.index') }}"
            class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-150 {{ request()->routeIs('appointments.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Appointments
        </a>

        <a href="{{ route('patients.index') }}"
            class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-150 {{ request()->routeIs('patients.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z">
                </path>
            </svg>
            Patients
        </a>

        <a href="{{ route('doctors.index') }}"
            class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-150 {{ request()->routeIs('doctors.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            Doctors
        </a>

        @if(Auth::user()->isClinicAdmin())
            <a href="{{ route('staff.index') }}"
                class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-150 {{ request()->routeIs('staff.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
                Receptionists
            </a>
        @endif

        {{-- Reporting Dropdown --}}
        <div class="pt-3 mt-3 border-t border-gray-100">
            <p class="px-3 pt-1 pb-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Reporting</p>

            <button @click="reportingOpen = !reportingOpen"
                class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-150">
                <span class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Reports & Logs
                </span>
                <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200"
                    :class="reportingOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="reportingOpen" x-collapse class="ml-6 mt-0.5 space-y-0.5 border-l-2 border-gray-100 pl-3">
                <a href="{{ route('reports.index') }}"
                    class="block px-3 py-1.5 rounded-md transition {{ request()->routeIs('reports.*') ? 'text-indigo-700 font-semibold bg-indigo-50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                    Reports
                </a>
                <a href="{{ route('whatsapp.logs') }}"
                    class="block px-3 py-1.5 rounded-md transition {{ request()->routeIs('whatsapp.logs') ? 'text-indigo-700 font-semibold bg-indigo-50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                    WhatsApp Logs
                </a>
                <a href="{{ route('sms.logs') }}"
                    class="block px-3 py-1.5 rounded-md transition {{ request()->routeIs('sms.logs') ? 'text-indigo-700 font-semibold bg-indigo-50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                    SMS Logs
                </a>
                @if(Auth::user()->isClinicAdmin())
                    <a href="{{ route('admin.logs.index') }}"
                        class="block px-3 py-1.5 rounded-md transition {{ request()->routeIs('admin.logs.*') ? 'text-indigo-700 font-semibold bg-indigo-50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                        Audit Logs
                    </a>
                @endif
            </div>
        </div>

        {{-- Communications Dropdown --}}
        <div class="pt-1">
            <button @click="commsOpen = !commsOpen"
                class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-150">
                <span class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z">
                        </path>
                    </svg>
                    WhatsApp
                </span>
                <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200"
                    :class="commsOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="commsOpen" x-collapse class="ml-6 mt-0.5 space-y-0.5 border-l-2 border-gray-100 pl-3">
                <a href="{{ route('whatsapp.dashboard') }}"
                    class="block px-3 py-1.5 rounded-md transition {{ request()->routeIs('whatsapp.dashboard') ? 'text-indigo-700 font-semibold bg-indigo-50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                    Dashboard
                </a>
                @if(Auth::user()->isClinicAdmin())
                    <a href="{{ route('whatsapp.settings') }}"
                        class="block px-3 py-1.5 rounded-md transition {{ request()->routeIs('whatsapp.settings') ? 'text-indigo-700 font-semibold bg-indigo-50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                        Settings
                    </a>
                @endif
            </div>
        </div>

        {{-- Clinic Admin Section --}}
        @if(Auth::user()->isClinicAdmin())
            <div class="pt-3 mt-3 border-t border-gray-100">
                <p class="px-3 pt-1 pb-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Settings</p>

                <button @click="settingsOpen = !settingsOpen"
                    class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-150">
                    <span class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Manage
                    </span>
                    <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200"
                        :class="settingsOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div x-show="settingsOpen" x-collapse class="ml-6 mt-0.5 space-y-0.5 border-l-2 border-gray-100 pl-3">
                    <a href="{{ route('clinic.edit') }}"
                        class="block px-3 py-1.5 rounded-md transition {{ request()->routeIs('clinic.*') ? 'text-indigo-700 font-semibold bg-indigo-50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                        Clinic Settings
                    </a>
                    <a href="{{ route('profile.edit') }}"
                        class="block px-3 py-1.5 rounded-md transition {{ request()->routeIs('profile.*') ? 'text-indigo-700 font-semibold bg-indigo-50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                        Profile
                    </a>
                </div>
            </div>

            <a href="{{ route('billing.index') }}"
                class="flex items-center gap-2.5 px-3 py-2 mt-1 rounded-lg transition-all duration-150 {{ request()->routeIs('billing.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                Billing
            </a>
        @endif

        {{-- Super Admin Section --}}
        @if(Auth::user()->isSuperAdmin())
            <div class="pt-3 mt-3 border-t border-gray-100">
                <p class="px-3 pt-1 pb-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Super Admin</p>

                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-150 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    SaaS Overview
                </a>

                <a href="{{ route('super-admin.clinics.index') }}"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-150 {{ request()->routeIs('super-admin.clinics.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    Clinics
                </a>

                <a href="{{ route('super-admin.plans.index') }}"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-150 {{ request()->routeIs('super-admin.plans.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    Plans
                </a>

                <a href="{{ route('super-admin.logs.index') }}"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-150 {{ request()->routeIs('super-admin.logs.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                    Global Audit
                </a>

                <a href="{{ route('super-admin.api-explorer') }}"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-150 {{ request()->routeIs('super-admin.api-explorer*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                    API Explorer
                </a>
            </div>
        @endif

    </nav>

    {{-- User Profile Footer --}}
    <div class="p-3 border-t border-gray-100 flex-shrink-0">
        <div x-data="{ profileOpen: false }" class="relative">
            <button @click="profileOpen = !profileOpen"
                class="flex items-center w-full gap-2.5 px-2.5 py-2 text-left rounded-lg hover:bg-gray-50 transition-all duration-150">
                <div
                    class="flex-shrink-0 w-7 h-7 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-[11px]">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-gray-400 uppercase tracking-wider">
                        {{ str_replace('_', ' ', Auth::user()->role) }}</p>
                </div>
                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="profileOpen" @click.away="profileOpen = false" x-transition
                class="absolute bottom-full left-0 right-0 mb-1.5 bg-white border border-gray-100 rounded-xl shadow-lg overflow-hidden">
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>

{{-- Overlay for mobile --}}
<div x-show="sidebarOpen" @click="sidebarOpen = false" class="lg:hidden fixed inset-0 z-40 bg-black/40 backdrop-blur-sm"
    x-transition></div>