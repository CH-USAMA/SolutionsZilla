<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upgrade Your Clinic') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl">
                    Upgrade Your Clinic Experience
                </h2>
                <p class="mt-4 text-xl text-gray-600 dark:text-gray-400">
                    Choose the plan that fits your clinic's needs. All plans include professional WhatsApp automation.
                </p>
            </div>

            @if(session('error'))
                <div class="mt-8 max-w-md mx-auto bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div
                class="mt-12 space-y-4 sm:mt-16 sm:space-y-0 sm:grid sm:grid-cols-2 sm:gap-6 lg:max-w-4xl lg:mx-auto xl:max-w-none xl:mx-0 xl:grid-cols-3">
                @foreach($plans as $plan)
                    <div
                        class="border {{ $plan->slug === 'testing' ? 'border-amber-300 ring-2 ring-amber-100' : 'border-gray-200 dark:border-gray-700' }} rounded-2xl shadow-sm divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800 transition-all hover:shadow-xl hover:-translate-y-1">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">{{ $plan->name }}
                                </h3>
                                @if($plan->slug === 'testing')
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700">DEV</span>
                                @endif
                            </div>
                            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ $plan->description }}</p>
                            <p class="mt-8">
                                @if($plan->price == 0)
                                    <span class="text-4xl font-extrabold text-gray-900 dark:text-white">Free</span>
                                @else
                                    <span
                                        class="text-4xl font-extrabold text-gray-900 dark:text-white">${{ number_format($plan->price, 0) }}</span>
                                    <span class="text-base font-medium text-gray-500">/mo</span>
                                @endif
                            </p>

                            <form action="{{ route('billing.checkout') }}" method="POST" class="mt-8">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                <button type="submit"
                                    class="w-full {{ $plan->slug === 'testing' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-indigo-600 hover:bg-indigo-700' }} border border-transparent rounded-md py-2 text-sm font-semibold text-white text-center focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $currentPlan && $currentPlan->id == $plan->id ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ $currentPlan && $currentPlan->id == $plan->id ? 'disabled' : '' }}>
                                    {{ $currentPlan && $currentPlan->id == $plan->id ? 'Current Plan' : ($plan->price == 0 ? 'Activate ' . $plan->name : 'Select ' . $plan->name) }}
                                </button>
                            </form>
                        </div>
                        <div class="pt-6 pb-8 px-6">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white tracking-wide uppercase">What's
                                included</h4>
                            <ul role="list" class="mt-6 space-y-4">
                                <li class="flex space-x-3">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Up to
                                        {{ $plan->max_appointments == 0 ? 'Unlimited' : $plan->max_appointments }}
                                        Appointments</span>
                                </li>
                                <li class="flex space-x-3">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Up to
                                        {{ $plan->max_whatsapp_messages == 0 ? 'Unlimited' : $plan->max_whatsapp_messages }}
                                        WhatsApp Messages</span>
                                </li>
                                @foreach($plan->features as $feature)
                                    <li class="flex space-x-3">
                                        <svg class="flex-shrink-0 h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>