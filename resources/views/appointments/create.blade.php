<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Book Appointment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('appointments.store') }}">
                        @csrf

                        <!-- Patient Info Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Patient Details</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="patient_name" value="Patient Name" />
                                    <x-text-input id="patient_name" class="block mt-1 w-full" type="text"
                                        name="patient_name" :value="old('patient_name')" required />
                                    <x-input-error :messages="$errors->get('patient_name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="patient_phone" value="Phone Number" />
                                    <x-text-input id="patient_phone" class="block mt-1 w-full" type="text"
                                        name="patient_phone" :value="old('patient_phone')" placeholder="03001234567"
                                        required />
                                    <x-input-error :messages="$errors->get('patient_phone')" class="mt-2" />
                                </div>

                                <div class="col-span-1 md:col-span-2">
                                    <x-input-label for="patient_email" value="Email (Optional)" />
                                    <x-text-input id="patient_email" class="block mt-1 w-full" type="email"
                                        name="patient_email" :value="old('patient_email')" />
                                    <x-input-error :messages="$errors->get('patient_email')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Appointment Info Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Appointment Details</h3>

                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <x-input-label for="doctor_id" value="Doctor" />
                                    <select name="doctor_id" id="doctor_id"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required>
                                        <option value="">Select Doctor</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                                {{ $doctor->name }} ({{ $doctor->specialization }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('doctor_id')" class="mt-2" />
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="appointment_date" value="Date" />
                                        <x-text-input id="appointment_date" class="block mt-1 w-full" type="date"
                                            name="appointment_date" :value="old('appointment_date')"
                                            min="{{ date('Y-m-d') }}" required />
                                        <x-input-error :messages="$errors->get('appointment_date')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="appointment_time" value="Time" />
                                        <x-text-input id="appointment_time" class="block mt-1 w-full" type="time"
                                            name="appointment_time" :value="old('appointment_time')" required />
                                        <x-input-error :messages="$errors->get('appointment_time')" class="mt-2" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="notes" value="Notes (Optional)" />
                                    <textarea id="notes" name="notes" rows="3"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('appointments.index') }}"
                                class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Book Appointment') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>