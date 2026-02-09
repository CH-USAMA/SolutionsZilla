<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Today's Schedule -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-lg font-semibold mb-4 text-gray-700">Today's Schedule</h3>

                    @if($todayAppointments->count() > 0)
                        <div class="space-y-4">
                            @foreach($todayAppointments as $appointment)
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
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
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No appointments for today.</p>
                    @endif
                </div>
            </div>

            <!-- Upcoming Appointments -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-lg font-semibold mb-4 text-gray-700">Upcoming (Next 7 Days)</h3>

                    @if($upcomingAppointments->count() > 0)
                        <div class="space-y-4">
                            @foreach($upcomingAppointments as $appointment)
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
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
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No upcoming appointments.</p>
                    @endif

                    <div class="mt-4 text-center">
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