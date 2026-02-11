<!DOCTYPE html>
<html>

<head>
    <title>Activity Logs Export</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            bg-color: #f2f2f2;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .action-tag {
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>ClinicFlow Activity Audit Report</h1>
        <p>Clinic: {{ auth()->user()->clinic->name }}</p>
        <p>Generated on: {{ now()->format('M d, Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date/Time</th>
                <th>User</th>
                <th>Action</th>
                <th>Model Type</th>
                <th>Model ID</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $log->user->name ?? 'System' }}</td>
                    <td class="action-tag">{{ $log->action }}</td>
                    <td>{{ class_basename($log->loggable_type) }}</td>
                    <td>{{ $log->loggable_id }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>