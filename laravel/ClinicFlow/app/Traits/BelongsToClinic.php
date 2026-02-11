<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToClinic
{
    /**
     * Boot the trait to add the global scope and handle creating event.
     */
    protected static function bootBelongsToClinic()
    {
        // Automatically set clinic_id when creating a model
        static::creating(function ($model) {
            if (!$model->clinic_id && Auth::check() && auth()->user()->clinic_id) {
                $model->clinic_id = auth()->user()->clinic_id;
            }
        });

        // Apply global scope to filter by clinic_id
        if (Auth::check()) {
            $user = Auth::user();

            // Skip scope for super admins or if the user doesn't have a clinic_id (e.g. during registration)
            if (!$user->isSuperAdmin() && $user->clinic_id) {
                static::addGlobalScope('clinic_id', function (Builder $builder) use ($user) {
                    $builder->where('clinic_id', $user->clinic_id);
                });
            }
        }
    }

    /**
     * Define the relationship to the clinic.
     */
    public function clinic()
    {
        return $this->belongsTo(\App\Models\Clinic::class);
    }
}
