<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight uppercase tracking-wider">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h2 class="text-lg font-bold text-gray-900">All System Users</h2>
                </div>

                <div class="overflow-x-auto">
                    <table id="users-table" class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    No</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Clinic</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Name</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Email (Username)</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Role</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div id="resetPasswordModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeResetPasswordModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="resetPasswordForm" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4" id="modal-title">Reset Password for
                            <span id="resetUserName" class="text-indigo-600"></span></h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-widest mb-1">New
                                    Password</label>
                                <input type="password" name="password"
                                    class="w-full border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required minlength="8">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-700 uppercase tracking-widest mb-1">Confirm
                                    Password</label>
                                <input type="password" name="password_confirmation"
                                    class="w-full border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required minlength="8">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Reset
                            Password</button>
                        <button type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            onclick="closeResetPasswordModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(function () {
                $('#users-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('super-admin.users.index') }}",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'clinic_name', name: 'clinic.name' },
                        { data: 'name', name: 'name' },
                        { data: 'email', name: 'email' },
                        { data: 'role_badge', name: 'role' },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                    ],
                    order: [[2, 'asc']], // Order by name
                    dom: '<"flex flex-wrap justify-between items-center bg-gray-50/50 px-6 py-4 border-b border-gray-100 gap-4"lf>rt<"bg-white px-6 py-4 border-t border-gray-100 flex flex-wrap justify-between items-center gap-4"ip>',
                    language: {
                        search: "",
                        searchPlaceholder: "Search users...",
                        lengthMenu: "Show _MENU_",
                    }
                });
            });

            function openResetPasswordModal(userId, userName) {
                const form = document.getElementById('resetPasswordForm');
                form.action = `/super-admin/users/${userId}/reset-password`;
                document.getElementById('resetUserName').textContent = userName;
                document.getElementById('resetPasswordModal').classList.remove('hidden');
            }

            function closeResetPasswordModal() {
                document.getElementById('resetPasswordModal').classList.add('hidden');
            }
        </script>
        <style>
            .dataTables_wrapper .dataTables_length select {
                padding-right: 2rem;
                border-color: #e5e7eb;
                border-radius: 0.5rem;
                font-size: 0.875rem;
            }

            .dataTables_wrapper .dataTables_filter input {
                border-color: #e5e7eb;
                border-radius: 0.5rem;
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
                width: 200px;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                background: #4f46e5 !important;
                color: white !important;
                border: 1px solid #4f46e5 !important;
                border-radius: 0.375rem;
            }
        </style>
    @endpush
</x-app-layout>