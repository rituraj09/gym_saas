<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserRole extends Model
{
    protected $fillable = ['code', 'name'];

    public function assignments(): HasMany
    {
        return $this->hasMany(UserRoleAssignment::class, 'user_role_id');
    }
}
