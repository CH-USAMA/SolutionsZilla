<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight uppercase tracking-wider">
            Enterprise Overview
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8 border-b pb-4">Enterprise Administration Overview</h1>

            <!-- Primary Metrics Grid -->
            <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-xl">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-xs font-bold text-gray-400 truncate uppercase tracking-widest">Total Revenue
                        </dt>
                        <dd class="mt-1 text-3xl font-extrabold text-blue-600">
                            ${{ number_format($metrics['total_revenue'], 2) }}</dd>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-xl">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-xs font-bold text-gray-400 truncate uppercase tracking-widest">Clinics</dt>
                        <dd class="mt-1 text-3xl font-extrabold text-gray-900">
                            {{ $metrics['total_clinics'] }}
                        </dd>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-xl">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-xs font-bold text-gray-400 truncate uppercase tracking-widest">Active Subs</dt>
                        <dd class="mt-1 text-3xl font-extrabold text-green-600">
                            {{ $metrics['active_subscriptions'] }}
                        </dd>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-xl">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-xs font-bold text-gray-400 truncate uppercase tracking-widest">Appointments</dt>
                        <dd class="mt-1 text-3xl font-extrabold text-indigo-600">
                            {{ number_format($metrics['total_appointments']) }}
                        </dd>
                    </div>
                </div>
            </div>

            <!-- Secondary Metrics Grid -->
            <div class="grid grid-cols-3 gap-5 mb-12">
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-xl">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-xs font-bold text-gray-400 truncate uppercase tracking-widest">Total Patients
                        </dt>
                        <dd class="mt-1 text-2xl font-extrabold text-teal-600">
                            {{ number_format($metrics['total_patients']) }}
                        </dd>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-xl">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-xs font-bold text-gray-400 truncate uppercase tracking-widest">Total Doctors
                        </dt>
                        <dd class="mt-1 text-2xl font-extrabold text-purple-600">
                            {{ number_format($metrics['total_doctors']) }}
                        </dd>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-xl">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-xs font-bold text-gray-400 truncate uppercase tracking-widest">Total Users</dt>
                        <dd class="mt-1 text-2xl font-extrabold text-orange-600">
                            {{ number_format($metrics['total_users']) }}
                        </dd>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                <!-- User Registration Chart -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z">
                            </path>
                        </svg>
                        User Registrations (6 Months)
                    </h2>
                    <div style="height: 280px;">
                        <canvas id="userChart"></canvas>
                    </div>
                </div>

                <!-- Revenue Chart -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        Subscription Revenue (6 Months)
                    </h2>
                    <div style="height: 280px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Appointment Trend & Plan Distribution -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                <!-- Appointment Trend Chart -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        Appointment Trend (6 Months)
                    </h2>
                    <div style="height: 280px;">
                        <canvas id="appointmentChart"></canvas>
                    </div>
                </div>

                <!-- Plan Distribution -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                        </svg>
                        Plan Distribution
                    </h2>
                    <div class="space-y-6">
                        @foreach($plan_distribution as $plan)
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-600">{{ $plan->name }}</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $plan->clinics_count }} clinics</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5">
                                    <div class="bg-indigo-500 h-2.5 rounded-full shadow-sm"
                                        style="width: {{ $metrics['total_clinics'] > 0 ? ($plan->clinics_count / $metrics['total_clinics']) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Recent Clinics & Billing -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Clinics -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <h2 class="text-lg font-bold text-gray-900">Recent Clinics</h2>
                        <a href="{{ route('super-admin.clinics.index') }}"
                            class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">View All →</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-white">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Clinic</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Plan</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Users</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Appts</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($recent_clinics as $clinic)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $clinic->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($clinic->plan)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700">{{ $clinic->plan->name }}</span>
                                            @else
                                                <span class="text-xs text-gray-400 italic">None</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-700">
                                            {{ $clinic->users_count }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-blue-600">
                                            {{ $clinic->appointments_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Billing Logs -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <h2 class="text-lg font-bold text-gray-900">Recent Transactions</h2>
                        <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded">Live Feed</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-white">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Clinic</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Amount</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest text-center">
                                        Receipt</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($recent_billings as $log)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $log->clinic->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-extrabold">
                                            ${{ number_format($log->amount, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->created_at->format('M d, H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            @if($log->invoice_path)
                                                <a href="{{ Storage::url($log->invoice_path) }}" target="_blank"
                                                    class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                </a>
                                            @else
                                                <span class="text-gray-300 italic text-xs">No PDF</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">No billing
                                            transactions yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartDefaults = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, weight: 'bold' }, color: '#9CA3AF' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#F3F4F6' },
                        ticks: { font: { size: 11 }, color: '#9CA3AF' }
                    }
                }
            };

            // User Registration Chart
            new Chart(document.getElementById('userChart'), {
                type: 'bar',
                data: {
                    labels: @json($userChartLabels),
                    datasets: [{
                        label: 'New Users',
                        data: @json($userChartData),
                        backgroundColor: 'rgba(249, 115, 22, 0.15)',
                        borderColor: 'rgb(249, 115, 22)',
                        borderWidth: 2,
                        borderRadius: 8,
                        barThickness: 32,
                    }]
                },
                options: chartDefaults
            });

            // Revenue Chart
            new Chart(document.getElementById('revenueChart'), {
                type: 'line',
                data: {
                    labels: @json($revenueChartLabels),
                    datasets: [{
                        label: 'Revenue ($)',
                        data: @json($revenueChartData),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.08)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointBorderWidth: 0,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                    }]
                },
                options: chartDefaults
            });

            // Appointment Trend Chart
            new Chart(document.getElementById('appointmentChart'), {
                type: 'line',
                data: {
                    labels: @json($appointmentChartLabels),
                    datasets: [{
                        label: 'Appointments',
                        data: @json($appointmentChartData),
                        borderColor: 'rgb(99, 102, 241)',
                        backgroundColor: 'rgba(99, 102, 241, 0.08)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgb(99, 102, 241)',
                        pointBorderWidth: 0,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                    }]
                },
                options: chartDefaults
            });
        });
    </script>
</x-app-layout>