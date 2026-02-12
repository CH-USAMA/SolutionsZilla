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
                                    {{ ucfirst($patient->gender ?? '-') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="font-bold text-gray-900">Address</dt>
                                <dd>{{ $patient->address ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="font-bold text-gray-900">Medical History</dt>
                                <dd class="mt-1 p-2 bg-yellow-50 rounded">
                                    {{ $patient->medical_history ?? 'No records.' }}
                                </dd>
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
                                                    {{ $appointment->appointment_date->format('M d, Y') }}
                                                </div>
                                                <div class="text-gray-500">
                                                    {{ date('h:i A', strtotime($appointment->appointment_time)) }}
                                                </div>
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

                <!-- Patient Documents -->
                <div class="col-span-1 md:col-span-3 mt-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Patient Documents</h3>
                        </div>
                        <div class="card-body">
                            <!-- Upload Form -->
                            <form action="{{ route('patients.documents.upload', $patient) }}" method="POST"
                                enctype="multipart/form-data" class="mb-6 p-4 bg-gray-50 rounded-lg">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                                    <div class="md:col-span-2">
                                        <label for="document"
                                            class="block text-sm font-medium text-gray-700 mb-2">Upload Document</label>
                                        <input type="file" name="document" id="document"
                                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required
                                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white focus:outline-none">
                                        <p class="mt-1 text-xs text-gray-500">PDF, JPG, PNG, DOC, DOCX (Max: 10MB)</p>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn-primary w-full">
                                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                                </path>
                                            </svg>
                                            Upload
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label for="description"
                                        class="block text-sm font-medium text-gray-700 mb-2">Description
                                        (Optional)</label>
                                    <input type="text" name="description" id="description"
                                        placeholder="e.g., Lab Report, X-Ray, Prescription"
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </form>

                            <!-- Documents List -->
                            @if($patient->documents->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($patient->documents as $document)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center mb-2">
                                                        @if(in_array($document->file_type, ['pdf']))
                                                            <svg class="w-8 h-8 text-red-500 mr-2" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path
                                                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z">
                                                                </path>
                                                            </svg>
                                                        @elseif(in_array($document->file_type, ['jpg', 'jpeg', 'png']))
                                                            <svg class="w-8 h-8 text-blue-500 mr-2" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                        @else
                                                            <svg class="w-8 h-8 text-gray-500 mr-2" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                        @endif
                                                        <div class="flex-1">
                                                            <h4 class="text-sm font-medium text-gray-900 truncate">
                                                                {{ $document->file_name }}</h4>
                                                            <p class="text-xs text-gray-500">
                                                                {{ number_format($document->file_size / 1024, 2) }} KB</p>
                                                        </div>
                                                    </div>
                                                    @if($document->description)
                                                        <p class="text-xs text-gray-600 mb-2">{{ $document->description }}</p>
                                                    @endif
                                                    <p class="text-xs text-gray-400">
                                                        {{ $document->created_at->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                            <div class="mt-3 flex gap-2">
                                                <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank"
                                                    class="flex-1 text-center px-3 py-1.5 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition">
                                                    View
                                                </a>
                                                <form action="{{ route('patients.documents.delete', [$patient, $document]) }}"
                                                    method="POST" class="flex-1"
                                                    onsubmit="return confirm('Are you sure you want to delete this document?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="w-full px-3 py-1.5 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <p>No documents uploaded yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>