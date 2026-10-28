<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainerMemberAssignment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'gym_id',
        'trainer_id',
        'member_id',
        'assignment_status_id',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
        ];
    }

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(AssignmentStatus::class, 'assignment_status_id');
    }

    public function isActive(): bool
    {
        return $this->status && $this->status->code === 'active';
    }

    public function scopeActive($query)
    {
        return $query->whereHas('status', fn ($q) => $q->where('code', 'active'));
    }

    public function scopeForGym($query, int $gymId)
    {
        return $query->where('gym_id', $gymId);
    }
}
