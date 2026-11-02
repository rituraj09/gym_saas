<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class Trainer extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'user_id', 'gym_id', 'trainer_code', 'specialization',
        'qualification', 'experience_years', 'joining_date', 'profile_photo',
        'date_of_birth', 'gender_id', 'blood_group_id', 'address',
    ];

    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
            'date_of_birth' => 'date',
            'experience_years' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function gender(): BelongsTo
    {
        return $this->belongsTo(GenderType::class, 'gender_id');
    }

    public function bloodGroup(): BelongsTo
    {
        return $this->belongsTo(BloodGroupType::class, 'blood_group_id');
    }

        public function assignments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TrainerMemberAssignment::class);
    }

    public function activeAssignments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TrainerMemberAssignment::class)->active();
    }

    public function activeMembers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'trainer_member_assignments')
            ->wherePivot('assignment_status_id', function ($query) {
                $query->select('id')->from('assignment_statuses')->where('code', 'active');
            })
            ->withPivot(['start_date', 'end_date', 'id'])
            ->withTimestamps();
    }

    /**
     * All members in this trainer's gym.
     */
    public function gymMembers()
    {
        return  Member::where('gym_id', $this->gym_id);
    }

    /**
     * IDs of members specifically assigned to this trainer.
     */
    public function getAssignedMemberIds(): array
    {
        return  TrainerMemberAssignment::where('trainer_id', $this->id)
            ->whereHas('status', fn($q) => $q->where('code', 'active'))
            ->pluck('member_id')
            ->toArray();
    }

    /**
     * Check if a member belongs to this trainer's gym.
     */
    public function canManageMember(int $memberId): bool
    {
        return  Member::where('id', $memberId)
            ->where('gym_id', $this->gym_id)
            ->exists();
    }
}
