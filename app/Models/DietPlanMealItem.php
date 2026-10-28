<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DietPlanMealItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'diet_plan_meal_id', 'food_name', 'quantity',
        'calories', 'protein_g', 'carbs_g', 'fat_g', 'notes', 'sort_order',
    ];

    protected $casts = [
        'calories' => 'integer',
        'protein_g' => 'decimal:1',
        'carbs_g' => 'decimal:1',
        'fat_g' => 'decimal:1',
        'sort_order' => 'integer',
    ];

    public function meal(): BelongsTo
    {
        return $this->belongsTo(DietPlanMeal::class, 'diet_plan_meal_id');
    }
}
