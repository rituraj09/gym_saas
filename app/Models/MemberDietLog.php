<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberDietLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'member_diet_plan_id', 'log_date', 'status', 'notes',
    ];

    protected $casts = [
        'log_date' => 'date',
    ];

    public function dietPlan(): BelongsTo
    {
        return $this->belongsTo(MemberDietPlan::class, 'member_diet_plan_id');
    }

    public function isFollowed(): bool
    {
        return $this->status === 'followed';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'followed' => 'Followed',
            'partially' => 'Partially Followed',
            'not_followed' => 'Not Followed',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'followed' => 'bg-green-100 text-green-800',
            'partially' => 'bg-yellow-100 text-yellow-800',
            'not_followed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
