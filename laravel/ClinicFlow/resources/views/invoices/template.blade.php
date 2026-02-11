<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $billingLog->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            color: #1f2937;
            font-size: 13px;
            line-height: 1.6;
        }

        .invoice-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 40px;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 20px;
        }

        .brand {}

        .brand h1 {
            font-size: 28px;
            font-weight: 800;
            color: #4f46e5;
            letter-spacing: -0.5px;
        }

        .brand p {
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .invoice-meta {
            text-align: right;
        }

        .invoice-meta h2 {
            font-size: 22px;
            font-weight: 800;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .invoice-meta .invoice-number {
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
        }

        .invoice-meta .invoice-date {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 2px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 8px;
        }

        .status-paid {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }

        .status-pending {
            background: #fef3c7;
            color: #d97706;
            border: 1px solid #fde68a;
        }

        /* Bill To / From */
        .parties {
            display: flex;
            justify-content: space-between;
            margin-bottom: 35px;
        }

        .party {
            width: 48%;
        }

        .party-label {
            font-size: 10px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }

        .party-name {
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
        }

        .party-detail {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background: #f9fafb;
            padding: 12px 16px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-bottom: 2px solid #e5e7eb;
        }

        .items-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f3f4f6;
        }

        .items-table .item-name {
            font-weight: 600;
            color: #1f2937;
        }

        .items-table .item-desc {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 2px;
        }

        .items-table .amount {
            text-align: right;
            font-weight: 700;
        }

        .items-table th:last-child {
            text-align: right;
        }

        /* Totals */
        .totals {
            float: right;
            width: 280px;
            margin-bottom: 40px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 13px;
        }

        .total-row.grand {
            border-top: 2px solid #1f2937;
            padding-top: 12px;
            margin-top: 8px;
            font-size: 18px;
            font-weight: 800;
            color: #1f2937;
        }

        .total-label {
            color: #6b7280;
        }

        .total-amount {
            font-weight: 700;
            color: #1f2937;
        }

        /* Footer */
        .footer {
            clear: both;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
        }

        .footer p {
            margin-bottom: 4px;
        }

        /* Payment Info */
        .payment-info {
            background: #f9fafb;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 30px;
            border: 1px solid #e5e7eb;
            clear: both;
        }

        .payment-info h3 {
            font-size: 11px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
        }

        .payment-info p {
            font-size: 12px;
            color: #4b5563;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <!-- Header -->
        <table style="width:100%; margin-bottom: 30px; border-bottom: 3px solid #4f46e5; padding-bottom: 15px;">
            <tr>
                <td style="vertical-align:top;">
                    <h1 style="font-size:28px; font-weight:800; color:#4f46e5; letter-spacing:-0.5px; margin:0;">
                        ClinicFlow</h1>
                    <p
                        style="font-size:11px; color:#6b7280; margin-top:2px; text-transform:uppercase; letter-spacing:1px;">
                        Healthcare Management Platform</p>
                </td>
                <td style="text-align:right; vertical-align:top;">
                    <h2
                        style="font-size:22px; font-weight:800; color:#1f2937; text-transform:uppercase; letter-spacing:2px; margin:0;">
                        Invoice</h2>
                    <p style="font-size:13px; color:#6b7280; margin-top:4px;">
                        #INV-{{ str_pad($billingLog->id, 5, '0', STR_PAD_LEFT) }}</p>
                    <p style="font-size:12px; color:#9ca3af; margin-top:2px;">
                        {{ $billingLog->created_at->format('F d, Y') }}</p>
                    <span class="status-badge {{ $billingLog->status === 'paid' ? 'status-paid' : 'status-pending' }}">
                        {{ strtoupper($billingLog->status) }}
                    </span>
                </td>
            </tr>
        </table>

        <!-- Bill To / From -->
        <table style="width:100%; margin-bottom:35px;">
            <tr>
                <td style="width:50%; vertical-align:top;">
                    <p
                        style="font-size:10px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:2px; margin-bottom:8px;">
                        From</p>
                    <p style="font-size:16px; font-weight:700; color:#1f2937;">ClinicFlow Inc.</p>
                    <p style="font-size:12px; color:#6b7280; margin-top:2px;">support@clinicflow.com</p>
                    <p style="font-size:12px; color:#6b7280;">Healthcare SaaS Platform</p>
                </td>
                <td style="width:50%; vertical-align:top; text-align:right;">
                    <p
                        style="font-size:10px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:2px; margin-bottom:8px;">
                        Bill To</p>
                    <p style="font-size:16px; font-weight:700; color:#1f2937;">{{ $billingLog->clinic->name ?? 'N/A' }}
                    </p>
                    <p style="font-size:12px; color:#6b7280; margin-top:2px;">{{ $billingLog->clinic->phone ?? '' }}</p>
                    <p style="font-size:12px; color:#6b7280;">{{ $billingLog->clinic->address ?? '' }}</p>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Plan</th>
                    <th>Period</th>
                    <th style="text-align:right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="item-name">Subscription Payment</div>
                        <div class="item-desc">Monthly subscription fee</div>
                    </td>
                    <td>{{ $billingLog->plan->name ?? 'N/A' }}</td>
                    <td>{{ $billingLog->created_at->format('M Y') }}</td>
                    <td class="amount">${{ number_format($billingLog->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Totals -->
        <table style="width:280px; float:right; margin-bottom:40px;">
            <tr>
                <td style="padding:8px 0; font-size:13px; color:#6b7280;">Subtotal</td>
                <td style="padding:8px 0; font-size:13px; font-weight:700; text-align:right; color:#1f2937;">
                    ${{ number_format($billingLog->amount, 2) }}</td>
            </tr>
            <tr>
                <td style="padding:8px 0; font-size:13px; color:#6b7280;">Tax (0%)</td>
                <td style="padding:8px 0; font-size:13px; font-weight:700; text-align:right; color:#1f2937;">$0.00</td>
            </tr>
            <tr>
                <td
                    style="padding:12px 0 8px; font-size:18px; font-weight:800; color:#1f2937; border-top:2px solid #1f2937;">
                    Total</td>
                <td
                    style="padding:12px 0 8px; font-size:18px; font-weight:800; text-align:right; color:#1f2937; border-top:2px solid #1f2937;">
                    ${{ number_format($billingLog->amount, 2) }}</td>
            </tr>
        </table>

        <!-- Payment Info -->
        <div style="clear:both;"></div>
        <div class="payment-info">
            <h3>Payment Information</h3>
            <p><strong>Gateway:</strong> {{ ucfirst($billingLog->payment_gateway ?? 'Stripe') }}</p>
            @if($billingLog->transaction_id)
                <p><strong>Transaction ID:</strong> {{ $billingLog->transaction_id }}</p>
            @endif
            <p><strong>Date:</strong> {{ $billingLog->created_at->format('F d, Y \a\t h:i A') }}</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for choosing ClinicFlow.</p>
            <p>This is an auto-generated invoice. For questions, contact support@clinicflow.com</p>
            <p style="margin-top:10px; font-size:10px;">Generated on {{ now()->format('F d, Y') }}</p>
        </div>
    </div>
</body>

</html>