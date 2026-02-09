<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Clinic Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('clinic.update') }}">
                        @csrf
                        @method('PATCH')

                        <div>
                            <x-input-label for="name" value="Clinic Name" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                :value="old('name', $clinic->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="phone" value="Phone" />
                            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone"
                                :value="old('phone', $clinic->phone)" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="address" value="Address" />
                            <textarea id="address" name="address" rows="3"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('address', $clinic->address) }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="opening_time" value="Opening Time" />
                                <x-text-input id="opening_time" class="block mt-1 w-full" type="time"
                                    name="opening_time" :value="old('opening_time', $clinic->opening_time->format('H:i'))" step="60" required />
                                <x-input-error :messages="$errors->get('opening_time')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="closing_time" value="Closing Time" />
                                <x-text-input id="closing_time" class="block mt-1 w-full" type="time"
                                    name="closing_time" :value="old('closing_time', $clinic->closing_time->format('H:i'))" step="60" required />
                                <x-input-error :messages="$errors->get('closing_time')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Save Settings') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>