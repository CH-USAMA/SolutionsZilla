<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight tracking-tight">
                    {{ __('Edit Receptionist') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Update account details for <strong>{{ $staff->name }}</strong>.
                </p>
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

            @if(session('success'))
                <div
                    class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Account Details Card --}}
            <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden mb-6">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/30">
                    <h3 class="text-base font-bold text-gray-900">Account Details</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Update personal information and contact details.</p>
                </div>

                <form action="{{ route('staff.update', $staff) }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Full Name
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $staff->name) }}"
                            class="w-full border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 py-2.5 px-4 bg-gray-50/50"
                            required placeholder="e.g. Sarah Jones">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Email
                                Address (Login) <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $staff->email) }}"
                                class="w-full border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 py-2.5 px-4 bg-gray-50/50"
                                required placeholder="sarah@clinic.com">
                            @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Phone
                                Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $staff->phone) }}"
                                class="w-full border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 py-2.5 px-4 bg-gray-50/50"
                                placeholder="+923001234567">
                            @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="pt-5 border-t border-gray-100 flex justify-end gap-3">
                        <a href="{{ route('staff.index') }}"
                            class="px-5 py-2.5 bg-white text-gray-700 rounded-xl border border-gray-200 text-sm font-bold hover:bg-gray-50 transition">Cancel</a>
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-bold hover:bg-indigo-700 transition shadow-sm shadow-indigo-200">Save
                            Changes</button>
                    </div>
                </form>
            </div>

            {{-- Reset Password Card --}}
            <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden mb-6">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/30">
                    <h3 class="text-base font-bold text-gray-900">Reset Password</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Set a new password for this receptionist. Leave blank to
                        keep the current password.</p>
                </div>

                <form action="{{ route('staff.update', $staff) }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- Hidden fields to re-submit current values --}}
                    <input type="hidden" name="name" value="{{ $staff->name }}">
                    <input type="hidden" name="email" value="{{ $staff->email }}">
                    <input type="hidden" name="phone" value="{{ $staff->phone }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">New
                                Password <span class="text-red-500">*</span></label>
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

                    <div class="pt-5 border-t border-gray-100 flex justify-end">
                        <button type="submit"
                            class="px-5 py-2.5 bg-amber-500 text-white rounded-xl text-sm font-bold hover:bg-amber-600 transition shadow-sm shadow-amber-200">
                            <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                </path>
                            </svg>
                            Reset Password
                        </button>
                    </div>
                </form>
            </div>

            {{-- Status & Danger Zone --}}
            <div class="bg-white shadow-sm border border-red-100 rounded-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-red-100 bg-red-50/30">
                    <h3 class="text-base font-bold text-red-700">Danger Zone</h3>
                    <p class="text-xs text-red-500 mt-0.5">Irreversible actions. Proceed with caution.</p>
                </div>
                <div class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <p class="font-bold text-sm text-gray-900">Delete this receptionist</p>
                        <p class="text-xs text-gray-500 mt-0.5">This will permanently remove their account and revoke
                            all access.</p>
                    </div>
                    <form action="{{ route('staff.destroy', $staff) }}" method="POST"
                        onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-5 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 transition shadow-sm shadow-red-200">
                            Delete Account
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>