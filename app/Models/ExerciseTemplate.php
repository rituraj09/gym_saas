<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExerciseTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'gym_id', 'created_by_user_id', 'name', 'description',
        'target_muscle_group_id', 'estimated_duration_minutes', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'estimated_duration_minutes' => 'integer',
    ];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function targetMuscleGroup(): BelongsTo
    {
        return $this->belongsTo(MuscleGroup::class, 'target_muscle_group_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ExerciseTemplateItem::class)->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForGym($query, $gymId)
    {
        return $query->where('gym_id', $gymId);
    }

    public function getExerciseCountAttribute(): int
    {
        return $this->items->count();
    }

    public function getTotalSetsAttribute(): int
    {
        return $this->items->sum('sets');
    }
}
