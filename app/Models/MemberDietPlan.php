<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberDietPlan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'gym_id', 'member_id', 'diet_plan_template_id',
        'created_by_user_id', 'start_date', 'end_date', 'notes', 'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function dietPlanTemplate(): BelongsTo
    {
        return $this->belongsTo(DietPlanTemplate::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function dietLogs(): HasMany
    {
        return $this->hasMany(MemberDietLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForGym($query, $gymId)
    {
        return $query->where('gym_id', $gymId);
    }

    public function scopeForMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    public function getThisWeekAdherenceAttribute(): float
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $totalDays = $startOfWeek->diffInDays($endOfWeek) + 1;
        $followedDays = $this->dietLogs()
            ->whereBetween('log_date', [$startOfWeek, $endOfWeek])
            ->where('status', 'followed')
            ->count();

        return $totalDays > 0 ? round(($followedDays / $totalDays) * 100) : 0;
    }
}
