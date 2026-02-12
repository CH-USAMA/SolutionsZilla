<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight uppercase tracking-wider">
            Clinic Management
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-2xl font-bold text-gray-900">All Clinics</h1>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500">{{ $clinics->count() }} clinics registered</span>
                    <a href="{{ route('super-admin.clinics.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        Add New Clinic
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl" role="alert">
                    <span class="block sm:inline font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Clinic</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Current Plan</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Users</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Doctors</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Patients</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Appts</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Assign Plan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($clinics as $clinic)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">{{ $clinic->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $clinic->phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($clinic->plan)
                                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                                                                                                {{ $clinic->plan->slug === 'testing' ? 'bg-amber-50 text-amber-700 border border-amber-200' :
                                            ($clinic->plan->slug === 'pro' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' :
                                                ($clinic->plan->slug === 'basic' ? 'bg-blue-50 text-blue-700 border border-blue-200' :
                                                    'bg-gray-50 text-gray-600 border border-gray-200')) }}">
                                                                        {{ $clinic->plan->name }}
                                                                    </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">No Plan</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm font-bold text-gray-700">
                                        {{ $clinic->users_count }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm font-bold text-gray-700">
                                        {{ $clinic->doctors_count }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm font-bold text-gray-700">
                                        {{ $clinic->patients_count }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm font-bold text-blue-600">
                                        {{ $clinic->appointments_count }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($clinic->subscription_status === 'active')
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700">Active</span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-600">{{ ucfirst($clinic->subscription_status ?? 'N/A') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <form action="{{ route('super-admin.clinics.update-plan', $clinic) }}" method="POST"
                                            class="inline-flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="plan_id"
                                                class="text-xs border-gray-200 rounded-lg py-1 px-2 focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="">— No Plan —</option>
                                                @foreach($plans as $plan)
                                                    <option value="{{ $plan->id }}" {{ $clinic->plan_id == $plan->id ? 'selected' : '' }}>
                                                        {{ $plan->name }} (${{ number_format($plan->price, 0) }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit"
                                                class="inline-flex items-center px-2 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-bold hover:bg-indigo-100 border border-indigo-200 transition">
                                                Apply
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>