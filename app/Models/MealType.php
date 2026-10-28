<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealType extends Model
{
    protected $fillable = ['name', 'code', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function meals(): HasMany
    {
        return $this->hasMany(DietPlanMeal::class);
    }
}
