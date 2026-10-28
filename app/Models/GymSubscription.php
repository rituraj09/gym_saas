<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GymSubscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'gym_id', 'saas_plan_id', 'saas_subscription_status_id',
        'start_date', 'end_date', 'trial_ends_at', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'trial_ends_at' => 'datetime',
    ];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function saasPlan(): BelongsTo
    {
        return $this->belongsTo(SaasPlan::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(SaasSubscriptionStatus::class, 'saas_subscription_status_id');
    }

    public function scopeActive($query)
    {
        $activeStatusId = SaasSubscriptionStatus::where('code', 'active')->value('id');
        $trialStatusId = SaasSubscriptionStatus::where('code', 'trial')->value('id');

        return $query->whereIn('saas_subscription_status_id', array_filter([$activeStatusId, $trialStatusId]));
    }

    public function scopeForGym($query, $gymId)
    {
        return $query->where('gym_id', $gymId);
    }

    public function isActive(): bool
    {
        return $this->status && in_array($this->status->code, ['active', 'trial']);
    }

    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }

    public function isOnTrial(): bool
    {
        return $this->status && $this->status->code === 'trial'
            && $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->end_date) return null;
        $days = now()->diffInDays($this->end_date, false);
        return max(0, (int) $days);
    }

    public function getStatusBadgeAttribute(): string
    {
        if (!$this->status) return 'bg-gray-100 text-gray-800';

        return match ($this->status->code) {
            'active' => 'bg-green-100 text-green-800',
            'trial' => 'bg-blue-100 text-blue-800',
            'expired' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-600',
            'suspended' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
