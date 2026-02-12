<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight uppercase tracking-wider">
            {{ __('Create New Clinic') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-lg font-bold text-gray-900">Clinic Registration</h2>
                    <p class="text-xs text-gray-500 mt-1">Create a new clinic and its primary administrator account.</p>
                </div>

                <form action="{{ route('super-admin.clinics.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf

                    <!-- Clinic Details -->
                    <div class="space-y-4">
                        <h3
                            class="text-sm font-bold text-gray-900 uppercase tracking-wide border-b border-gray-100 pb-2">
                            Clinic Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-700 uppercase tracking-widest mb-1">Clinic
                                    Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="w-full border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required placeholder="e.g. City Health Center">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-700 uppercase tracking-widest mb-1">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                    class="w-full border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="e.g. +92 300 1234567">
                                @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-bold text-gray-700 uppercase tracking-widest mb-1">Address</label>
                            <textarea name="address" rows="3"
                                class="w-full border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Full address of the facility">{{ old('address') }}</textarea>
                            @error('address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Admin Account -->
                    <div class="space-y-4 pt-4">
                        <h3
                            class="text-sm font-bold text-gray-900 uppercase tracking-wide border-b border-gray-100 pb-2">
                            Administrator Account</h3>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-widest mb-1">Admin
                                Name <span class="text-red-500">*</span></label>
                            <input type="text" name="admin_name" value="{{ old('admin_name') }}"
                                class="w-full border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                required placeholder="Full name of the administrator">
                            @error('admin_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-widest mb-1">Email
                                (Username) <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="w-full border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                required placeholder="admin@clinic.com">
                            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-700 uppercase tracking-widest mb-1">Password
                                    <span class="text-red-500">*</span></label>
                                <input type="password" name="password"
                                    class="w-full border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required minlength="8">
                                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-700 uppercase tracking-widest mb-1">Confirm
                                    Password <span class="text-red-500">*</span></label>
                                <input type="password" name="password_confirmation"
                                    class="w-full border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required minlength="8">
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                        <a href="{{ route('super-admin.clinics.index') }}"
                            class="px-4 py-2 bg-white text-gray-700 rounded-lg border border-gray-300 text-sm font-bold hover:bg-gray-50 transition">Cancel</a>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 transition shadow-sm">Create
                            Clinic & Admin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>