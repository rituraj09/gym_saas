<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MuscleGroup extends Model
{
    protected $fillable = ['name', 'code', 'description'];

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }

    public function exerciseTemplates(): HasMany
    {
        return $this->hasMany(ExerciseTemplate::class, 'target_muscle_group_id');
    }
}
