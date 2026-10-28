<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseTemplateItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'exercise_template_id', 'exercise_id', 'sets', 'reps',
        'rest_seconds', 'weight_kg', 'notes', 'sort_order',
    ];

    protected $casts = [
        'sets' => 'integer',
        'reps' => 'integer',
        'rest_seconds' => 'integer',
        'weight_kg' => 'decimal:1',
        'sort_order' => 'integer',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(ExerciseTemplate::class, 'exercise_template_id');
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function getRestFormattedAttribute(): string
    {
        if ($this->rest_seconds < 60) {
            return $this->rest_seconds . 's';
        }

        $minutes = intdiv($this->rest_seconds, 60);
        $seconds = $this->rest_seconds % 60;

        return $seconds > 0 ? "{$minutes}m {$seconds}s" : "{$minutes}m";
    }
}
