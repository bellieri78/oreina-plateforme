<?php

namespace Tests\Feature\Journal;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class AuthorRevisionTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_author_submits_revision_from_revision_requested(): void
    {
        Storage::fake('submissions');

        $author = User::factory()->create(['email_verified_at' => now()]);
        $submission = Submission::create([
            'author_id' => $author->id,
            'title' => 'Test',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => SubmissionStatus::RevisionRequested->value,
        ]);

        $file = $this->makeDocx('revision_v2.docx');

        $this->actingAs($author)
            ->put(route('journal.submissions.update', $submission), [
                'manuscript_file' => $file,
                'revision_notes' => 'Ajouté les références demandées',
            ])
            ->assertRedirect();

        $fresh = $submission->fresh();
        $this->assertSame('under_initial_review', $fresh->status->value);
        $this->assertDatabaseHas('submission_transitions', [
            'submission_id' => $submission->id,
            'from_status' => 'revision_requested',
            'to_status' => 'under_initial_review',
        ]);
    }

    public function test_author_submits_revision_from_revision_after_review(): void
    {
        Storage::fake('submissions');

        $author = User::factory()->create(['email_verified_at' => now()]);
        $submission = Submission::create([
            'author_id' => $author->id,
            'title' => 'Test',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => SubmissionStatus::RevisionAfterReview->value,
        ]);

        $file = $this->makeDocx('revision_v2.docx');

        $this->actingAs($author)
            ->put(route('journal.submissions.update', $submission), [
                'manuscript_file' => $file,
                'revision_notes' => 'Refonte section 3',
            ])
            ->assertRedirect();

        $this->assertSame('under_peer_review', $submission->fresh()->status->value);
    }

    public function test_cannot_submit_revision_if_status_not_revision(): void
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        $submission = Submission::create([
            'author_id' => $author->id,
            'title' => 'Test',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => SubmissionStatus::UnderPeerReview->value,
        ]);

        $file = $this->makeDocx('revision_v2.docx');

        $this->actingAs($author)
            ->put(route('journal.submissions.update', $submission), [
                'manuscript_file' => $file,
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame('under_peer_review', $submission->fresh()->status->value);
    }

    public function test_other_user_cannot_submit_revision(): void
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        $stranger = User::factory()->create(['email_verified_at' => now()]);

        $submission = Submission::create([
            'author_id' => $author->id,
            'title' => 'Test',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => SubmissionStatus::RevisionRequested->value,
        ]);

        $file = $this->makeDocx('revision_v2.docx');

        $this->actingAs($stranger)
            ->put(route('journal.submissions.update', $submission), [
                'manuscript_file' => $file,
            ])
            ->assertForbidden();
    }
}
