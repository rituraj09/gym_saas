<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'account_status_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function accountStatus(): BelongsTo
    {
        return $this->belongsTo(AccountStatus::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(UserRole::class, 'user_role_assignments', 'user_id', 'user_role_id')
            ->withTimestamps();
    }

    public function platformAdmin(): HasOne
    {
        return $this->hasOne(PlatformAdmin::class);
    }

    public function gymOwner(): HasOne
    {
        return $this->hasOne(GymOwner::class);
    }

    public function trainer(): HasOne
    {
        return $this->hasOne(Trainer::class);
    }

    public function member(): HasOne
    {
        return $this->hasOne(Member::class);
    }

    public function hasRole(string $roleCode): bool
    {
        return $this->roles()->where('code', $roleCode)->exists();
    }

    public function hasAnyRole(array $roleCodes): bool
    {
        return $this->roles()->whereIn('code', $roleCodes)->exists();
    }

    public function isActive(): bool
    {
        return $this->accountStatus && $this->accountStatus->code === 'active';
    }

    public function getPrimaryRole(): ?string
    {
        $priority = ['platform_admin', 'gym_owner', 'trainer', 'member'];

        foreach ($priority as $roleCode) {
            if ($this->hasRole($roleCode)) {
                return $roleCode;
            }
        }

        return null;
    }

    public function notifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AppNotification::class)->latest();
    }

    public function unreadNotifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AppNotification::class)->unread()->latest();
    }

    public function unreadNotificationCount(): int
    {
        return $this->unreadNotifications()->count();
    }
}
