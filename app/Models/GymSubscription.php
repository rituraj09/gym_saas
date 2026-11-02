<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;

class GymSubscription extends Model
{
    use SoftDeletes, LogsActivity;

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

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SaasPayment::class, 'gym_subscription_id');
    }

    public function totalPaid(): float
    {
        return $this->payments()
            ->whereHas('paymentStatus', fn($q) => $q->where('code', 'completed'))
            ->sum('amount');
    }

        public function needsSuspension(): bool
    {
        $graceDays = config('gymsaas.subscription_grace_days', 7);

        // Trial expired
        if ($this->isOnTrial() === false && $this->status && $this->status->code === 'trial') {
            return true;
        }

        // Active subscription past end_date + grace period
        if ($this->isActive() && $this->end_date) {
            $graceDeadline = $this->end_date->copy()->addDays($graceDays);
            if ($graceDeadline->isPast()) {
                return true;
            }
        }

        return false;
    }

    public function isWithinGracePeriod(): bool
    {
        $graceDays = config('gymsaas.subscription_grace_days', 7);

        if (!$this->end_date || !$this->isActive()) {
            return false;
        }

        return $this->end_date->isPast()
            && $this->end_date->copy()->addDays($graceDays)->isFuture();
    }




    public function isExpired(): bool
    {
        return $this->status && $this->status->code === 'expired';
    }

    public function isExpiringSoon(int $days = 7): bool
    {
        if (!$this->isActive() || !$this->end_date) {
            return false;
        }

        $today = now()->startOfDay();
        $expiryDate = $this->end_date->copy()->startOfDay();

        return $expiryDate->greaterThanOrEqualTo($today)
            && $expiryDate->lessThanOrEqualTo($today->copy()->addDays($days));
    }


    public function hasFeature(string $feature): bool
    {
        if (!$this->gym) {
            return $this->saasPlan && $this->saasPlan->hasFeature($feature);
        }

        // Delegate to Gym model which checks overrides first
        return $this->gym->isModuleEnabled($feature);
    }
    public function canAddTrainer(): bool
    {
        if (!$this->saasPlan || $this->saasPlan->isUnlimited('trainers')) {
            return true;
        }

        $currentCount = $this->gym->trainers()->count();
        return $currentCount < $this->saasPlan->max_trainers;
    }

    public function canAddMember(): bool
    {
        if (!$this->saasPlan || $this->saasPlan->isUnlimited('members')) {
            return true;
        }

        $currentCount = $this->gym->members()->count();
        return $currentCount < $this->saasPlan->max_members;
    }

    public function getRemainingTrainers(): ?int
    {
        if (!$this->saasPlan || $this->saasPlan->isUnlimited('trainers')) {
            return null;
        }

        return max(0, $this->saasPlan->max_trainers - $this->gym->trainers()->count());
    }

    public function getRemainingMembers(): ?int
    {
        if (!$this->saasPlan || $this->saasPlan->isUnlimited('members')) {
            return null;
        }

        return max(0, $this->saasPlan->max_members - $this->gym->members()->count());
    }
}
