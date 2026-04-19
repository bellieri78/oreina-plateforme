<?php

namespace Tests\Feature\Journal;

use App\Models\ArticleEvent;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_download_pdf_records_event_and_redirects(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('pdfs/article.pdf', '%PDF-1.4 fake');

        $author = User::factory()->create(['email_verified_at' => now()]);
        $submission = Submission::create([
            'author_id' => $author->id,
            'title' => 'With PDF',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'pdf_file' => 'pdfs/article.pdf',
            'status' => 'published',
            'published_at' => now(),
            'submitted_at' => now(),
        ]);

        $response = $this->get(route('journal.articles.pdf', $submission));
        $response->assertRedirect();

        $this->assertDatabaseHas('article_events', [
            'submission_id' => $submission->id,
            'event_type' => ArticleEvent::TYPE_PDF_DOWNLOAD,
        ]);
    }

    public function test_download_pdf_404_when_no_pdf(): void
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        $submission = Submission::create([
            'author_id' => $author->id,
            'title' => 'No PDF',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => 'published',
            'published_at' => now(),
            'submitted_at' => now(),
        ]);

        $this->get(route('journal.articles.pdf', $submission))->assertNotFound();
    }
}
