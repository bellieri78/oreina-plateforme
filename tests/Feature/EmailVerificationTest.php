<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_sends_verification_notification(): void
    {
        Notification::fake();

        $this->post(route('hub.register.submit'), [
            'name' => 'Testeur',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect();

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user, 'User should be created after registration');
        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_unverified_user_is_redirected_from_submission_routes(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->actingAs($user)
            ->get(route('journal.submissions.create'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_verified_user_accesses_submission_routes(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)
            ->get(route('journal.submissions.create'))
            ->assertOk();
    }
}
