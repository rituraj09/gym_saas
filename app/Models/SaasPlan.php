<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\LogsActivity;

class SaasPlan extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'name', 'code', 'description', 'monthly_price', 'yearly_price',
        'max_trainers', 'max_members', 'has_exercise_module',
        'has_diet_module', 'has_reports_module', 'has_priority_support',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'max_trainers' => 'integer',
        'max_members' => 'integer',
        'has_exercise_module' => 'boolean',
        'has_diet_module' => 'boolean',
        'has_reports_module' => 'boolean',
        'has_priority_support' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function gymSubscriptions(): HasMany
    {
        return $this->hasMany(GymSubscription::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getMaxTrainersLabelAttribute(): string
    {
        return $this->max_trainers === 0 ? 'Unlimited' : number_format($this->max_trainers);
    }

    public function getMaxMembersLabelAttribute(): string
    {
        return $this->max_members === 0 ? 'Unlimited' : number_format($this->max_members);
    }

    public function getFeaturesListAttribute(): array
    {
        $features = [];
        if ($this->has_exercise_module) $features[] = 'Exercise Plans';
        if ($this->has_diet_module) $features[] = 'Diet Plans';
        if ($this->has_reports_module) $features[] = 'Reports';
        if ($this->has_priority_support) $features[] = 'Priority Support';
        return $features;
    }

    /**
     * Check if a specific feature/module is enabled on this plan.
     * Accepted values: 'exercise', 'diet', 'reports', 'priority_support'
     */
    public function hasFeature(string $feature): bool
    {
        $map = [
            'exercise'         => 'has_exercise_module',
            'diet'             => 'has_diet_module',
            'reports'          => 'has_reports_module',
            'priority_support' => 'has_priority_support',
        ];

        $column = $map[$feature] ?? null;

        if (!$column) {
            return false;
        }

        return (bool) $this->{$column};
    }

    /**
     * Check if a resource limit is unlimited (0 = unlimited in your schema).
     */
    public function isUnlimited(string $resource): bool
    {
        $column = 'max_' . $resource;
        return (int) $this->{$column} === 0;
    }
}
