<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppNotification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'gym_id',
        'type',
        'title',
        'message',
        'link',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public const TYPE_MEMBERSHIP_EXPIRING = 'membership_expiring';
    public const TYPE_MEMBERSHIP_EXPIRED = 'membership_expired';
    public const TYPE_SUBSCRIPTION_EXPIRING = 'subscription_expiring';
    public const TYPE_SUBSCRIPTION_EXPIRED = 'subscription_expired';
    public const TYPE_PAYMENT_RECEIVED = 'payment_received';

    public const TYPE_LABELS = [
        self::TYPE_MEMBERSHIP_EXPIRING  => 'Membership Expiring',
        self::TYPE_MEMBERSHIP_EXPIRED   => 'Membership Expired',
        self::TYPE_SUBSCRIPTION_EXPIRING => 'Subscription Expiring',
        self::TYPE_SUBSCRIPTION_EXPIRED  => 'Subscription Expired',
        self::TYPE_PAYMENT_RECEIVED     => 'Payment Received',
    ];

    public const TYPE_ICONS = [
        self::TYPE_MEMBERSHIP_EXPIRING  => 'exclamation',
        self::TYPE_MEMBERSHIP_EXPIRED   => 'x-mark',
        self::TYPE_SUBSCRIPTION_EXPIRING => 'exclamation',
        self::TYPE_SUBSCRIPTION_EXPIRED  => 'x-mark',
        self::TYPE_PAYMENT_RECEIVED     => 'currency',
    ];

    public const TYPE_COLORS = [
        self::TYPE_MEMBERSHIP_EXPIRING  => 'bg-yellow-100 text-yellow-700',
        self::TYPE_MEMBERSHIP_EXPIRED   => 'bg-red-100 text-red-700',
        self::TYPE_SUBSCRIPTION_EXPIRING => 'bg-orange-100 text-orange-700',
        self::TYPE_SUBSCRIPTION_EXPIRED  => 'bg-red-100 text-red-700',
        self::TYPE_PAYMENT_RECEIVED     => 'bg-green-100 text-green-700',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }

    public function getTypeIconAttribute(): string
    {
        return self::TYPE_ICONS[$this->type] ?? 'bell';
    }

    public function getTypeColorAttribute(): string
    {
        return self::TYPE_COLORS[$this->type] ?? 'bg-gray-100 text-gray-700';
    }
}
