<?php

namespace Tests\Feature\Console;

use App\Mail\ReviewReminder;
use App\Models\Review;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReviewReminderCommandTest extends TestCase
{
    use RefreshDatabase;

    private function makeSubmission(): Submission
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        return Submission::create([
            'author_id' => $author->id,
            'title' => 'T',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => 'under_peer_review',
            'submitted_at' => now(),
        ]);
    }

    public function test_reminds_invitation_after_7_days(): void
    {
        Mail::fake();
        $reviewer = User::factory()->create(['email_verified_at' => now()]);
        Review::create([
            'submission_id' => $this->makeSubmission()->id,
            'reviewer_id' => $reviewer->id,
            'status' => Review::STATUS_INVITED,
            'invited_at' => now()->subDays(8),
        ]);

        $this->artisan('reviews:send-reminders')->assertSuccessful();

        Mail::assertQueued(ReviewReminder::class, 1);
    }

    public function test_does_not_remind_recent_invitation(): void
    {
        Mail::fake();
        $reviewer = User::factory()->create(['email_verified_at' => now()]);
        Review::create([
            'submission_id' => $this->makeSubmission()->id,
            'reviewer_id' => $reviewer->id,
            'status' => Review::STATUS_INVITED,
            'invited_at' => now()->subDays(3),
        ]);

        $this->artisan('reviews:send-reminders')->assertSuccessful();

        Mail::assertNotQueued(ReviewReminder::class);
    }

    public function test_does_not_re_remind_within_5_days(): void
    {
        Mail::fake();
        $reviewer = User::factory()->create(['email_verified_at' => now()]);
        Review::create([
            'submission_id' => $this->makeSubmission()->id,
            'reviewer_id' => $reviewer->id,
            'status' => Review::STATUS_INVITED,
            'invited_at' => now()->subDays(10),
            'last_reminder_at' => now()->subDays(2),
        ]);

        $this->artisan('reviews:send-reminders')->assertSuccessful();

        Mail::assertNotQueued(ReviewReminder::class);
    }

    public function test_reminds_overdue_review(): void
    {
        Mail::fake();
        $reviewer = User::factory()->create(['email_verified_at' => now()]);
        Review::create([
            'submission_id' => $this->makeSubmission()->id,
            'reviewer_id' => $reviewer->id,
            'status' => Review::STATUS_ACCEPTED,
            'invited_at' => now()->subDays(25),
            'responded_at' => now()->subDays(24),
            'due_date' => now()->subDays(3),
        ]);

        $this->artisan('reviews:send-reminders')->assertSuccessful();

        Mail::assertQueued(ReviewReminder::class, 1);
    }
}
