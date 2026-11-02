<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class Membership extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'gym_id', 'member_id', 'membership_plan_id',
        'membership_status_id', 'start_date', 'end_date', 'price', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
            'price'      => 'decimal:2',
        ];
    }

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class, 'membership_plan_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(MembershipStatus::class, 'membership_status_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(GymMemberPayment::class);
    }

    public function isActive(): bool
    {
        return $this->status && $this->status->code === 'active';
    }

    public function isExpired(): bool
    {
        return $this->end_date->isPast();
    }

    public function isExpiringSoon(int $days = 7): bool
    {
        return ! $this->isExpired() && $this->end_date->diffInDays(now()) <= $days;
    }

    public function scopeActive($query)
    {
        return $query->whereHas('status', fn ($q) => $q->where('code', 'active'))
                     ->where('end_date', '>=', now());
    }
}
