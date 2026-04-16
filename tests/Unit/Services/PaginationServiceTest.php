<?php

namespace Tests\Unit\Services;

use App\Models\JournalIssue;
use App\Models\Submission;
use App\Models\User;
use App\Services\PaginationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginationServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaginationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PaginationService();
    }

    private function makeIssue(): JournalIssue
    {
        return JournalIssue::create([
            'volume_number' => 1,
            'issue_number' => 1,
            'title' => 'Tome 1',
            'slug' => 'tome-1-' . uniqid(),
            'year' => 2026,
            'status' => 'published',
        ]);
    }

    private function makeSubmission(JournalIssue $issue, ?int $startPage = null, ?int $endPage = null): Submission
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        return Submission::create([
            'author_id' => $author->id,
            'title' => 'Article ' . uniqid(),
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => 'published',
            'journal_issue_id' => $issue->id,
            'start_page' => $startPage,
            'end_page' => $endPage,
            'submitted_at' => now(),
            'published_at' => now(),
        ]);
    }

    public function test_first_article_starts_at_page_1(): void
    {
        $issue = $this->makeIssue();
        $submission = $this->makeSubmission($issue);

        $this->service->assignPages($submission, 12);

        $this->assertSame(1, $submission->fresh()->start_page);
        $this->assertSame(12, $submission->fresh()->end_page);
    }

    public function test_second_article_follows_first(): void
    {
        $issue = $this->makeIssue();
        $this->makeSubmission($issue, 1, 12);
        $second = $this->makeSubmission($issue);

        $this->service->assignPages($second, 8);

        $this->assertSame(13, $second->fresh()->start_page);
        $this->assertSame(20, $second->fresh()->end_page);
    }

    public function test_third_article_follows_second(): void
    {
        $issue = $this->makeIssue();
        $this->makeSubmission($issue, 1, 12);
        $this->makeSubmission($issue, 13, 20);
        $third = $this->makeSubmission($issue);

        $this->service->assignPages($third, 5);

        $this->assertSame(21, $third->fresh()->start_page);
        $this->assertSame(25, $third->fresh()->end_page);
    }

    public function test_reassigning_pages_updates_correctly(): void
    {
        $issue = $this->makeIssue();
        $submission = $this->makeSubmission($issue, 1, 12);

        $this->service->assignPages($submission, 15);

        $this->assertSame(1, $submission->fresh()->start_page);
        $this->assertSame(15, $submission->fresh()->end_page);
    }

    public function test_submission_without_issue_throws(): void
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        $submission = Submission::create([
            'author_id' => $author->id,
            'title' => 'No issue',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => 'published',
            'submitted_at' => now(),
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->service->assignPages($submission, 10);
    }

    public function test_get_next_start_page(): void
    {
        $issue = $this->makeIssue();
        $this->makeSubmission($issue, 1, 12);
        $this->makeSubmission($issue, 13, 20);

        $this->assertSame(21, $this->service->getNextStartPage($issue));
    }

    public function test_get_next_start_page_empty_issue(): void
    {
        $issue = $this->makeIssue();

        $this->assertSame(1, $this->service->getNextStartPage($issue));
    }
}
