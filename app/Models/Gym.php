<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Traits\LogsActivity;

class Gym extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name', 'code', 'email', 'phone', 'address',
        'city', 'state', 'zip_code', 'logo',
        'gym_status_id', 'established_date',
    ];

    protected function casts(): array
    {
        return ['established_date' => 'date'];
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(GymStatus::class, 'gym_status_id');
    }

    public function owners(): HasMany
    {
        return $this->hasMany(GymOwner::class);
    }

    public function trainers(): HasMany
    {
        return $this->hasMany(Trainer::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(GymSubscription::class);
    }

    // public function activeSubscription(): HasOne
    // {
    //     return $this->hasOne(GymSubscription::class)->active()->latest('start_date');
    // }

    public function gymSubscriptions(): HasMany
    {
        return $this->hasMany(GymSubscription::class);
    }

   public function activeSubscription(): HasOne
    {
        return $this->hasOne(GymSubscription::class)
            ->whereHas('status', function ($query) {
                $query->whereIn('code', ['active', 'trial']);
            })
            ->latestOfMany('start_date');
    }

    public function latestSubscription(): HasOne
    {
        return $this->hasOne(GymSubscription::class)
            ->latestOfMany('start_date')
            ->with(['saasPlan', 'status']);
    }


    public function moduleAccess(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(GymModuleAccess::class);
    }

    public function activeModuleAccess(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(GymModuleAccess::class)->active();
    }

    /**
     * Check if a module is enabled for this gym.
     * Resolution: override (if active) → plan default → false
     */
    public function isModuleEnabled(string $moduleCode): bool
    {
        // 1. Check for active override
        $override = $this->activeModuleAccess()
            ->where('module_code', $moduleCode)
            ->first();

        if ($override) {
            return $override->is_enabled;
        }

        // 2. Fall back to plan default
        $subscription = $this->activeSubscription;

        if (!$subscription || !$subscription->saasPlan) {
            return false;
        }

        return $subscription->saasPlan->hasFeature($moduleCode);
    }
}
