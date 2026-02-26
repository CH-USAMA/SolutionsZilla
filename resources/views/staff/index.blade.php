<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg text-gray-900 leading-tight tracking-tight">
            {{ __('Receptionists') }}
        </h2>
    </x-slot>

    <div class="py-6 bg-[#f8fafc] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
                <!-- Card Header -->
                <div class="px-4 py-3 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3 bg-gray-50/30">
                    <h2 class="text-sm font-bold text-gray-900">All Receptionists</h2>
                    <a href="{{ route('staff.create') }}"
                        class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Receptionist
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table id="staff-table" class="min-w-full divide-y divide-gray-50 w-full text-sm text-left">
                        <thead class="bg-gray-50/50 text-gray-500">
                            <tr>
                                <th class="px-4 py-2.5 font-bold uppercase tracking-wider text-[11px]">Name</th>
                                <th class="px-4 py-2.5 font-bold uppercase tracking-wider text-[11px]">Email / Username
                                </th>
                                <th class="px-4 py-2.5 font-bold uppercase tracking-wider text-[11px]">Phone</th>
                                <th class="px-4 py-2.5 font-bold uppercase tracking-wider text-[11px]">Status</th>
                                <th class="px-4 py-2.5 font-bold uppercase tracking-wider text-[11px]">Joined</th>
                                <th class="px-4 py-2.5 font-bold uppercase tracking-wider text-[11px] text-right">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-700 bg-white"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            table.dataTable tbody tr {
                border-bottom: 1px solid #f9fafb;
                background-color: #ffffff;
                transition: background-color 0.15s ease-in-out;
            }

            table.dataTable tbody tr:hover {
                background-color: #f8fafc;
            }

            table.dataTable tbody td {
                padding: 0.65rem 1rem;
                vertical-align: middle;
                font-size: 0.8125rem;
            }

            table.dataTable.no-footer {
                border-bottom: none;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                background: #4f46e5 !important;
                color: white !important;
                border: none;
                border-radius: 0.5rem;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button {
                border-radius: 0.5rem;
                border: transparent;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                background: #e0e7ff !important;
                color: #4338ca !important;
                border: transparent;
            }
        </style>
    @endpush

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" />
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    @push('scripts')
        <script>
            $(function () {
                $('#staff-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('staff.index') }}",
                    columns: [
                        {
                            data: 'name',
                            name: 'name',
                            render: function (data, type, row) {
                                return `
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs uppercase tracking-wider mr-3">
                                                    ${data.charAt(0)}
                                                </div>
                                                <span class="font-bold text-gray-900">${data}</span>
                                            </div>
                                            `;
                            }
                        },
                        {
                            data: 'email',
                            name: 'email',
                            render: function (data) {
                                return `<span class="text-gray-500">${data}</span>`;
                            }
                        },
                        {
                            data: 'phone',
                            name: 'phone',
                            render: function (data) {
                                return data ? `<span class="text-gray-600">${data}</span>` : `<span class="text-gray-400 italic">Not provided</span>`;
                            }
                        },
                        { data: 'status', name: 'status', orderable: false, searchable: false },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right' },
                    ],
                    order: [[0, 'asc']],
                    dom: '<"flex flex-col sm:flex-row justify-between items-center bg-white px-6 py-5 border-b border-gray-100 gap-4"lf>rt<"bg-white px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4"ip>',
                    language: {
                        search: "",
                        searchPlaceholder: "Search receptionists...",
                        lengthMenu: "Show _MENU_ entries"
                    },
                    drawCallback: function () {
                        $('.dataTables_filter input').addClass('border-gray-200 rounded-xl px-4 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50');
                        $('.dataTables_length select').addClass('border-gray-200 rounded-xl px-4 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50');
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>