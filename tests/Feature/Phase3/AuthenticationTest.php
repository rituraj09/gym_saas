<?php

namespace Tests\Feature\Phase3;

use App\Models\AccountStatus;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function createUserWithRole(string $roleCode, string $statusCode = 'active'): User
    {
        $status = AccountStatus::where('code', $statusCode)->first();
        $role = UserRole::where('code', $roleCode)->first();

        $user = User::factory()->create([
            'account_status_id' => $status->id,
        ]);

        $user->roles()->attach($role->id);

        return $user;
    }

    // --- Login Tests ---

    public function test_active_admin_is_redirected_to_admin_dashboard(): void
    {
        $user = $this->createUserWithRole('platform_admin');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_active_owner_is_redirected_to_owner_dashboard(): void
    {
        $user = $this->createUserWithRole('gym_owner');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('owner.dashboard'));
    }

    public function test_active_trainer_is_redirected_to_trainer_dashboard(): void
    {
        $user = $this->createUserWithRole('trainer');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('trainer.dashboard'));
    }

    public function test_active_member_is_redirected_to_member_dashboard(): void
    {
        $user = $this->createUserWithRole('member');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('member.dashboard'));
    }

    // --- Account Status Tests ---

    public function test_suspended_user_cannot_login(): void
    {
        $user = $this->createUserWithRole('gym_owner', 'suspended');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = $this->createUserWithRole('gym_owner', 'inactive');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_pending_user_cannot_login(): void
    {
        $user = $this->createUserWithRole('gym_owner', 'pending');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_user_without_role_cannot_login(): void
    {
        $status = AccountStatus::where('code', 'active')->first();
        $user = User::factory()->create([
            'account_status_id' => $status->id,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    // --- Role Access Tests ---

    public function test_admin_can_access_admin_dashboard(): void
    {
        $user = $this->createUserWithRole('platform_admin');

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Platform Admin Dashboard');
    }

    public function test_admin_cannot_access_owner_dashboard(): void
    {
        $user = $this->createUserWithRole('platform_admin');

        $response = $this->actingAs($user)->get(route('owner.dashboard'));

        $response->assertStatus(403);
    }

    public function test_owner_can_access_owner_dashboard(): void
    {
        $user = $this->createUserWithRole('gym_owner');

        $response = $this->actingAs($user)->get(route('owner.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Gym Owner Dashboard');
    }

    public function test_owner_cannot_access_admin_dashboard(): void
    {
        $user = $this->createUserWithRole('gym_owner');

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_owner_cannot_access_trainer_dashboard(): void
    {
        $user = $this->createUserWithRole('gym_owner');

        $response = $this->actingAs($user)->get(route('trainer.dashboard'));

        $response->assertStatus(403);
    }

    public function test_trainer_can_access_trainer_dashboard(): void
    {
        $user = $this->createUserWithRole('trainer');

        $response = $this->actingAs($user)->get(route('trainer.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Trainer Dashboard');
    }

    public function test_trainer_cannot_access_owner_dashboard(): void
    {
        $user = $this->createUserWithRole('trainer');

        $response = $this->actingAs($user)->get(route('owner.dashboard'));

        $response->assertStatus(403);
    }

    public function test_member_can_access_member_dashboard(): void
    {
        $user = $this->createUserWithRole('member');

        $response = $this->actingAs($user)->get(route('member.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Member Dashboard');
    }

    public function test_member_cannot_access_trainer_dashboard(): void
    {
        $user = $this->createUserWithRole('member');

        $response = $this->actingAs($user)->get(route('trainer.dashboard'));

        $response->assertStatus(403);
    }

    public function test_member_cannot_access_admin_dashboard(): void
    {
        $user = $this->createUserWithRole('member');

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    // --- Generic Dashboard Redirect ---

    public function test_generic_dashboard_redirects_to_role_dashboard(): void
    {
        $user = $this->createUserWithRole('gym_owner');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('owner.dashboard'));
    }

    // --- Unauthenticated Access ---

    public function test_unauthenticated_user_cannot_access_any_dashboard(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect('/login');
        $this->get(route('owner.dashboard'))->assertRedirect('/login');
        $this->get(route('trainer.dashboard'))->assertRedirect('/login');
        $this->get(route('member.dashboard'))->assertRedirect('/login');
    }

    // --- Password Reset Pages ---

    public function test_forgot_password_page_is_accessible(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    // --- Logout ---

    public function test_user_can_logout(): void
    {
        $user = $this->createUserWithRole('gym_owner');

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    // --- Account Active Middleware ---

    public function test_inactive_user_is_logged_out_when_accessing_dashboard(): void
    {
        $user = $this->createUserWithRole('gym_owner', 'active');

        // Log in first
        $this->actingAs($user);

        // Change status to suspended
        $suspended = AccountStatus::where('code', 'suspended')->first();
        $user->update(['account_status_id' => $suspended->id]);

        $response = $this->get(route('owner.dashboard'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
