<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class Member extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'user_id', 'gym_id', 'member_code', 'date_of_birth',
        'gender_id', 'blood_group_id', 'address',
        'emergency_contact_name', 'emergency_contact_phone',
        'joining_date', 'profile_photo',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'joining_date' => 'date',
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

    public function activeAssignment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(TrainerMemberAssignment::class)->active()->latestOfMany('start_date');
    }

    public function currentTrainer(): ?Trainer
    {
        return $this->activeAssignment?->trainer;
    }
        public function memberships(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Membership::class);
    }

   public function activeMembership(): \Illuminate\Database\Eloquent\Relations\HasOne
{
    return $this->hasOne(Membership::class)
        ->whereHas('status', function ($query) {
            $query->where('code', 'active');
        })
        ->whereDate('end_date', '>=', now()->toDateString())
        ->latestOfMany('end_date');
}



    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(GymMemberPayment::class);
    }



    public function latestMembership()
    {
        return $this->hasOne(Membership::class)->latest('end_date');
    }

    /**
     * Currently active exercise plan.
     */
    public function activeExercisePlan()
    {
        return $this->hasOne(MemberExercisePlan::class)
            ->where('is_active', true)
            ->latest();
    }

    /**
     * Currently active diet plan.
     */
    public function activeDietPlan()
    {
        return $this->hasOne(MemberDietPlan::class)
            ->where('is_active', true)
            ->latest();
    }

    /**
     * Trainers assigned to this member.
     */
    public function assignedTrainers()
    {
        return $this->belongsToMany(
         Trainer::class,
            'trainer_member_assignments'
        )->wherePivot('assignment_status_id', function ($q) {
            $q->select('id')
            ->from('assignment_statuses')
            ->where('code', 'active');
        });
    }
}
