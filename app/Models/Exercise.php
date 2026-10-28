<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exercise extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'muscle_group_id',
        'equipment_type_id', 'difficulty_level_id', 'instructions',
        'image', 'video_url', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function muscleGroup(): BelongsTo
    {
        return $this->belongsTo(MuscleGroup::class);
    }

    public function equipmentType(): BelongsTo
    {
        return $this->belongsTo(EquipmentType::class);
    }

    public function difficultyLevel(): BelongsTo
    {
        return $this->belongsTo(DifficultyLevel::class);
    }

    public function templateItems(): HasMany
    {
        return $this->hasMany(ExerciseTemplateItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForMuscleGroup($query, $muscleGroupId)
    {
        return $query->where('muscle_group_id', $muscleGroupId);
    }

    public function scopeForEquipment($query, $equipmentTypeId)
    {
        return $query->where('equipment_type_id', $equipmentTypeId);
    }

    public function scopeForDifficulty($query, $difficultyLevelId)
    {
        return $query->where('difficulty_level_id', $difficultyLevelId);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', "%{$term}%");
    }
}
