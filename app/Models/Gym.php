<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Gym extends Model
{
    use HasFactory, SoftDeletes;

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

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(GymSubscription::class)->active()->latest('start_date');
    }
}
