<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg text-gray-900 leading-tight tracking-tight">
            {{ __('Appointments') }}
        </h2>
    </x-slot>

    <div class="py-6 bg-[#f8fafc] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
                <!-- Card Header -->
                <div
                    class="px-4 py-3 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3 bg-gray-50/30">
                    <h2 class="text-sm font-bold text-gray-900">All Appointments</h2>
                    <div class="flex items-center gap-3">
                        <button id="export_csv"
                            class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-xs font-bold hover:bg-green-100 border border-green-200 transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Export CSV
                        </button>
                        <a href="{{ route('appointments.create') }}"
                            class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700 transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Book Appointment
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                <div class="px-4 py-3 border-b border-gray-100 bg-white">
                    <div class="flex flex-wrap gap-3 items-end">
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Date</label>
                            <input id="date_filter" type="date"
                                class="text-xs border-gray-200 rounded-lg py-1.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 w-40" />
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Doctor</label>
                            <select id="doctor_filter"
                                class="text-xs border-gray-200 rounded-lg py-1.5 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Doctors</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Status</label>
                            <select id="status_filter"
                                class="text-xs border-gray-200 rounded-lg py-1.5 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Statuses</option>
                                <option value="booked">Booked</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="no_show">No Show</option>
                            </select>
                        </div>
                        <button id="clear_filters"
                            class="text-xs text-gray-500 hover:text-gray-900 font-bold border-b border-gray-400 pb-0.5">Clear</button>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table id="appointments-table" class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th
                                    class="px-4 py-2.5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    No</th>
                                @if(Auth::user()->isSuperAdmin())
                                    <th
                                        class="px-4 py-2.5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                        Clinic</th>
                                @endif
                                <th
                                    class="px-4 py-2.5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    Time</th>
                                <th
                                    class="px-4 py-2.5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    Patient</th>
                                <th
                                    class="px-4 py-2.5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    Doctor</th>
                                <th
                                    class="px-4 py-2.5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-4 py-2.5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTables Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" />
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script type="text/javascript">
        $(function () {
            var table = $('#appointments-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('appointments.index') }}",
                    data: function (d) {
                        d.date = $('#date_filter').val();
                        d.doctor_id = $('#doctor_filter').val();
                        d.status = $('#status_filter').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    @if(Auth::user()->isSuperAdmin())
                        { data: 'clinic_name', name: 'clinic_name' },
                    @endif
                    { data: 'appointment_time', name: 'appointment_time' },
                    { data: 'patient_name', name: 'patient_name' },
                    { data: 'doctor_name', name: 'doctor_name' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                order: [[1, 'asc']]
            });

            $('#date_filter, #doctor_filter, #status_filter').change(function () {
                table.draw();
            });

            $('#clear_filters').click(function () {
                $('#date_filter').val('');
                $('#doctor_filter').val('');
                $('#status_filter').val('');
                table.draw();
            });

            $('#export_csv').click(function () {
                var params = '?export=csv&date=' + ($('#date_filter').val() || '') +
                    '&doctor_id=' + ($('#doctor_filter').val() || '') +
                    '&status=' + ($('#status_filter').val() || '');
                window.location.href = "{{ route('appointments.index') }}" + params;
            });
        });
    </script>
</x-app-layout>