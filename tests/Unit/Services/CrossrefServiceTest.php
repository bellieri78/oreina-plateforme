<?php

namespace Tests\Unit\Services;

use App\Models\Submission;
use App\Models\User;
use App\Services\CrossrefService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CrossrefServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_doi_suffix_follows_chersotis_format(): void
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        $submission = Submission::create([
            'author_id' => $author->id,
            'title' => 'Test',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => 'published',
            'published_at' => now(),
            'submitted_at' => now(),
        ]);

        $service = new CrossrefService();
        $suffix = $service->generateDoiSuffix($submission);

        $this->assertMatchesRegularExpression('/^chersotis\.\d{4}\.\d{4}$/', $suffix);
    }

    public function test_dry_run_stores_doi_without_http_call(): void
    {
        Http::fake();
        config(['services.crossref.dry_run' => true]);

        $author = User::factory()->create(['email_verified_at' => now()]);
        $submission = Submission::create([
            'author_id' => $author->id,
            'title' => 'Test DOI',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => 'published',
            'published_at' => now(),
            'submitted_at' => now(),
        ]);

        $service = new CrossrefService();
        $result = $service->registerDoi($submission);

        $this->assertTrue($result['success']);
        $this->assertTrue($result['dry_run'] ?? false);
        $this->assertNotEmpty($submission->fresh()->doi);
        Http::assertNothingSent();
    }
}
