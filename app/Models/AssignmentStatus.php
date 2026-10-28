<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssignmentStatus extends Model
{
    protected $fillable = ['code', 'name'];

    public function assignments(): HasMany
    {
        return $this->hasMany(TrainerMemberAssignment::class);
    }
}
