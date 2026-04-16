<?php

namespace Tests\Feature\Journal;

use App\Enums\SubmissionStatus;
use App\Mail\NewSubmissionAlert;
use App\Mail\SubmissionDecision;
use App\Mail\SubmissionReceived;
use App\Models\EditorialCapability;
use App\Models\Submission;
use App\Models\User;
use App\Services\SubmissionStateMachine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class SubmissionNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_submission_sends_confirmation_to_author_and_alert_to_editors(): void
    {
        Mail::fake();
        Storage::fake('submissions');

        $author = User::factory()->create(['email_verified_at' => now()]);
        $editor = User::factory()->create(['email_verified_at' => now()]);
        $editor->grantCapability(EditorialCapability::EDITOR);

        $docx = $this->makeDocx('article.docx');

        $this->actingAs($author)
            ->post(route('journal.submissions.store'), [
                'title' => 'Mon article test',
                'abstract' => str_repeat('Ceci est un résumé. ', 10),
                'keywords' => 'test, article',
                'manuscript_file' => $docx,
                'accept_terms' => '1',
            ])
            ->assertRedirect();

        Mail::assertQueued(SubmissionReceived::class, fn($m) => $m->hasTo($author->email));
        Mail::assertQueued(NewSubmissionAlert::class, fn($m) => $m->hasTo($editor->email));
    }

    public function test_accepted_transition_sends_decision_to_author(): void
    {
        Mail::fake();

        $author = User::factory()->create(['email_verified_at' => now()]);
        $actor = User::factory()->create(['email_verified_at' => now()]);

        $submission = Submission::create([
            'author_id' => $author->id,
            'title' => 'T',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => SubmissionStatus::UnderPeerReview->value,
            'submitted_at' => now(),
        ]);

        app(SubmissionStateMachine::class)->transition(
            $submission,
            SubmissionStatus::Accepted,
            $actor,
        );

        Mail::assertQueued(SubmissionDecision::class, fn($m) => $m->hasTo($author->email));
    }

    private function makeDocx(string $name): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'docx_');
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString(
            '[Content_Types].xml',
            '<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document"/></Types>'
        );
        $zip->close();
        return new UploadedFile($path, $name, 'application/zip', null, true);
    }
}
