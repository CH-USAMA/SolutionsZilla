<?php

namespace App\Services;

use App\Models\BillingLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    /**
     * Generate an invoice PDF for a billing log and store it.
     *
     * @param BillingLog $billingLog
     * @return string The storage path of the generated invoice
     */
    public function generate(BillingLog $billingLog): string
    {
        $billingLog->load(['clinic', 'plan']);

        $pdf = Pdf::loadView('invoices.template', [
            'billingLog' => $billingLog,
        ]);

        $pdf->setPaper('A4', 'portrait');

        $filename = 'invoices/INV-' . str_pad($billingLog->id, 5, '0', STR_PAD_LEFT) . '.pdf';

        Storage::disk('public')->put($filename, $pdf->output());

        // Update the billing log with the invoice path
        $billingLog->update(['invoice_path' => $filename]);

        return $filename;
    }

    /**
     * Generate the PDF and return it as a download response.
     *
     * @param BillingLog $billingLog
     * @return \Illuminate\Http\Response
     */
    public function download(BillingLog $billingLog)
    {
        $billingLog->load(['clinic', 'plan']);

        $pdf = Pdf::loadView('invoices.template', [
            'billingLog' => $billingLog,
        ]);

        $pdf->setPaper('A4', 'portrait');

        $filename = 'INV-' . str_pad($billingLog->id, 5, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate the PDF and return it as a stream (view in browser).
     *
     * @param BillingLog $billingLog
     * @return \Illuminate\Http\Response
     */
    public function stream(BillingLog $billingLog)
    {
        $billingLog->load(['clinic', 'plan']);

        $pdf = Pdf::loadView('invoices.template', [
            'billingLog' => $billingLog,
        ]);

        $pdf->setPaper('A4', 'portrait');

        $filename = 'INV-' . str_pad($billingLog->id, 5, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->stream($filename);
    }
}
