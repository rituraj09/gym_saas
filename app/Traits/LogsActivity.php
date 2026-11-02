<?php

namespace App\Traits;

use App\Services\AuditLogService;

trait LogsActivity
{
    /**
     * Boot the trait — register model event listeners.
     */
    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            AuditLogService::log('created', $model, null, $model->attributesToArray());
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();

            // Skip if only timestamps changed
            $significant = array_diff_key($changes, array_flip(['updated_at', 'created_at', 'deleted_at']));

            if (! empty($significant)) {
                $oldValues = [];
                foreach (array_keys($significant) as $key) {
                    $oldValues[$key] = $model->getOriginal($key);
                }

                AuditLogService::log('updated', $model, $oldValues, $significant);
            }
        });

        static::deleted(function ($model) {
            AuditLogService::log('deleted', $model, $model->attributesToArray(), null);
        });
    }
}
