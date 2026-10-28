<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GenderType extends Model
{
    protected $fillable = ['code', 'name'];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class, 'gender_id');
    }
}
