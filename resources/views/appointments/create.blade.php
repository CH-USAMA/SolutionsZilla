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
                        <div class="mb-6" x-data="{ 
                            query: '{{ old('patient_name', $preSelectedPatient->name ?? '') }}',
                            patients: [],
                            showDropdown: false,
                            selectedPatientId: null,
                            phone: '{{ old('patient_phone', $preSelectedPatient->phone ?? '') }}',
                            email: '{{ old('patient_email', $preSelectedPatient->email ?? '') }}',
                            dob: '{{ old('patient_dob', ($preSelectedPatient && $preSelectedPatient->date_of_birth) ? $preSelectedPatient->date_of_birth->format('Y-m-d') : '') }}',
                            address: '{{ old('patient_address', $preSelectedPatient->address ?? '') }}',
                            showAdvanced: {{ (old('show_advanced') || isset($preSelectedPatient)) ? 'true' : 'false' }},

                            async searchPatients() {
                                if (this.query.length < 2) {
                                    this.patients = [];
                                    this.showDropdown = false;
                                    return;
                                }

                                try {
                                    const response = await fetch(`{{ route('patients.search') }}?q=${encodeURIComponent(this.query)}`);
                                    this.patients = await response.json();
                                    this.showDropdown = this.patients.length > 0;
                                } catch (error) {
                                    console.error('Search failed:', error);
                                }
                            },

                            selectPatient(patient) {
                                this.query = patient.name;
                                this.phone = patient.phone;
                                this.email = patient.email || '';
                                this.dob = patient.dob || '';
                                this.address = patient.address || '';
                                this.selectedPatientId = patient.id;
                                this.showDropdown = false;
                                if (this.dob || this.address) this.showAdvanced = true;
                            }
                        }">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Patient Details</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="relative">
                                    <x-input-label for="patient_name" value="Patient Name" />
                                    <x-text-input id="patient_name" class="block mt-1 w-full" type="text"
                                        name="patient_name" x-model="query" @input.debounce.300ms="searchPatients()"
                                        @click.away="showDropdown = false" required autocomplete="off" />

                                    <!-- Autocomplete Dropdown -->
                                    <div x-show="showDropdown"
                                        class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto"
                                        x-transition>
                                        <template x-for="patient in patients" :key="patient.id">
                                            <div @click="selectPatient(patient)"
                                                class="px-4 py-2 cursor-pointer hover:bg-indigo-50 border-b border-gray-100 last:border-0">
                                                <div class="font-bold text-sm text-gray-900" x-text="patient.name">
                                                </div>
                                                <div class="text-xs text-gray-500" x-text="patient.phone"></div>
                                            </div>
                                        </template>
                                    </div>
                                    <x-input-error :messages="$errors->get('patient_name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="patient_phone" value="Phone Number" />
                                    <x-text-input id="patient_phone" class="block mt-1 w-full" type="text"
                                        name="patient_phone" x-model="phone" placeholder="03001234567" required />
                                    <x-input-error :messages="$errors->get('patient_phone')" class="mt-2" />
                                </div>

                                <div class="col-span-1 md:col-span-2">
                                    <x-input-label for="patient_email" value="Email (Optional)" />
                                    <x-text-input id="patient_email" class="block mt-1 w-full" type="email"
                                        name="patient_email" x-model="email" />
                                    <x-input-error :messages="$errors->get('patient_email')" class="mt-2" />
                                </div>

                                <div class="col-span-1 md:col-span-2 mt-2">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="show_advanced" value="1" x-model="showAdvanced"
                                            class="sr-only peer">
                                        <div
                                            class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                        </div>
                                        <span class="ms-3 text-sm font-medium text-gray-700">Add extra information (DOB,
                                            Address)</span>
                                    </label>
                                </div>

                                <div class="col-span-1 md:col-span-2 space-y-4 pt-2" x-show="showAdvanced" x-transition>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <x-input-label for="patient_dob" value="Date of Birth" />
                                            <x-text-input id="patient_dob" class="block mt-1 w-full" type="date"
                                                name="patient_dob" x-model="dob" max="{{ date('Y-m-d') }}" />
                                            <x-input-error :messages="$errors->get('patient_dob')" class="mt-2" />
                                        </div>
                                        <div>
                                            <x-input-label for="patient_address" value="Address" />
                                            <x-text-input id="patient_address" class="block mt-1 w-full" type="text"
                                                name="patient_address" x-model="address" placeholder="Full Address" />
                                            <x-input-error :messages="$errors->get('patient_address')" class="mt-2" />
                                        </div>
                                    </div>
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