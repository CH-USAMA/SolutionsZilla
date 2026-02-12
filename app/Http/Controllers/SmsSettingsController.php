<?php

namespace App\Http\Controllers;

use App\Models\SmsLog;
use Illuminate\Http\Request;

class SmsSettingsController extends Controller
{
    /**
     * Display a listing of SMS logs for the clinic.
     */
    public function logs()
    {
        $logs = SmsLog::where('clinic_id', auth()->user()->clinic_id)
            ->with('patient')
            ->latest()
            ->paginate(20);

        return view('admin.sms.logs', compact('logs'));
    }
}
