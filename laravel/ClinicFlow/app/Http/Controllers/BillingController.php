<?php

namespace App\Http\Controllers;

use App\Models\BillingRecord;
use App\Models\Clinic;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    /**
     * Display billing history.
     */
    public function index()
    {
        if (!auth()->user()->isClinicAdmin()) {
            abort(403);
        }

        $records = BillingRecord::forClinic(auth()->user()->clinic_id)
            ->latest('billing_date')
            ->get();

        $clinic = auth()->user()->clinic;

        return view('billing.index', compact('records', 'clinic'));
    }
}
