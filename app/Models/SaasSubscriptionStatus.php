<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaasSubscriptionStatus extends Model
{
    protected $fillable = ['name', 'code'];

    public function gymSubscriptions(): HasMany
    {
        return $this->hasMany(GymSubscription::class);
    }
}
