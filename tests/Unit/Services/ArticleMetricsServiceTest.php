<?php

namespace Tests\Unit\Services;

use App\Models\ArticleEvent;
use App\Models\Submission;
use App\Models\User;
use App\Services\ArticleMetricsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ArticleMetricsServiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeSubmission(): Submission
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        return Submission::create([
            'author_id' => $author->id,
            'title' => 'Test submission',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => 'published',
            'published_at' => now(),
            'submitted_at' => now(),
        ]);
    }

    private function makeRequest(string $ip = '203.0.113.1', ?string $cookieId = null): Request
    {
        $request = Request::create('/articles/1', 'GET');
        $request->server->set('REMOTE_ADDR', $ip);
        $request->headers->set('User-Agent', 'Mozilla/5.0 test');
        if ($cookieId) {
            $request->cookies->set('oreina_visitor', $cookieId);
        }
        return $request;
    }

    public function test_record_view_inserts_event_on_first_visit(): void
    {
        $submission = $this->makeSubmission();
        $service = new ArticleMetricsService();

        $service->recordView($submission, $this->makeRequest('203.0.113.1', 'cookie-abc'));

        $this->assertDatabaseCount('article_events', 1);
        $this->assertDatabaseHas('article_events', [
            'submission_id' => $submission->id,
            'event_type' => ArticleEvent::TYPE_VIEW,
            'cookie_id' => 'cookie-abc',
        ]);
    }

    public function test_record_view_dedups_by_ip_within_24h(): void
    {
        $submission = $this->makeSubmission();
        $service = new ArticleMetricsService();

        $service->recordView($submission, $this->makeRequest('203.0.113.5'));
        $service->recordView($submission, $this->makeRequest('203.0.113.5'));

        $this->assertDatabaseCount('article_events', 1);
    }

    public function test_record_view_dedups_by_cookie_even_with_different_ip(): void
    {
        $submission = $this->makeSubmission();
        $service = new ArticleMetricsService();

        $service->recordView($submission, $this->makeRequest('203.0.113.10', 'cookie-xyz'));
        $service->recordView($submission, $this->makeRequest('203.0.113.99', 'cookie-xyz'));

        $this->assertDatabaseCount('article_events', 1);
    }

    public function test_record_view_records_again_after_24h(): void
    {
        $submission = $this->makeSubmission();
        $service = new ArticleMetricsService();

        $service->recordView($submission, $this->makeRequest('203.0.113.20'));

        \Illuminate\Support\Facades\DB::table('article_events')->update([
            'occurred_at' => now()->subHours(25),
        ]);

        $service->recordView($submission, $this->makeRequest('203.0.113.20'));

        $this->assertDatabaseCount('article_events', 2);
    }

    public function test_ip_is_hashed_never_stored_plaintext(): void
    {
        $submission = $this->makeSubmission();
        $service = new ArticleMetricsService();

        $service->recordView($submission, $this->makeRequest('203.0.113.42'));

        $this->assertDatabaseMissing('article_events', ['hashed_ip' => '203.0.113.42']);
        $event = ArticleEvent::first();
        $this->assertSame(64, strlen($event->hashed_ip));
    }

    public function test_record_pdf_download_inserts_event(): void
    {
        $submission = $this->makeSubmission();
        $service = new ArticleMetricsService();

        $service->recordPdfDownload($submission, $this->makeRequest());

        $this->assertDatabaseHas('article_events', [
            'submission_id' => $submission->id,
            'event_type' => ArticleEvent::TYPE_PDF_DOWNLOAD,
        ]);
    }

    public function test_record_share_stores_network(): void
    {
        $submission = $this->makeSubmission();
        $service = new ArticleMetricsService();

        $service->recordShare($submission, $this->makeRequest(), 'twitter');

        $this->assertDatabaseHas('article_events', [
            'submission_id' => $submission->id,
            'event_type' => ArticleEvent::TYPE_SHARE,
            'network' => 'twitter',
        ]);
    }

    public function test_record_share_rejects_invalid_network(): void
    {
        $submission = $this->makeSubmission();
        $service = new ArticleMetricsService();

        $this->expectException(\InvalidArgumentException::class);
        $service->recordShare($submission, $this->makeRequest(), 'myspace');
    }
}
