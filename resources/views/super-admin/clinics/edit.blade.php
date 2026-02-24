<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('super-admin.clinics.index') }}"
                class="p-2 bg-white rounded-xl border border-gray-100 shadow-sm text-gray-400 hover:text-indigo-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Edit Clinic') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-[#f8fafc] min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 bg-gradient-to-br from-indigo-50/50 to-white">
                    <div class="flex items-center gap-4">
                        <div
                            class="h-16 w-16 bg-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold shadow-lg shadow-indigo-200">
                            {{ substr($clinic->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $clinic->name }}</h3>
                            <p class="text-sm text-gray-500">Update clinic identity and contact information</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('super-admin.clinics.update', $clinic) }}" method="POST" class="p-8 space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Clinic
                                Name</label>
                            <input type="text" name="name" value="{{ old('name', $clinic->name) }}" required
                                class="block w-full px-4 py-3 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 border border-gray-100">
                            @error('name') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Phone
                                Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $clinic->phone) }}"
                                class="block w-full px-4 py-3 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 border border-gray-100">
                            @error('phone') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label
                                class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Address</label>
                            <textarea name="address" rows="3"
                                class="block w-full px-4 py-3 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 border border-gray-100">{{ old('address', $clinic->address) }}</textarea>
                            @error('address') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-6 flex items-center justify-end gap-4">
                        <a href="{{ route('super-admin.clinics.index') }}"
                            class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 transition">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-8 py-3 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transform hover:-translate-y-0.5 transition-all duration-200">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>