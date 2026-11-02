<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class AuditLogService
{
    /**
     * Fields that should never be logged (sensitive data).
     */
    protected static array $hiddenFields = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Record an audit log entry.
     */
    public static function log(string $action, Model $model, ?array $oldValues = null, ?array $newValues = null): void
    {
        try {
            AuditLog::create([
                'gym_id'      => static::resolveGymId($model),
                'user_id'     => Auth::id(),
                'action'      => $action,
                'model_type'  => get_class($model),
                'model_id'    => $model->getKey(),
                'model_label' => static::resolveLabel($model),
                'old_values'  => $oldValues ? static::filterFields($oldValues) : null,
                'new_values'  => $newValues ? static::filterFields($newValues) : null,
                'ip_address'  => Request::ip(),
                'user_agent'  => Str::limit(Request::userAgent(), 500),
            ]);
        } catch (\Throwable $e) {
            // Never let audit logging break the main application.
            \Log::warning('Audit log failed: ' . $e->getMessage());
        }
    }

    /**
     * Determine the gym_id from the model.
     */
    protected static function resolveGymId(Model $model): ?int
    {
        // Direct gym_id column
        if (isset($model->gym_id)) {
            return $model->gym_id;
        }

        // The model IS a gym
        if ($model instanceof \App\Models\Gym) {
            return $model->id;
        }

        // Platform-level models (no gym)
        return null;
    }

    /**
     * Get a human-readable label for the model.
     */
    protected static function resolveLabel(Model $model): string
    {
        // Models with a user relationship (Trainer, Member, GymOwner)
        if (method_exists($model, 'user') && $model->relationLoaded('user') && $model->user) {
            return $model->user->name;
        }

        // Try common name fields
        foreach (['name', 'title', 'code'] as $field) {
            if (! empty($model->{$field})) {
                return (string) $model->{$field};
            }
        }

        // Fallback
        return class_basename($model) . ' #' . $model->getKey();
    }

    /**
     * Remove sensitive fields from the values array.
     */
    protected static function filterFields(array $values): array
    {
        return array_diff_key($values, array_flip(static::$hiddenFields));
    }
}
