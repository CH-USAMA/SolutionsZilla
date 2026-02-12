<?php

namespace App\Http\Controllers;

use App\Models\BillingLog;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Download an invoice PDF for a billing log.
     */
    public function download(BillingLog $billingLog)
    {
        $this->authorizeInvoice($billingLog);

        return $this->invoiceService->download($billingLog);
    }

    /**
     * Stream/view an invoice PDF in the browser.
     */
    public function stream(BillingLog $billingLog)
    {
        $this->authorizeInvoice($billingLog);

        return $this->invoiceService->stream($billingLog);
    }

    /**
     * Generate and store an invoice PDF, then redirect back.
     */
    public function generate(BillingLog $billingLog)
    {
        $this->authorizeInvoice($billingLog);

        $this->invoiceService->generate($billingLog);

        return redirect()->back()->with('success', 'Invoice PDF generated successfully.');
    }

    /**
     * Authorize access: Super Admin can access all, Clinic Admin only their own.
     */
    private function authorizeInvoice(BillingLog $billingLog): void
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return; // Super admin can access all invoices
        }

        if ($user->clinic_id !== $billingLog->clinic_id) {
            abort(403, 'Unauthorized access to this invoice.');
        }
    }
}
