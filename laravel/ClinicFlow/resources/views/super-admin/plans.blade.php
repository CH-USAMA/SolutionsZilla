<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight uppercase tracking-wider">
            Plan Management
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Subscription Plans</h1>
                <span class="text-sm text-gray-500">{{ $plans->count() }} plans configured</span>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl" role="alert">
                    <span class="block sm:inline font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                Plan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                Price</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                Limits</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                Clinics</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                                Status</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($plans as $plan)
                                            <tr class="hover:bg-gray-50/50 transition {{ !$plan->is_active ? 'opacity-60' : '' }}">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div>
                                                            <div class="text-sm font-bold text-gray-900">{{ $plan->name }}</div>
                                                            <div class="text-xs text-gray-400">{{ $plan->slug }}</div>
                                                        </div>
                                                        @if($plan->slug === 'testing')
                                                            <span
                                                                class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                                                                DEV
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span
                                                        class="text-sm font-extrabold text-gray-900">${{ number_format($plan->price, 0) }}</span>
                                                    <span class="text-xs text-gray-400">/mo</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                                    <div>{{ $plan->max_appointments == 0 ? '∞' : $plan->max_appointments }} appts</div>
                                                    <div>{{ $plan->max_whatsapp_messages == 0 ? '∞' : $plan->max_whatsapp_messages }} WA
                                                    </div>
                                                    <div>{{ $plan->max_users == 0 ? '∞' : $plan->max_users }} users</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="text-sm font-bold text-blue-600">{{ $plan->clinics_count }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    @if($plan->is_active)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200">
                                                            Active
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-200">
                                                            Disabled
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <form action="{{ route('super-admin.plans.toggle', $plan) }}" method="POST"
                                                        class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold transition
                                                                {{ $plan->is_active
                            ? 'bg-red-50 text-red-600 hover:bg-red-100 border border-red-200'
                            : 'bg-green-50 text-green-700 hover:bg-green-100 border border-green-200' }}">
                                                            {{ $plan->is_active ? 'Disable' : 'Enable' }}
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                <div class="flex">
                    <svg class="w-5 h-5 text-amber-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-amber-700">
                        <strong>Note:</strong> Disabling a plan prevents new clinics from selecting it. Existing clinics
                        on the plan will not be affected.
                        The <strong>Testing</strong> plan bypasses Stripe and can be used for development and demos.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>