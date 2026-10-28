<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutLogStatus extends Model
{
    protected $fillable = ['name', 'code'];

    public function workoutLogs(): HasMany
    {
        return $this->hasMany(MemberWorkoutLog::class);
    }
}
