<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use App\Notifications\ApplicationStatusUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::create(['name' => 'candidate']);
        Role::create(['name' => 'employer']);
        Role::create(['name' => 'admin']);
    }

    public function test_candidate_can_apply_for_job(): void
    {
        $employer = $this->userWithRole('employer');
        $job = Job::factory()->create(['user_id' => $employer->id]);
        $candidate = $this->userWithRole('candidate');

        Sanctum::actingAs($candidate, [], 'sanctum');

        $response = $this->postJson('/api/applications', [
            'job_id' => $job->id,
            'email' => 'candidate@example.com',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('applications', [
            'job_id' => $job->id,
            'candidate_id' => $candidate->id,
            'email' => 'candidate@example.com',
            'status' => 'pending',
        ]);
    }

    public function test_candidate_cannot_apply_twice_for_same_job(): void
    {
        $employer = $this->userWithRole('employer');
        $job = Job::factory()->create(['user_id' => $employer->id]);
        $candidate = $this->userWithRole('candidate');

        Sanctum::actingAs($candidate, [], 'sanctum');

        $this->postJson('/api/applications', [
            'job_id' => $job->id,
            'email' => 'candidate@example.com',
        ])->assertCreated();

        $response = $this->postJson('/api/applications', [
            'job_id' => $job->id,
            'email' => 'candidate@example.com',
        ]);

        $response->assertUnprocessable();
    }

    public function test_employer_cannot_apply_for_job(): void
    {
        $employer = $this->userWithRole('employer');
        $job = Job::factory()->create(['user_id' => $employer->id]);

        Sanctum::actingAs($employer, [], 'sanctum');

        $response = $this->postJson('/api/applications', [
            'job_id' => $job->id,
            'email' => 'employer@example.com',
        ]);

        $response->assertForbidden();
    }

    public function test_apply_with_resume_stores_file(): void
    {
        Storage::fake('local');

        $employer = $this->userWithRole('employer');
        $job = Job::factory()->create(['user_id' => $employer->id]);
        $candidate = $this->userWithRole('candidate');
        $resume = UploadedFile::fake()->create('resume.pdf', 256, 'application/pdf');

        Sanctum::actingAs($candidate, [], 'sanctum');

        $response = $this->postJson('/api/applications', [
            'job_id' => $job->id,
            'email' => 'candidate@example.com',
            'resume' => $resume,
        ]);

        $response->assertCreated();

        $application = Application::firstOrFail();

        $this->assertNotNull($application->resume_path);
        Storage::disk('local')->assertExists($application->resume_path);
        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'resume_path' => $application->resume_path,
        ]);
    }

    public function test_candidate_can_cancel_own_pending_application(): void
    {
        $candidate = $this->userWithRole('candidate');
        $application = $this->makeApplication(candidate: $candidate, status: 'pending');

        Sanctum::actingAs($candidate, [], 'sanctum');

        $response = $this->deleteJson("/api/applications/{$application->id}");

        $response->assertOk();

        $this->assertDatabaseMissing('applications', [
            'id' => $application->id,
        ]);
    }

    public function test_candidate_cannot_cancel_others_application(): void
    {
        $owner = $this->userWithRole('candidate');
        $otherCandidate = $this->userWithRole('candidate');
        $application = $this->makeApplication(candidate: $owner, status: 'pending');

        Sanctum::actingAs($otherCandidate, [], 'sanctum');

        $response = $this->deleteJson("/api/applications/{$application->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
        ]);
    }

    public function test_candidate_cannot_cancel_accepted_application(): void
    {
        $candidate = $this->userWithRole('candidate');
        $application = $this->makeApplication(candidate: $candidate, status: 'accepted');

        Sanctum::actingAs($candidate, [], 'sanctum');

        $response = $this->deleteJson("/api/applications/{$application->id}");

        $response->assertUnprocessable();

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'accepted',
        ]);
    }

    public function test_employer_can_accept_application(): void
    {
        Notification::fake();

        $employer = $this->userWithRole('employer');
        $application = $this->makeApplication(employer: $employer);

        Sanctum::actingAs($employer, [], 'sanctum');

        $response = $this->putJson("/api/applications/{$application->id}", [
            'status' => 'accepted',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'accepted',
        ]);
    }

    public function test_employer_can_reject_application(): void
    {
        Notification::fake();

        $employer = $this->userWithRole('employer');
        $application = $this->makeApplication(employer: $employer);

        Sanctum::actingAs($employer, [], 'sanctum');

        $response = $this->putJson("/api/applications/{$application->id}", [
            'status' => 'rejected',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'rejected',
        ]);
    }

    public function test_employer_cannot_update_status_of_other_employers_application(): void
    {
        $ownerEmployer = $this->userWithRole('employer');
        $otherEmployer = $this->userWithRole('employer');
        $application = $this->makeApplication(employer: $ownerEmployer);

        Sanctum::actingAs($otherEmployer, [], 'sanctum');

        $response = $this->putJson("/api/applications/{$application->id}", [
            'status' => 'accepted',
        ]);

        $response->assertForbidden();
    }

    public function test_notification_sent_when_status_updated(): void
    {
        Notification::fake();

        $employer = $this->userWithRole('employer');
        $candidate = $this->userWithRole('candidate');
        $application = $this->makeApplication(employer: $employer, candidate: $candidate);

        Sanctum::actingAs($employer, [], 'sanctum');

        $this->putJson("/api/applications/{$application->id}", [
            'status' => 'accepted',
        ])->assertOk();

        Notification::assertSentTo($candidate, ApplicationStatusUpdated::class);
    }

    public function test_employer_can_see_own_job_applications(): void
    {
        $employer = $this->userWithRole('employer');
        $otherEmployer = $this->userWithRole('employer');

        $firstJob = Job::factory()->create(['user_id' => $employer->id]);
        $secondJob = Job::factory()->create(['user_id' => $employer->id]);
        $otherJob = Job::factory()->create(['user_id' => $otherEmployer->id]);

        $firstApplication = $this->makeApplication(job: $firstJob);
        $secondApplication = $this->makeApplication(job: $secondJob);
        $otherApplication = $this->makeApplication(job: $otherJob);

        Sanctum::actingAs($employer, [], 'sanctum');

        $response = $this->getJson('/api/applications');

        $response
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment(['id' => $firstApplication->id])
            ->assertJsonFragment(['id' => $secondApplication->id])
            ->assertJsonMissing(['id' => $otherApplication->id]);
    }

    public function test_candidate_cannot_access_employer_index(): void
    {
        $candidate = $this->userWithRole('candidate');

        Sanctum::actingAs($candidate, [], 'sanctum');

        $response = $this->getJson('/api/applications');

        $response->assertForbidden();
    }

    public function test_linkedin_redirect_returns_redirect_response(): void
    {
        $driver = Mockery::mock();
        $driver->shouldReceive('stateless')->once()->andReturnSelf();
        $driver->shouldReceive('redirect')->once()->andReturn(redirect()->away('https://www.linkedin.com/oauth/v2/authorization'));

        Socialite::shouldReceive('driver')
            ->once()
            ->with('linkedin-openid')
            ->andReturn($driver);

        $response = $this->get('/api/auth/linkedin/redirect');

        $response
            ->assertRedirect()
            ->assertStatus(302)
            ->assertHeader('Location', 'https://www.linkedin.com/oauth/v2/authorization');
    }

    public function test_linkedin_callback_creates_new_user(): void
    {
        $this->mockSocialiteUser(
            id: 'linkedin-123',
            name: 'LinkedIn Candidate',
            email: 'linkedin@example.com',
            avatar: 'https://example.com/avatar.jpg',
        );

        $response = $this->getJson('/api/auth/linkedin/callback');

        $response
            ->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'linkedin_id', 'linkedin_avatar']]);

        $this->assertDatabaseHas('users', [
            'email' => 'linkedin@example.com',
            'name' => 'LinkedIn Candidate',
            'linkedin_id' => 'linkedin-123',
            'linkedin_avatar' => 'https://example.com/avatar.jpg',
        ]);
    }

    public function test_linkedin_callback_updates_existing_user(): void
    {
        $existingUser = $this->userWithRole('candidate', [
            'email' => 'linkedin@example.com',
            'linkedin_id' => null,
            'linkedin_avatar' => null,
        ]);

        $this->mockSocialiteUser(
            id: 'linkedin-456',
            name: 'Updated LinkedIn Name',
            email: 'linkedin@example.com',
            avatar: 'https://example.com/new-avatar.jpg',
        );

        $response = $this->getJson('/api/auth/linkedin/callback');

        $response->assertOk();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'id' => $existingUser->id,
            'email' => 'linkedin@example.com',
            'linkedin_id' => 'linkedin-456',
            'linkedin_avatar' => 'https://example.com/new-avatar.jpg',
        ]);
    }

    private function userWithRole(string $role, array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        return $user;
    }

    private function makeApplication(
        ?User $employer = null,
        ?User $candidate = null,
        ?Job $job = null,
        string $status = 'pending',
    ): Application {
        $employer ??= $this->userWithRole('employer');
        $candidate ??= $this->userWithRole('candidate');
        $job ??= Job::factory()->create(['user_id' => $employer->id]);

        return Application::factory()->create([
            'job_id' => $job->id,
            'candidate_id' => $candidate->id,
            'email' => $candidate->email,
            'status' => $status,
        ]);
    }

    private function mockSocialiteUser(string $id, string $name, string $email, string $avatar): void
    {
        $socialiteUser = Mockery::mock();
        $socialiteUser->shouldReceive('getId')->andReturn($id);
        $socialiteUser->shouldReceive('getName')->andReturn($name);
        $socialiteUser->shouldReceive('getEmail')->andReturn($email);
        $socialiteUser->shouldReceive('getAvatar')->andReturn($avatar);

        $driver = Mockery::mock();
        $driver->shouldReceive('stateless')->once()->andReturnSelf();
        $driver->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('linkedin-openid')
            ->andReturn($driver);
    }
}
