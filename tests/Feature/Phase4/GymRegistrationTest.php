<?php

namespace Tests\Feature\Phase4;

use App\Models\Gym;
use App\Models\GymOwner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GymRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'gym_name'     => 'PowerFit Gym',
            'gym_phone'    => '+15551234567',
            'gym_email'    => 'contact@powerfit.test',
            'gym_address'  => '123 Main Street',
            'gym_city'     => 'New York',
            'gym_state'    => 'NY',
            'gym_zip_code' => '10001',
            'owner_name'   => 'John Smith',
            'owner_email'  => 'john@powerfit.test',
            'owner_phone'  => '+15559876543',
            'password'     => 'SecurePass1',
            'password_confirmation' => 'SecurePass1',
            'terms'        => '1',
        ], $overrides);
    }

    public function test_registration_page_is_accessible_to_guests(): void
    {
        $this->get(route('gym.register'))
            ->assertStatus(200)
            ->assertSee('Register Your Gym');
    }

    public function test_registration_page_is_not_accessible_to_authenticated_users(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('gym.register'))
            ->assertRedirect();
    }

    public function test_valid_submission_creates_user_gym_owner_and_role(): void
    {
        $response = $this->post(route('gym.register'), $this->validPayload());

        $response->assertRedirect(route('owner.dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'john@powerfit.test',
            'phone' => '+15559876543',
            'name'  => 'John Smith',
        ]);

        $this->assertDatabaseHas('gyms', [
            'name'  => 'PowerFit Gym',
            'city'  => 'New York',
            'state' => 'NY',
        ]);

        $this->assertDatabaseHas('gym_owners', [
            'user_id' => User::where('email', 'john@powerfit.test')->first()->id,
        ]);

        $user = User::where('email', 'john@powerfit.test')->first();
        $this->assertTrue($user->hasRole('gym_owner'));
    }

    public function test_gym_is_created_with_trial_status(): void
    {
        $this->post(route('gym.register'), $this->validPayload());

        $gym = Gym::where('name', 'PowerFit Gym')->first();
        $this->assertNotNull($gym);
        $this->assertEquals('trial', $gym->status->code);
    }

    public function test_user_is_auto_logged_in_after_registration(): void
    {
        $this->post(route('gym.register'), $this->validPayload());

        $this->assertAuthenticatedAs(User::where('email', 'john@powerfit.test')->first());
    }

    public function test_validation_requires_essential_fields(): void
    {
        $response = $this->post(route('gym.register'), []);

        $response->assertSessionHasErrors([
            'gym_name',
            'gym_address',
            'gym_city',
            'gym_state',
            'owner_name',
            'owner_email',
            'owner_phone',
            'password',
            'terms',
        ]);
    }

    public function test_password_must_be_confirmed(): void
    {
        $response = $this->post(route('gym.register'), $this->validPayload([
            'password_confirmation' => 'different-password',
        ]));

        $response->assertSessionHasErrors('password');
    }

    public function test_password_must_meet_complexity_requirements(): void
    {
        $response = $this->post(route('gym.register'), $this->validPayload([
            'password' => 'simple',
            'password_confirmation' => 'simple',
        ]));

        $response->assertSessionHasErrors('password');
    }

    public function test_owner_email_must_be_valid(): void
    {
        $response = $this->post(route('gym.register'), $this->validPayload([
            'owner_email' => 'not-an-email',
        ]));

        $response->assertSessionHasErrors('owner_email');
    }

    public function test_two_different_gyms_can_use_the_same_owner_email(): void
    {
        // First registration
        $this->post(route('gym.register'), $this->validPayload());

        $this->post('/logout');

        // Second registration with SAME email, different gym
        $response = $this->post(route('gym.register'), $this->validPayload([
            'gym_name' => 'Second Gym',
        ]));

        $response->assertRedirect(route('owner.dashboard'));
        $this->assertEquals(2, User::where('email', 'john@powerfit.test')->count());
        $this->assertEquals(2, Gym::count());
    }

    public function test_logo_upload_is_stored(): void
    {
        Storage::fake('public');

        $logo = UploadedFile::fake()->image('logo.png', 400, 400);

        $this->post(route('gym.register'), $this->validPayload([
            'gym_logo' => $logo,
        ]));

        $gym = Gym::where('name', 'PowerFit Gym')->first();
        $this->assertNotNull($gym->logo);
        Storage::disk('public')->assertExists($gym->logo);
    }

    public function test_invalid_logo_file_is_rejected(): void
    {
        $invalidFile = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->post(route('gym.register'), $this->validPayload([
            'gym_logo' => $invalidFile,
        ]));

        $response->assertSessionHasErrors('gym_logo');
    }

    public function test_registration_is_transactional_rolls_back_on_error(): void
    {
        // Simulate failure by deleting required lookup data mid-flight
        \App\Models\UserRole::where('code', 'gym_owner')->delete();

        try {
            $this->post(route('gym.register'), $this->validPayload());
        } catch (\Throwable $e) {
            // swallow
        }

        // No records should exist because transaction rolled back
        $this->assertDatabaseMissing('users', ['email' => 'john@powerfit.test']);
        $this->assertDatabaseMissing('gyms', ['name' => 'PowerFit Gym']);
    }

    public function test_gym_code_is_unique_and_prefixed(): void
    {
        $this->post(route('gym.register'), $this->validPayload());
        $this->post('/logout');
        $this->post(route('gym.register'), $this->validPayload([
            'gym_name' => 'Other Gym',
        ]));

        $codes = Gym::pluck('code')->toArray();
        $this->assertCount(2, array_unique($codes));
        foreach ($codes as $code) {
            $this->assertStringStartsWith('GYM-', $code);
        }
    }

    public function test_owner_code_is_prefixed_with_gym_code(): void
    {
        $this->post(route('gym.register'), $this->validPayload());

        $gym = Gym::first();
        $owner = GymOwner::first();

        $this->assertStringStartsWith($gym->code . '-OWN', $owner->owner_code);
    }
}
