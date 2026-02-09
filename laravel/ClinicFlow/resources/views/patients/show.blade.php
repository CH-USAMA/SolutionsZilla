<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $patient->name }}
            </h2>
            <a href="{{ route('patients.edit', $patient) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 transition">
                Edit Profile
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Patient Info -->
                <div class="col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Profile</h3>
                        <dl class="text-sm text-gray-600 space-y-3">
                            <div>
                                <dt class="font-bold text-gray-900">Phone</dt>
                                <dd>{{ $patient->phone }}</dd>
                            </div>
                            <div>
                                <dt class="font-bold text-gray-900">Email</dt>
                                <dd>{{ $patient->email ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="font-bold text-gray-900">Age / Gender</dt>
                                <dd>{{ $patient->age ? $patient->age . ' years' : '-' }} /
                                    {{ ucfirst($patient->gender ?? '-') }}</dd>
                            </div>
                            <div>
                                <dt class="font-bold text-gray-900">Address</dt>
                                <dd>{{ $patient->address ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="font-bold text-gray-900">Medical History</dt>
                                <dd class="mt-1 p-2 bg-yellow-50 rounded">
                                    {{ $patient->medical_history ?? 'No records.' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Appointment History -->
                <div class="col-span-1 md:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Appointment History</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                            Date/Time</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                            Doctor</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                            Status</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($patient->appointments()->latest('appointment_date')->get() as $appointment)
                                        <tr>
                                            <td class="px-4 py-3 text-sm">
                                                <div class="font-medium">
                                                    {{ $appointment->appointment_date->format('M d, Y') }}</div>
                                                <div class="text-gray-500">
                                                    {{ date('h:i A', strtotime($appointment->appointment_time)) }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-sm">{{ $appointment->doctor->name }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        @if($appointment->status === 'confirmed') bg-green-100 text-green-800
                                                        @elseif($appointment->status === 'completed') bg-gray-100 text-gray-800
                                                        @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                                        @else bg-yellow-100 text-yellow-800 @endif">
                                                    {{ ucfirst($appointment->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <a href="{{ route('appointments.show', $appointment) }}"
                                                    class="text-indigo-600 hover:text-indigo-900">View</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-3 text-center text-gray-500">No appointments
                                                found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>