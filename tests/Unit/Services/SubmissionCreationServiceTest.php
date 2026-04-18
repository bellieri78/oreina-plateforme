<?php

namespace Tests\Unit\Services;

use App\Enums\SubmissionStatus;
use App\Mail\AccountInvitation;
use App\Models\Submission;
use App\Models\User;
use App\Services\SubmissionCreationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SubmissionCreationServiceTest extends TestCase
{
    use RefreshDatabase;

    private function data(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Un papier test',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
        ], $overrides);
    }

    public function test_create_for_existing_author_persists_submission(): void
    {
        Mail::fake();
        $author = User::factory()->create();
        $editor = User::factory()->create();

        $service = app(SubmissionCreationService::class);
        $sub = $service->createForExistingAuthor($author, $this->data(), $editor);

        $this->assertEquals($author->id, $sub->author_id);
        $this->assertEquals($editor->id, $sub->submitted_by_user_id);
        $this->assertEquals(SubmissionStatus::Submitted, $sub->status);
        $this->assertNotNull($sub->submitted_at);
    }

    public function test_create_for_existing_author_sets_submitted_by_null_when_author_creates_self(): void
    {
        Mail::fake();
        $author = User::factory()->create();

        $service = app(SubmissionCreationService::class);
        $sub = $service->createForExistingAuthor($author, $this->data(), $author);

        $this->assertNull($sub->submitted_by_user_id);
    }

    public function test_create_for_new_author_creates_ghost_user_and_sends_invitation(): void
    {
        Mail::fake();
        $editor = User::factory()->create();

        $service = app(SubmissionCreationService::class);
        $sub = $service->createForNewAuthor(
            'Jean Nouveau',
            'jean@example.com',
            $this->data(),
            $editor,
        );

        $author = User::where('email', 'jean@example.com')->first();
        $this->assertNotNull($author);
        $this->assertTrue($author->isGhost());
        $this->assertEquals($editor->id, $author->invited_by_user_id);
        $this->assertEquals($author->id, $sub->author_id);
        $this->assertEquals($editor->id, $sub->submitted_by_user_id);

        Mail::assertQueued(AccountInvitation::class, function ($mail) use ($author) {
            return $mail->hasTo($author->email);
        });
    }

    public function test_create_for_new_author_is_atomic_on_mail_failure(): void
    {
        // On simule un échec mail en bindant un mailer qui throw
        $this->app->bind(\Illuminate\Contracts\Mail\Mailer::class, function () {
            return new class implements \Illuminate\Contracts\Mail\Mailer {
                public function to($users, $name = null) { throw new \RuntimeException('mail down'); }
                public function bcc($users, $name = null) { return $this; }
                public function cc($users, $name = null) { return $this; }
                public function raw($text, $callback) {}
                public function send($view, array $data = [], $callback = null) {}
                public function sendNow($mailable, array $data = [], $callback = null) {}
                public function queue($view, $queue = null) {}
                public function later($delay, $view, array $data = [], $callback = null) {}
                public function mailer($name = null) { return $this; }
            };
        });

        $editor = User::factory()->create();
        $service = app(SubmissionCreationService::class);

        try {
            $service->createForNewAuthor('Jean', 'jean@example.com', $this->data(), $editor);
            $this->fail('Exception attendue');
        } catch (\RuntimeException $e) {
            // OK
        }

        $this->assertDatabaseMissing('users', ['email' => 'jean@example.com']);
        $this->assertDatabaseMissing('submissions', ['title' => 'Un papier test']);
    }
}
