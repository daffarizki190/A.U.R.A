<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    /**
     * Boot the trait.
     */
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->recordActivity('created');
        });

        static::updated(function ($model) {
            $model->recordActivity('updated');
        });

        static::deleted(function ($model) {
            $model->recordActivity('deleted');
        });
    }

    /**
     * Record the activity.
     */
    protected function recordActivity($action)
    {
        $changes = null;

        if ($action === 'updated') {
            $changes = [
                'before' => array_intersect_key($this->getOriginal(), $this->getDirty()),
                'after' => $this->getDirty(),
            ];
            
            // Remove timestamps from changes
            unset($changes['before']['updated_at'], $changes['after']['updated_at']);
            
            if (empty($changes['after'])) {
                return; // No meaningful changes
            }
        } elseif ($action === 'created') {
            $changes = $this->getAttributes();
            unset($changes['id'], $changes['created_at'], $changes['updated_at']);
        }

        try {
            ActivityLog::create([
                'user_id'    => Auth::id(),
                'model_type' => get_class($this),
                'model_id'   => $this->id,
                'action'     => $action,
                'changes'    => $changes,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            // Tabel activity_logs belum ada — abaikan, jangan crash operasi utama
            \Illuminate\Support\Facades\Log::warning('ActivityLog gagal: ' . $e->getMessage());
        }
    }
}
