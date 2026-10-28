<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BloodGroupType extends Model
{
    protected $fillable = ['code', 'name'];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class, 'blood_group_id');
    }
}
