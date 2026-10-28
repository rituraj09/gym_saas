<?php

namespace Tests\Feature\Phase2;

use App\Models\AccountStatus;
use App\Models\BloodGroupType;
use App\Models\GenderType;
use App\Models\Gym;
use App\Models\GymOwner;
use App\Models\GymStatus;
use App\Models\Member;
use App\Models\PlatformAdmin;
use App\Models\Trainer;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    // --- Lookup Table Tests ---

    public function test_account_statuses_are_seeded(): void
    {
        $this->assertDatabaseCount('account_statuses', 4);
        $this->assertDatabaseHas('account_statuses', ['code' => 'active']);
        $this->assertDatabaseHas('account_statuses', ['code' => 'inactive']);
        $this->assertDatabaseHas('account_statuses', ['code' => 'suspended']);
        $this->assertDatabaseHas('account_statuses', ['code' => 'pending']);
    }

    public function test_user_roles_are_seeded(): void
    {
        $this->assertDatabaseCount('user_roles', 4);
        $this->assertDatabaseHas('user_roles', ['code' => 'platform_admin']);
        $this->assertDatabaseHas('user_roles', ['code' => 'gym_owner']);
        $this->assertDatabaseHas('user_roles', ['code' => 'trainer']);
        $this->assertDatabaseHas('user_roles', ['code' => 'member']);
    }

    public function test_gender_types_are_seeded(): void
    {
        $this->assertDatabaseCount('gender_types', 3);
        $this->assertDatabaseHas('gender_types', ['code' => 'male']);
        $this->assertDatabaseHas('gender_types', ['code' => 'female']);
    }

    public function test_blood_group_types_are_seeded(): void
    {
        $this->assertDatabaseCount('blood_group_types', 8);
        $this->assertDatabaseHas('blood_group_types', ['code' => 'o_positive']);
    }

    public function test_gym_statuses_are_seeded(): void
    {
        $this->assertDatabaseCount('gym_statuses', 5);
        $this->assertDatabaseHas('gym_statuses', ['code' => 'trial']);
    }

    // --- User Tests ---

    public function test_user_can_be_created_with_phone_and_status(): void
    {
        $status = AccountStatus::where('code', 'active')->first();

        $user = User::factory()->create([
            'phone' => '+1234567890',
            'account_status_id' => $status->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'phone' => '+1234567890',
            'account_status_id' => $status->id,
        ]);
    }

    public function test_user_belongs_to_account_status(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(AccountStatus::class, $user->accountStatus);
        $this->assertEquals('active', $user->accountStatus->code);
    }

    // --- Gym Tests ---

    public function test_gym_can_be_created(): void
    {
        $gym = Gym::factory()->create();

        $this->assertDatabaseHas('gyms', ['id' => $gym->id]);
        $this->assertNotNull($gym->code);
    }

    public function test_gym_belongs_to_gym_status(): void
    {
        $gym = Gym::factory()->create();

        $this->assertInstanceOf(GymStatus::class, $gym->status);
    }

    // --- Gym Owner Tests ---

    public function test_gym_owner_can_be_created(): void
    {
        $gym = Gym::factory()->create();
        $user = User::factory()->create();

        $owner = GymOwner::factory()->create([
            'user_id' => $user->id,
            'gym_id' => $gym->id,
        ]);

        $this->assertDatabaseHas('gym_owners', ['id' => $owner->id]);
    }

    public function test_gym_owner_belongs_to_user_and_gym(): void
    {
        $gym = Gym::factory()->create();
        $user = User::factory()->create();

        $owner = GymOwner::factory()->create([
            'user_id' => $user->id,
            'gym_id' => $gym->id,
        ]);

        $this->assertEquals($user->id, $owner->user->id);
        $this->assertEquals($gym->id, $owner->gym->id);
    }

    // --- Trainer Tests ---

    public function test_trainer_can_be_created(): void
    {
        $gym = Gym::factory()->create();
        $user = User::factory()->create();

        $trainer = Trainer::factory()->create([
            'user_id' => $user->id,
            'gym_id' => $gym->id,
        ]);

        $this->assertDatabaseHas('trainers', ['id' => $trainer->id]);
    }

    public function test_trainer_belongs_to_user_and_gym(): void
    {
        $gym = Gym::factory()->create();
        $user = User::factory()->create();

        $trainer = Trainer::factory()->create([
            'user_id' => $user->id,
            'gym_id' => $gym->id,
        ]);

        $this->assertEquals($user->id, $trainer->user->id);
        $this->assertEquals($gym->id, $trainer->gym->id);
    }

    // --- Member Tests ---

    public function test_member_can_be_created(): void
    {
        $gym = Gym::factory()->create();
        $user = User::factory()->create();

        $member = Member::factory()->create([
            'user_id' => $user->id,
            'gym_id' => $gym->id,
        ]);

        $this->assertDatabaseHas('members', ['id' => $member->id]);
    }

    public function test_member_belongs_to_user_gym_gender_and_blood_group(): void
    {
        $gym = Gym::factory()->create();
        $user = User::factory()->create();

        $member = Member::factory()->create([
            'user_id' => $user->id,
            'gym_id' => $gym->id,
        ]);

        $this->assertInstanceOf(User::class, $member->user);
        $this->assertInstanceOf(Gym::class, $member->gym);
        $this->assertInstanceOf(GenderType::class, $member->gender);
        $this->assertInstanceOf(BloodGroupType::class, $member->bloodGroup);
    }

    // --- Role Assignment Tests ---

    public function test_user_can_be_assigned_roles(): void
    {
        $user = User::factory()->create();
        $ownerRole = UserRole::where('code', 'gym_owner')->first();

        $user->roles()->attach($ownerRole->id);

        $this->assertTrue($user->hasRole('gym_owner'));
        $this->assertFalse($user->hasRole('trainer'));
    }

    public function test_user_can_have_multiple_roles(): void
    {
        $user = User::factory()->create();
        $ownerRole = UserRole::where('code', 'gym_owner')->first();
        $trainerRole = UserRole::where('code', 'trainer')->first();

        $user->roles()->attach([$ownerRole->id, $trainerRole->id]);

        $this->assertTrue($user->hasRole('gym_owner'));
        $this->assertTrue($user->hasRole('trainer'));
        $this->assertCount(2, $user->roles);
    }

    // --- Foreign Key Constraint Tests ---

    public function test_gym_owner_cannot_reference_nonexistent_user(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        GymOwner::factory()->create([
            'user_id' => 99999,
            'gym_id' => Gym::factory()->create()->id,
        ]);
    }

    public function test_member_cannot_reference_nonexistent_gym(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Member::factory()->create([
            'user_id' => User::factory()->create()->id,
            'gym_id' => 99999,
        ]);
    }

    // --- Gym Has Many Tests ---

    public function test_gym_has_many_trainers_and_members(): void
    {
        $gym = Gym::factory()->create();

        Trainer::factory()->count(3)->create(['gym_id' => $gym->id]);
        Member::factory()->count(5)->create(['gym_id' => $gym->id]);

        $this->assertCount(3, $gym->trainers);
        $this->assertCount(5, $gym->members);
    }

    // --- Platform Admin Tests ---

    public function test_platform_admin_can_be_created(): void
    {
        $user = User::factory()->create();

        $admin = PlatformAdmin::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('platform_admins', ['id' => $admin->id]);
        $this->assertEquals($user->id, $admin->user->id);
    }
}
