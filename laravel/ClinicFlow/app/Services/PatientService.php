<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Clinic;
use Illuminate\Support\Facades\Storage;

class PatientService
{
    /**
     * Create or retrieve a patient by phone for a specific clinic.
     */
    public function getOrCreateByPhone(string $phone, string $name, Clinic $clinic, array $extra = []): Patient
    {
        return Patient::updateOrCreate(
            ['phone' => $phone, 'clinic_id' => $clinic->id],
            array_merge(['name' => $name], $extra)
        );
    }

    /**
     * Handle patient document upload.
     */
    public function uploadDocument(Patient $patient, $file)
    {
        $path = $file->store('patients/documents', 'public');

        return $patient->documents()->create([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientMimeType(),
        ]);
    }

    /**
     * Delete a patient document.
     */
    public function deleteDocument(Patient $patient, $documentId)
    {
        $document = $patient->documents()->findOrFail($documentId);
        Storage::disk('public')->delete($document->file_path);
        return $document->delete();
    }
}
