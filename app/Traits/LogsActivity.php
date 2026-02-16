<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->logActivity('created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $before = array_intersect_key($model->getOriginal(), $model->getChanges());
            $after = $model->getChanges();

            $model->logActivity('updated', $before, $after);
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted', $model->getOriginal(), null);
        });
    }

    protected function logActivity($action, $before = null, $after = null)
    {
        $changes = [];
        if ($before)
            $changes['before'] = $before;
        if ($after)
            $changes['after'] = $after;

        ActivityLog::create([
            'clinic_id' => Auth::user()->clinic_id ?? $this->clinic_id ?? null,
            'user_id' => Auth::id(),
            'action' => $action,
            'loggable_type' => get_class($this),
            'loggable_id' => $this->id,
            'changes' => !empty($changes) ? $changes : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
