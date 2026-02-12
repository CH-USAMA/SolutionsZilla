<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight uppercase tracking-wider">
            {{ __('Reports') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Date Filter Card -->
            <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h2 class="text-lg font-bold text-gray-900">Report Filters</h2>
                    <a href="{{ route('reports.index') }}?start_date={{ $startDate }}&end_date={{ $endDate }}&export=csv"
                        class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-xs font-bold hover:bg-green-100 border border-green-200 transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Export Report
                    </a>
                </div>
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-end gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Start
                                Date</label>
                            <input type="date" name="start_date" value="{{ $startDate }}"
                                class="text-xs border-gray-200 rounded-lg py-1.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 w-40" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">End
                                Date</label>
                            <input type="date" name="end_date" value="{{ $endDate }}"
                                class="text-xs border-gray-200 rounded-lg py-1.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 w-40" />
                        </div>
                        <button type="submit"
                            class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700 transition">
                            Update Report
                        </button>
                    </form>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-2 gap-5 lg:grid-cols-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-xl">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-xs font-bold text-gray-400 truncate uppercase tracking-widest">Total
                            Appointments</dt>
                        <dd class="mt-1 text-3xl font-extrabold text-gray-900">{{ $totalAppointments }}</dd>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-xl">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-xs font-bold text-gray-400 truncate uppercase tracking-widest">Completed</dt>
                        <dd class="mt-1 text-3xl font-extrabold text-green-600">{{ $completedAppointments }}</dd>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-xl">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-xs font-bold text-gray-400 truncate uppercase tracking-widest">No Shows</dt>
                        <dd class="mt-1 text-3xl font-extrabold text-red-600">{{ $noShows }}</dd>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-xl">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-xs font-bold text-gray-400 truncate uppercase tracking-widest">Cancelled</dt>
                        <dd class="mt-1 text-3xl font-extrabold text-gray-600">{{ $cancelled }}</dd>
                    </div>
                </div>
            </div>

            <!-- Analysis Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-lg font-bold text-gray-900">Financial Estimation</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Estimated Revenue
                                (Completed)</div>
                            <div class="mt-2 text-2xl font-extrabold text-green-600">PKR
                                {{ number_format($estimatedRevenue) }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Estimated Loss (No
                                Shows)</div>
                            <div class="mt-2 text-2xl font-extrabold text-red-600">PKR
                                {{ number_format($estimatedLoss) }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-lg font-bold text-gray-900">Analysis</h2>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-600 leading-relaxed">
                            @if($totalAppointments > 0)
                                You have a <strong
                                    class="text-gray-900">{{ round(($noShows / $totalAppointments) * 100) }}%</strong>
                                no-show rate for this period.
                                @if(($noShows / $totalAppointments) > 0.15)
                                    <span class="text-amber-600 font-semibold">Consider enabling stronger reminders to reduce
                                        this rate.</span>
                                @else
                                    <span class="text-green-600 font-semibold">This is within a healthy range.</span>
                                @endif
                            @else
                                No data available for analysis.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>