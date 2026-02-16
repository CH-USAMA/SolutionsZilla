<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <!-- Date Filter -->
        <div class="mb-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <x-input-label for="start_date" value="Start Date" />
                        <x-text-input id="start_date" type="date" name="start_date"
                            value="{{ $startDate->format('Y-m-d') }}" class="w-40" />
                    </div>

                    <div>
                        <x-input-label for="end_date" value="End Date" />
                        <x-text-input id="end_date" type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                            class="w-40" />
                    </div>

                    <div>
                        <x-primary-button>Filter</x-primary-button>
                        <a href="{{ route('dashboard') }}"
                            class="ml-2 text-gray-600 hover:text-gray-900 text-sm border-b border-gray-600">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                <div class="text-gray-500 text-sm font-medium uppercase">Today's Appointments</div>
                <div class="mt-2 text-3xl font-bold text-gray-900">{{ $todayTotal }}</div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                <div class="text-gray-500 text-sm font-medium uppercase">Confirmed</div>
                <div class="mt-2 text-3xl font-bold text-gray-900">{{ $todayConfirmed }}</div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-gray-500">
                <div class="text-gray-500 text-sm font-medium uppercase">Completed</div>
                <div class="mt-2 text-3xl font-bold text-gray-900">{{ $todayCompleted }}</div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                <div class="text-gray-500 text-sm font-medium uppercase">No Shows (Month)</div>
                <div class="mt-2 text-3xl font-bold text-gray-900">{{ $monthlyNoShows }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" x-data="{ todayLimit: 10, upcomingLimit: 10 }">
            <!-- Today's Schedule -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col">
                <div class="p-6 text-gray-900 flex-1">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-lg font-semibold text-gray-700">Today's Schedule</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">Limit:</span>
                            <select x-model="todayLimit"
                                class="text-xs border-gray-300 rounded p-1 focus:ring-blue-500">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">All</option>
                            </select>
                        </div>
                    </div>

                    @if($todayAppointments->count() > 0)
                        <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                            @foreach($todayAppointments as $index => $appointment)
                                <div x-show="{{ $index }} < todayLimit"
                                    class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition border border-transparent hover:border-blue-100">
                                    <div class="flex items-center space-x-4">
                                        <div class="text-center min-w-[60px]">
                                            <div class="text-sm font-bold text-gray-800">
                                                {{ date('h:i A', strtotime($appointment->appointment_time)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $appointment->patient->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $appointment->doctor->name }}</div>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                                    @if($appointment->status === 'confirmed') bg-green-100 text-green-800
                                                                    @elseif($appointment->status === 'completed') bg-gray-100 text-gray-800
                                                                    @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                                                    @elseif($appointment->status === 'no_show') bg-red-100 text-red-800
                                                                    @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                            <div x-show="todayLimit < {{ $todayAppointments->count() }}" class="text-center py-2">
                                <button @click="todayLimit += 10"
                                    class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    + Show More
                                </button>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No appointments for today.</p>
                    @endif
                </div>
            </div>

            <!-- Upcoming Appointments -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col">
                <div class="p-6 text-gray-900 flex-1">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-lg font-semibold text-gray-700">Upcoming (Next 7 Days)</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">Limit:</span>
                            <select x-model="upcomingLimit"
                                class="text-xs border-gray-300 rounded p-1 focus:ring-blue-500">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                    </div>

                    @if($upcomingAppointments->count() > 0)
                        <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                            @foreach($upcomingAppointments as $index => $appointment)
                                <div x-show="{{ $index }} < upcomingLimit"
                                    class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition border border-transparent hover:border-blue-100">
                                    <div class="flex items-center space-x-4">
                                        <div class="text-center min-w-[60px]">
                                            <div class="text-xs font-bold text-gray-500 uppercase">
                                                {{ $appointment->appointment_date->format('M d') }}
                                            </div>
                                            <div class="text-sm font-bold text-gray-800">
                                                {{ date('h:i A', strtotime($appointment->appointment_time)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $appointment->patient->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $appointment->doctor->name }}</div>
                                        </div>
                                    </div>
                                    <div>
                                        <a href="{{ route('appointments.show', $appointment) }}"
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium">View</a>
                                    </div>
                                </div>
                            @endforeach
                            <div x-show="upcomingLimit < {{ $upcomingAppointments->count() }}" class="text-center py-2">
                                <button @click="upcomingLimit += 10"
                                    class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    + Show More
                                </button>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No upcoming appointments.</p>
                    @endif

                    <div class="mt-6 text-center pt-4 border-t border-gray-50">
                        <a href="{{ route('appointments.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Book New Appointment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>