<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Appointment Details
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold">{{ $appointment->patient->name }}</h3>
                            <p class="text-gray-600">{{ $appointment->patient->phone }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold 
                            @if($appointment->status === 'confirmed') bg-green-100 text-green-800
                            @elseif($appointment->status === 'completed') bg-gray-100 text-gray-800
                            @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                            @elseif($appointment->status === 'no_show') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Appointment Info
                            </h4>
                            <div class="bg-gray-50 p-4 rounded-md">
                                <div class="grid grid-cols-3 gap-2 text-sm">
                                    <span class="text-gray-500">Date:</span>
                                    <span
                                        class="col-span-2 font-medium">{{ $appointment->appointment_date->format('l, F j, Y') }}</span>

                                    <span class="text-gray-500">Time:</span>
                                    <span
                                        class="col-span-2 font-medium">{{ date('h:i A', strtotime($appointment->appointment_time)) }}</span>

                                    <span class="text-gray-500">Doctor:</span>
                                    <span class="col-span-2 font-medium">Dr. {{ $appointment->doctor->name }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Reminders</h4>
                            <div class="bg-gray-50 p-4 rounded-md">
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.506-.669-.516-.173-.009-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                            </svg>
                                            WhatsApp
                                        </span>
                                        <span
                                            class="{{ $appointment->whatsapp_reminder_sent ? 'text-green-600' : 'text-gray-400' }}">
                                            {{ $appointment->whatsapp_reminder_sent ? 'Sent' : 'Pending' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                                </path>
                                            </svg>
                                            SMS
                                        </span>
                                        <span
                                            class="{{ $appointment->sms_reminder_sent ? 'text-green-600' : 'text-gray-400' }}">
                                            {{ $appointment->sms_reminder_sent ? 'Sent' : 'Pending' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($appointment->notes)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Notes</h4>
                            <div class="bg-yellow-50 p-4 rounded-md text-gray-700 text-sm">
                                {{ $appointment->notes }}
                            </div>
                        </div>
                    @endif

                    <div class="border-t pt-6 flex justify-between items-center">
                        <form action="{{ route('appointments.destroy', $appointment) }}" method="POST"
                            onsubmit="return confirm('Are you sure?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-semibold">Delete
                                Appointment</button>
                        </form>

                        <div class="flex space-x-3">
                            @if($appointment->status === 'booked')
                                <form action="{{ route('appointments.update-status', $appointment) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit"
                                        class="px-4 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200">Cancel</button>
                                </form>
                                <form action="{{ route('appointments.update-status', $appointment) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit"
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Confirm</button>
                                </form>
                            @elseif($appointment->status === 'confirmed')
                                <form action="{{ route('appointments.update-status', $appointment) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="no_show">
                                    <button type="submit"
                                        class="px-4 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200">No
                                        Show</button>
                                </form>
                                <form action="{{ route('appointments.update-status', $appointment) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Complete
                                        Appointment</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>