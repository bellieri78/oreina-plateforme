<?php

namespace Tests\Feature;

use App\Models\Submission;
use App\Models\User;
use App\Services\SubmissionFileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubmissionFileTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_stores_file_with_uuid_name(): void
    {
        Storage::fake('submissions');

        $author = User::factory()->create(['email_verified_at' => now()]);
        $submission = $this->makeSubmission($author);

        $file = UploadedFile::fake()->create('original.docx', 10);
        $service = app(SubmissionFileService::class);

        $stored = $service->store($submission, $file, SubmissionFileService::TYPE_MANUSCRIPT);

        $this->assertStringStartsWith("{$submission->id}/manuscript/", $stored['path']);
        $this->assertSame('original.docx', $stored['original_filename']);
        Storage::disk('submissions')->assertExists($stored['path']);
    }

    public function test_author_can_download_their_file(): void
    {
        Storage::fake('submissions');

        $author = User::factory()->create(['email_verified_at' => now()]);
        $submission = $this->makeSubmission($author);

        $file = UploadedFile::fake()->create('original.docx', 10);
        $stored = app(SubmissionFileService::class)->store($submission, $file, 'manuscript');
        $relativeAfterId = substr($stored['path'], strlen("{$submission->id}/"));

        $this->actingAs($author)
            ->get(route('journal.submissions.file.download', [
                'submission' => $submission->id,
                'path' => $relativeAfterId,
            ]))
            ->assertOk();
    }

    public function test_other_user_gets_403(): void
    {
        Storage::fake('submissions');

        $author = User::factory()->create(['email_verified_at' => now()]);
        $stranger = User::factory()->create(['email_verified_at' => now()]);
        $submission = $this->makeSubmission($author);

        $file = UploadedFile::fake()->create('original.docx', 10);
        $stored = app(SubmissionFileService::class)->store($submission, $file, 'manuscript');
        $relativeAfterId = substr($stored['path'], strlen("{$submission->id}/"));

        $this->actingAs($stranger)
            ->get(route('journal.submissions.file.download', [
                'submission' => $submission->id,
                'path' => $relativeAfterId,
            ]))
            ->assertForbidden();
    }

    public function test_path_traversal_is_blocked(): void
    {
        Storage::fake('submissions');

        $author = User::factory()->create(['email_verified_at' => now()]);
        $submission = $this->makeSubmission($author);

        $this->actingAs($author)
            ->get(route('journal.submissions.file.download', [
                'submission' => $submission->id,
                'path' => '../../etc/passwd',
            ]))
            ->assertForbidden();
    }

    private function makeSubmission(User $author): Submission
    {
        return Submission::create([
            'author_id' => $author->id,
            'title' => 'Test',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.pdf',
            'status' => Submission::STATUS_SUBMITTED,
        ]);
    }
}
