<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight tracking-tight">Dashboard</h2>
                <p class="text-sm text-gray-500 mt-0.5">Welcome back, {{ Auth::user()->name }}.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('patients.create') }}" class="inline-flex items-center px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    New Patient
                </a>
                <a href="{{ route('appointments.create') }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-xl text-xs font-bold text-white hover:bg-indigo-700 transition shadow-sm shadow-indigo-200">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Book Appointment
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#f8fafc] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Date Filter --}}
            <div class="mb-6">
                <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-center gap-3 bg-white px-4 py-3 rounded-xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Date Range</span>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                        class="border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 py-1.5 px-3 bg-gray-50">
                    <span class="text-gray-400 text-xs">to</span>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                        class="border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 py-1.5 px-3 bg-gray-50">
                    <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700 transition">Apply</button>
                    <a href="{{ route('dashboard') }}" class="text-xs text-gray-500 hover:text-indigo-600 transition font-medium">Reset</a>
                </form>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
                {{-- Today's Appointments --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4.5 h-4.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Appointments</p>
                            <p class="text-xl font-black text-gray-900 -mt-0.5">{{ $todayTotal }}</p>
                        </div>
                    </div>
                </div>

                {{-- Confirmed --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4.5 h-4.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Confirmed</p>
                            <p class="text-xl font-black text-gray-900 -mt-0.5">{{ $todayConfirmed }}</p>
                        </div>
                    </div>
                </div>

                {{-- Completed --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4.5 h-4.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Completed</p>
                            <p class="text-xl font-black text-gray-900 -mt-0.5">{{ $todayCompleted }}</p>
                        </div>
                    </div>
                </div>

                {{-- No Shows --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4.5 h-4.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">No Shows</p>
                            <p class="text-xl font-black text-gray-900 -mt-0.5">{{ $monthlyNoShows }}</p>
                        </div>
                    </div>
                </div>

                {{-- Total Patients --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4.5 h-4.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Patients</p>
                            <p class="text-xl font-black text-gray-900 -mt-0.5">{{ $totalPatients }}</p>
                        </div>
                    </div>
                </div>

                {{-- Active Staff --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4.5 h-4.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Staff</p>
                            <p class="text-xl font-black text-gray-900 -mt-0.5">{{ $activeReceptionists }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Schedule Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" x-data="{ todayLimit: 10, upcomingLimit: 10 }">

                {{-- Today's Schedule --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-900">Today's Schedule</h3>
                        <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">{{ $todayTotal }} total</span>
                    </div>

                    <div class="p-4">
                        @if($todayAppointments->count() > 0)
                            <div class="space-y-2.5 max-h-[420px] overflow-y-auto pr-1">
                                @foreach($todayAppointments as $index => $appointment)
                                    <div x-show="{{ $index }} < todayLimit"
                                        class="flex items-center justify-between p-3 bg-gray-50/70 rounded-xl hover:bg-indigo-50/50 transition-all duration-150 border border-transparent hover:border-indigo-100">
                                        <div class="flex items-center gap-3">
                                            <div class="text-center min-w-[52px] bg-white rounded-lg py-1.5 px-2 border border-gray-100 shadow-sm">
                                                <div class="text-xs font-black text-gray-800">
                                                    {{ date('h:i', strtotime($appointment->appointment_time)) }}
                                                </div>
                                                <div class="text-[9px] font-bold text-gray-400 uppercase">
                                                    {{ date('A', strtotime($appointment->appointment_time)) }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900">{{ $appointment->patient->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $appointment->doctor->name }}</div>
                                            </div>
                                        </div>
                                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-full
                                            @if($appointment->status === 'confirmed') bg-green-100 text-green-700
                                            @elseif($appointment->status === 'completed') bg-gray-100 text-gray-600
                                            @elseif($appointment->status === 'cancelled') bg-red-100 text-red-700
                                            @elseif($appointment->status === 'no_show') bg-red-100 text-red-700
                                            @else bg-amber-100 text-amber-700 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                        </span>
                                    </div>
                                @endforeach
                                @if($todayTotal > 10)
                                <div x-show="todayLimit < {{ $todayAppointments->count() }}" class="text-center pt-2">
                                    <button @click="todayLimit += 10" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold transition">
                                        Show More ↓
                                    </button>
                                </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="text-sm text-gray-400 font-medium">No appointments for today.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Upcoming Appointments --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-900">Upcoming (Next 7 Days)</h3>
                        <span class="text-[10px] font-bold text-green-600 bg-green-50 px-2.5 py-1 rounded-full">{{ $upcomingAppointments->count() }} booked</span>
                    </div>

                    <div class="p-4">
                        @if($upcomingAppointments->count() > 0)
                            <div class="space-y-2.5 max-h-[420px] overflow-y-auto pr-1">
                                @foreach($upcomingAppointments as $index => $appointment)
                                    <div x-show="{{ $index }} < upcomingLimit"
                                        class="flex items-center justify-between p-3 bg-gray-50/70 rounded-xl hover:bg-indigo-50/50 transition-all duration-150 border border-transparent hover:border-indigo-100">
                                        <div class="flex items-center gap-3">
                                            <div class="text-center min-w-[52px] bg-white rounded-lg py-1.5 px-2 border border-gray-100 shadow-sm">
                                                <div class="text-[9px] font-black text-indigo-600 uppercase">
                                                    {{ $appointment->appointment_date->format('M d') }}
                                                </div>
                                                <div class="text-xs font-black text-gray-800">
                                                    {{ date('h:i A', strtotime($appointment->appointment_time)) }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900">{{ $appointment->patient->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $appointment->doctor->name }}</div>
                                            </div>
                                        </div>
                                        <a href="{{ route('appointments.show', $appointment) }}"
                                            class="text-xs text-indigo-600 hover:text-indigo-800 font-bold transition">View</a>
                                    </div>
                                @endforeach
                                @if($upcomingAppointments->count() > 10)
                                <div x-show="upcomingLimit < {{ $upcomingAppointments->count() }}" class="text-center pt-2">
                                    <button @click="upcomingLimit += 10" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold transition">
                                        Show More ↓
                                    </button>
                                </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-sm text-gray-400 font-medium">No upcoming appointments.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>