<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight uppercase tracking-wider">
            {{ __('Activity & Audit Logs') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
                <!-- Card Header -->
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h2 class="text-lg font-bold text-gray-900">Audit Trail</h2>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.logs.export') }}"
                            class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-xs font-bold hover:bg-green-100 border border-green-200 transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Export PDF
                        </a>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Time</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    User</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Action</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Model</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log->created_at->format('M d, Y H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                        {{ $log->user->name ?? 'System' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border
                                                {{ $log->action === 'created' ? 'border-green-200 bg-green-50 text-green-700' : '' }}
                                                {{ $log->action === 'updated' ? 'border-blue-200 bg-blue-50 text-blue-700' : '' }}
                                                {{ $log->action === 'deleted' ? 'border-red-200 bg-red-50 text-red-700' : '' }}">
                                            {{ strtoupper($log->action) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 italic">
                                        {{ class_basename($log->loggable_type) }} (#{{ $log->loggable_id }})
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        @if($log->changes)
                                            <button
                                                onclick="document.getElementById('changes-{{ $log->id }}').classList.toggle('hidden')"
                                                class="text-indigo-600 hover:text-indigo-800 font-bold text-xs">View
                                                Changes</button>
                                            <div id="changes-{{ $log->id }}"
                                                class="hidden mt-2 p-3 bg-gray-50 rounded-lg text-xs border border-gray-100">
                                                <pre
                                                    class="whitespace-pre-wrap">{{ json_encode($log->changes, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400">No activity logs
                                        found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>