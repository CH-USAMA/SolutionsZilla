<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight tracking-tight">
                    {{ __('Add Receptionist') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Create a new front-desk staff account.</p>
            </div>
            <a href="{{ route('staff.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-xl font-bold text-sm text-gray-700 hover:bg-gray-50 transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-[#f8fafc] min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/30">
                    <h3 class="text-base font-bold text-gray-900">New Account</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Create a login account for front-desk staff.</p>
                </div>

                <form action="{{ route('staff.store') }}" method="POST" class="p-6 space-y-5">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Full Name
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 py-2.5 px-4 bg-gray-50/50"
                            required placeholder="e.g. Sarah Jones">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Email
                                Address (Login) <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="w-full border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 py-2.5 px-4 bg-gray-50/50"
                                required placeholder="sarah@clinic.com">
                            @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Phone
                                Number</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                class="w-full border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 py-2.5 px-4 bg-gray-50/50"
                                placeholder="+923001234567">
                            @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Password
                                <span class="text-red-500">*</span></label>
                            <input type="password" name="password"
                                class="w-full border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 py-2.5 px-4 bg-gray-50/50"
                                required minlength="8" placeholder="Min. 8 characters">
                            @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Confirm
                                Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation"
                                class="w-full border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 py-2.5 px-4 bg-gray-50/50"
                                required minlength="8" placeholder="Re-enter password">
                        </div>
                    </div>

                    <div class="pt-5 border-t border-gray-100 flex justify-end gap-3">
                        <a href="{{ route('staff.index') }}"
                            class="px-5 py-2.5 bg-white text-gray-700 rounded-xl border border-gray-200 text-sm font-bold hover:bg-gray-50 transition">Cancel</a>
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-bold hover:bg-indigo-700 transition shadow-sm shadow-indigo-200">Create
                            Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>