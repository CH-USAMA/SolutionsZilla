<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Role Permissions: ') }} {{ ucwords(str_replace('_', ' ', $role->name)) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('super-admin.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 p-6">
                    <div class="mb-6">
                        <x-input-label for="name" :value="__('Role Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-gray-50"
                            :value="old('name', $role->name)" required autofocus
                            :disabled="in_array($role->name, ['super_admin', 'clinic_admin'])" />
                        @if(in_array($role->name, ['super_admin', 'clinic_admin']))
                            <input type="hidden" name="name" value="{{ $role->name }}">
                        @endif
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        @if(in_array($role->name, ['super_admin', 'clinic_admin']))
                            <p class="mt-2 text-xs text-amber-600 italic">System roles cannot be renamed, but you can manage
                                their permissions.</p>
                        @endif
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider">Assign Permissions
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($permissions as $group => $groupPermissions)
                                <div class="bg-gray-50/50 rounded-xl p-4 border border-gray-100">
                                    <h4
                                        class="text-xs font-bold text-indigo-600 mb-3 border-b border-indigo-100 pb-2 flex items-center">
                                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                            </path>
                                        </svg>
                                        {{ $group }}
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($groupPermissions as $permission)
                                            <label class="flex items-center group cursor-pointer">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 transition cursor-pointer"
                                                    {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                                <span class="ml-2 text-xs text-gray-600 group-hover:text-indigo-600 transition">
                                                    {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end gap-4">
                        <a href="{{ route('super-admin.roles.index') }}"
                            class="text-sm text-gray-600 hover:text-gray-900 font-medium transition">
                            Cancel
                        </a>
                        <x-primary-button>
                            {{ __('Update Permissions') }}
                        </x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>