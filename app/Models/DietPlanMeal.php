<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DietPlanMeal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'diet_plan_template_id', 'meal_type_id', 'meal_time', 'notes', 'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(DietPlanTemplate::class, 'diet_plan_template_id');
    }

    public function mealType(): BelongsTo
    {
        return $this->belongsTo(MealType::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DietPlanMealItem::class)->orderBy('sort_order');
    }

    public function getTotalCaloriesAttribute(): int
    {
        return $this->items->sum('calories');
    }

    public function getTotalProteinAttribute(): float
    {
        return $this->items->sum('protein_g');
    }
}
