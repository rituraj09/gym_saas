<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberWorkoutDay extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'member_exercise_plan_id', 'day_of_week',
        'exercise_template_id', 'notes', 'sort_order',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'sort_order' => 'integer',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(MemberExercisePlan::class, 'member_exercise_plan_id');
    }

    public function exerciseTemplate(): BelongsTo
    {
        return $this->belongsTo(ExerciseTemplate::class);
    }

    public function workoutLogs(): HasMany
    {
        return $this->hasMany(MemberWorkoutLog::class);
    }

    public function getDayNameAttribute(): string
    {
        $days = [
            1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday',
            4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday',
        ];

        return $days[$this->day_of_week] ?? 'Unknown';
    }

    public function getShortDayAttribute(): string
    {
        $days = [
            1 => 'Mon', 2 => 'Tue', 3 => 'Wed',
            4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun',
        ];

        return $days[$this->day_of_week] ?? '?';
    }
}
