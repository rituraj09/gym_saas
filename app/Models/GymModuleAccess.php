<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GymModuleAccess extends Model
{
    use SoftDeletes;

    protected $table = 'gym_module_access';

    protected $fillable = [
        'gym_id',
        'module_code',
        'is_enabled',
        'reason',
        'granted_by',
        'expires_at',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public const MODULES = [
        'exercise'         => 'Exercise Plans',
        'diet'             => 'Diet Plans',
        'reports'          => 'Reports',
        'priority_support' => 'Priority Support',
    ];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function grantedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    public function getModuleLabelAttribute(): string
    {
        return self::MODULES[$this->module_code] ?? ucfirst($this->module_code);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isActive(): bool
    {
        return !$this->isExpired();
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }
}
