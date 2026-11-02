<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'gym_id',
        'user_id',
        'action',
        'model_type',
        'model_id',
        'model_label',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /* ── Relationships ── */

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ── Helpers ── */

    /**
     * Human-readable model name (e.g. "App\Models\Trainer" → "Trainer").
     */
    public function getModelNameAttribute(): string
    {
        return class_basename($this->model_type);
    }

    /**
     * Color badge class based on action type.
     */
    public function getActionBadgeClassAttribute(): string
    {
        return match ($this->action) {
            'created'  => 'bg-green-100 text-green-700',
            'updated'  => 'bg-blue-100 text-blue-700',
            'deleted'  => 'bg-red-100 text-red-700',
            'restored' => 'bg-yellow-100 text-yellow-700',
            default    => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Icon name based on action type.
     */
    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'created'  => 'plus-circle',
            'updated'  => 'pencil-square',
            'deleted'  => 'trash',
            'restored' => 'arrow-path',
            default    => 'information-circle',
        };
    }

    /**
     * Summary of changed fields for display.
     */
    public function getChangesSummaryAttribute(): string
    {
        if ($this->action === 'created') {
            return 'Record created';
        }

        if ($this->action === 'deleted') {
            return 'Record deleted';
        }

        if ($this->action === 'updated' && is_array($this->new_values)) {
            $fields = array_keys($this->new_values);
            $count = count($fields);

            if ($count === 0) {
                return 'No changes recorded';
            }

            if ($count <= 3) {
                return 'Changed: ' . implode(', ', $fields);
            }

            return 'Changed: ' . implode(', ', array_slice($fields, 0, 3)) . " +{$count} more";
        }

        return '—';
    }

    /* ── Scopes ── */

    public function scopeForGym($query, ?int $gymId)
    {
        if ($gymId) {
            $query->where('gym_id', $gymId);
        }

        return $query;
    }

    public function scopeByAction($query, ?string $action)
    {
        if ($action) {
            $query->where('action', $action);
        }

        return $query;
    }

    public function scopeByModel($query, ?string $modelType)
    {
        if ($modelType) {
            $query->where('model_type', 'like', '%' . $modelType);
        }

        return $query;
    }

    public function scopeByUser($query, ?int $userId)
    {
        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query;
    }

    public function scopeByDateRange($query, ?string $from, ?string $to)
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }
}
