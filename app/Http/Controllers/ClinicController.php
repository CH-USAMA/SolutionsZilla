<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use Illuminate\Http\Request;

class ClinicController extends Controller
{
    /**
     * Show the form for editing the clinic profile.
     */
    public function edit()
    {
        // Ensure user is admin
        if (!auth()->user()->isClinicAdmin()) {
            abort(403, 'Only clinic admins can manage clinic settings.');
        }

        $clinic = auth()->user()->clinic;

        return view('clinic.edit', compact('clinic'));
    }

    /**
     * Update the clinic profile.
     */
    public function update(Request $request)
    {
        if (!auth()->user()->isClinicAdmin()) {
            abort(403);
        }

        $clinic = auth()->user()->clinic;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'opening_time' => ['required', 'date_format:H:i'],
            'closing_time' => ['required', 'date_format:H:i'],
        ]);

        $clinic->update($validated);

        return back()->with('success', 'Clinic settings updated successfully.');
    }
}
