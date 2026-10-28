<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberWorkoutLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'member_exercise_plan_id', 'member_workout_day_id',
        'workout_log_status_id', 'workout_date', 'notes',
    ];

    protected $casts = [
        'workout_date' => 'date',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(MemberExercisePlan::class, 'member_exercise_plan_id');
    }

    public function workoutDay(): BelongsTo
    {
        return $this->belongsTo(MemberWorkoutDay::class, 'member_workout_day_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(WorkoutLogStatus::class, 'workout_log_status_id');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('workout_date', $date);
    }

    public function scopeForWeek($query, $startDate)
    {
        $endDate = \Carbon\Carbon::parse($startDate)->endOfWeek();
        return $query->whereBetween('workout_date', [$startDate, $endDate]);
    }

    public function isCompleted(): bool
    {
        return $this->status && $this->status->code === 'completed';
    }

    public function isSkipped(): bool
    {
        return $this->status && $this->status->code === 'skipped';
    }
}
