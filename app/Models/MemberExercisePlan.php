<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberExercisePlan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'gym_id', 'member_id', 'created_by_user_id',
        'name', 'description', 'start_date', 'end_date', 'is_active',
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function workoutDays(): HasMany
    {
        return $this->hasMany(MemberWorkoutDay::class)->orderBy('sort_order')->orderBy('day_of_week');
    }

    public function workoutLogs(): HasMany
    {
        return $this->hasMany(MemberWorkoutLog::class);
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

    public function getDayNamesAttribute(): array
    {
        return [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        ];
    }

    public function getWorkoutDaysByDayAttribute(): array
    {
        $grouped = [];
        foreach ($this->workoutDays as $day) {
            $grouped[$day->day_of_week][] = $day;
        }
        return $grouped;
    }

    public function getThisWeekCompletionRateAttribute(): float
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $scheduledDays = $this->workoutDays->count();
        if ($scheduledDays === 0) return 0;

        $completedCount = $this->workoutLogs()
            ->whereBetween('workout_date', [$startOfWeek, $endOfWeek])
            ->whereHas('status', fn($q) => $q->where('code', 'completed'))
            ->count();

        return round(($completedCount / $scheduledDays) * 100);
    }
}
