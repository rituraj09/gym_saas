<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DifficultyLevel extends Model
{
    protected $fillable = ['name', 'code'];

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }
}
