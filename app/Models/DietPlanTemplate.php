<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DietPlanTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'gym_id', 'created_by_user_id', 'name', 'description',
        'target_goal', 'total_calories', 'total_protein_g',
        'total_carbs_g', 'total_fat_g', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_calories' => 'integer',
        'total_protein_g' => 'decimal:1',
        'total_carbs_g' => 'decimal:1',
        'total_fat_g' => 'decimal:1',
    ];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function meals(): HasMany
    {
        return $this->hasMany(DietPlanMeal::class)->orderBy('sort_order');
    }

    public function memberDietPlans(): HasMany
    {
        return $this->hasMany(MemberDietPlan::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForGym($query, $gymId)
    {
        return $query->where('gym_id', $gymId);
    }

    public function getCalculatedCaloriesAttribute(): int
    {
        return $this->meals->sum(fn($meal) => $meal->items->sum('calories'));
    }

    public function getCalculatedProteinAttribute(): float
    {
        return $this->meals->sum(fn($meal) => $meal->items->sum('protein_g'));
    }

    public function getCalculatedCarbsAttribute(): float
    {
        return $this->meals->sum(fn($meal) => $meal->items->sum('carbs_g'));
    }

    public function getCalculatedFatAttribute(): float
    {
        return $this->meals->sum(fn($meal) => $meal->items->sum('fat_g'));
    }
}
