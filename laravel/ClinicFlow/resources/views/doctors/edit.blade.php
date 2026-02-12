<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Doctor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('doctors.update', $doctor) }}">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" value="Name" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                :value="old('name', $doctor->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="specialization" value="Specialization" />
                            <x-text-input id="specialization" class="block mt-1 w-full" type="text"
                                name="specialization" :value="old('specialization', $doctor->specialization)" />
                            <x-input-error :messages="$errors->get('specialization')" class="mt-2" />
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="phone" value="Phone" />
                                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone"
                                    :value="old('phone', $doctor->phone)" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="email" value="Email" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                    :value="old('email', $doctor->email)" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="consultation_fee" value="Consultation Fee (PKR)" />
                            <x-text-input id="consultation_fee" class="block mt-1 w-full" type="number"
                                name="consultation_fee" :value="old('consultation_fee', $doctor->consultation_fee)"
                                min="0" step="0.01" required />
                            <x-input-error :messages="$errors->get('consultation_fee')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="qualifications" value="Qualifications" />
                            <textarea id="qualifications" name="qualifications" rows="3"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('qualifications', $doctor->qualifications) }}</textarea>
                            <x-input-error :messages="$errors->get('qualifications')" class="mt-2" />
                        </div>

                        <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <h3 class="text-sm font-bold text-gray-700 mb-3">Change Password <span
                                    class="text-xs font-normal text-gray-400">(Optional)</span></h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="password" value="New Password" />
                                    <x-text-input id="password" class="block mt-1 w-full" type="password"
                                        name="password" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="password_confirmation" value="Confirm New Password" />
                                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                                        name="password_confirmation" />
                                </div>
                            </div>
                        </div>

                        <div class="block mt-4">
                            <label for="is_available" class="inline-flex items-center">
                                <input id="is_available" type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    name="is_available" value="1" {{ old('is_available', $doctor->is_available) ? 'checked' : '' }}>
                                <span class="ms-2 text-sm text-gray-600">{{ __('Available for appointments') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-between mt-4">
                            <button type="button"
                                onclick="if(confirm('Are you sure you want to delete this doctor?')) document.getElementById('delete-doctor-form').submit();"
                                class="text-red-600 hover:text-red-900 text-sm font-semibold">
                                Delete Doctor
                            </button>

                            <x-primary-button>
                                {{ __('Update Doctor') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <form id="delete-doctor-form" method="POST" action="{{ route('doctors.destroy', $doctor) }}"
                        class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>