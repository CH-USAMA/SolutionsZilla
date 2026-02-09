<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-end gap-4">
                        <div>
                            <x-input-label for="start_date" value="Start Date" />
                            <x-text-input id="start_date" type="date" name="start_date" value="{{ $startDate }}" class="w-40" />
                        </div>
                        <div>
                            <x-input-label for="end_date" value="End Date" />
                            <x-text-input id="end_date" type="date" name="end_date" value="{{ $endDate }}" class="w-40" />
                        </div>
                        <x-primary-button>Update Report</x-primary-button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 uppercase">Total Appointments</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $totalAppointments }}</div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 uppercase">Completed</div>
                    <div class="mt-2 text-3xl font-bold text-green-600">{{ $completedAppointments }}</div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 uppercase">No Shows</div>
                    <div class="mt-2 text-3xl font-bold text-red-600">{{ $noShows }}</div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 uppercase">Cancelled</div>
                    <div class="mt-2 text-3xl font-bold text-gray-600">{{ $cancelled }}</div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Financial Estimation -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4 border-b pb-2">Financial Estimation</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <div class="text-sm font-medium text-gray-500">Estimated Revenue (Completed)</div>
                            <div class="text-2xl font-bold text-gray-900">PKR {{ number_format($estimatedRevenue) }}</div>
                        </div>
                        
                        <div>
                            <div class="text-sm font-medium text-gray-500">Estimated Loss (No Shows)</div>
                            <div class="text-2xl font-bold text-red-600">PKR {{ number_format($estimatedLoss) }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4 border-b pb-2">Analysis</h3>
                    <p class="text-gray-600">
                        @if($totalAppointments > 0)
                            You have a <strong>{{ round(($noShows / $totalAppointments) * 100) }}%</strong> no-show rate for this period.
                            @if(($noShows / $totalAppointments) > 0.15)
                                Consider enabling stronger reminders to reduce this rate.
                            @else
                                This is within a healthy range.
                            @endif
                        @else
                            No data available for analysis.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
