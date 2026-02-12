<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight uppercase tracking-wider">
            {{ __('Billing & Subscription') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Subscription Status Card -->
            <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-lg font-bold text-gray-900">Subscription Status</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gray-50/80 p-4 rounded-xl border border-gray-100">
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Status</div>
                            <div class="mt-2 text-xl font-extrabold capitalize 
                                {{ $clinic->billing_status === 'paid' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $clinic->billing_status }}
                            </div>
                        </div>
                        <div class="bg-gray-50/80 p-4 rounded-xl border border-gray-100">
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Monthly Fee</div>
                            <div class="mt-2 text-xl font-extrabold text-gray-900">PKR {{ number_format($clinic->monthly_fee) }}</div>
                        </div>
                        <div class="bg-gray-50/80 p-4 rounded-xl border border-gray-100">
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Next Due Date</div>
                            <div class="mt-2 text-xl font-extrabold text-gray-900">
                                {{ $clinic->next_billing_date ? $clinic->next_billing_date->format('M d, Y') : '—' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Billing History Card -->
            <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h2 class="text-lg font-bold text-gray-900">Billing History</h2>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('billing.plans') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-bold hover:bg-indigo-100 border border-indigo-200 transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            View Plans
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Date</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Description</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Amount</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Paid Date</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">Invoice</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($records as $record)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $record->billing_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ ucfirst($record->type) }} Fee
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-extrabold text-gray-900">
                                        PKR {{ number_format($record->amount) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                            {{ $record->status === 'paid' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-600 border border-red-200' }}">
                                            {{ ucfirst($record->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $record->paid_date ? $record->paid_date->format('M d, Y') : '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        @if($record->invoice_path)
                                            <a href="{{ Storage::url($record->invoice_path) }}" target="_blank"
                                                class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 transition" title="View Invoice PDF">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            </a>
                                        @else
                                            <span class="text-gray-300 italic text-xs">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-400">No billing history found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>