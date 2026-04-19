<?php

namespace Tests\Unit\Services;

use App\Models\Submission;
use App\Models\User;
use App\Services\CrossrefCitationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CrossrefCitationServiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeSubmission(array $attrs = []): Submission
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        return Submission::create(array_merge([
            'author_id' => $author->id,
            'title' => 'Test',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => 'published',
            'published_at' => now(),
            'submitted_at' => now(),
            'doi' => '10.24349/chersotis.2026.0001',
        ], $attrs));
    }

    public function test_should_sync_returns_false_without_doi(): void
    {
        $submission = $this->makeSubmission(['doi' => null]);
        $this->assertFalse((new CrossrefCitationService())->shouldSync($submission));
    }

    public function test_should_sync_returns_true_when_never_synced(): void
    {
        $submission = $this->makeSubmission();
        $this->assertTrue((new CrossrefCitationService())->shouldSync($submission));
    }

    public function test_should_sync_returns_false_within_7_days(): void
    {
        $submission = $this->makeSubmission(['citation_synced_at' => now()->subDays(3)]);
        $this->assertFalse((new CrossrefCitationService())->shouldSync($submission));
    }

    public function test_should_sync_returns_true_after_7_days(): void
    {
        $submission = $this->makeSubmission(['citation_synced_at' => now()->subDays(8)]);
        $this->assertTrue((new CrossrefCitationService())->shouldSync($submission));
    }

    public function test_sync_updates_citation_count_and_timestamp(): void
    {
        Http::fake([
            'api.crossref.org/works/*' => Http::response([
                'message' => ['is-referenced-by-count' => 12],
            ], 200),
        ]);

        $submission = $this->makeSubmission();
        (new CrossrefCitationService())->sync($submission);

        $submission->refresh();
        $this->assertSame(12, $submission->citation_count);
        $this->assertNotNull($submission->citation_synced_at);
    }

    public function test_sync_is_resilient_to_api_failure(): void
    {
        Http::fake([
            'api.crossref.org/works/*' => Http::response(null, 500),
        ]);

        $submission = $this->makeSubmission();
        (new CrossrefCitationService())->sync($submission);

        $submission->refresh();
        $this->assertSame(0, $submission->citation_count);
        $this->assertNull($submission->citation_synced_at);
    }
}
