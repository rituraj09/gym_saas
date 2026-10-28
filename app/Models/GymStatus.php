<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GymStatus extends Model
{
    protected $fillable = ['code', 'name'];

    public function gyms(): HasMany
    {
        return $this->hasMany(Gym::class);
    }
}
