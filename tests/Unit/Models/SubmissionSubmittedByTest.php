<?php

namespace Tests\Unit\Models;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionSubmittedByTest extends TestCase
{
    use RefreshDatabase;

    public function test_submitted_by_relation_returns_the_creator(): void
    {
        $author = User::factory()->create();
        $editor = User::factory()->create();

        $submission = Submission::create([
            'author_id' => $author->id,
            'submitted_by_user_id' => $editor->id,
            'title' => 'Test',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
            'status' => SubmissionStatus::Submitted,
        ]);

        $this->assertEquals($editor->id, $submission->submittedBy->id);
    }

    public function test_was_submitted_on_behalf_true_when_different_from_author(): void
    {
        $author = User::factory()->create();
        $editor = User::factory()->create();

        $submission = Submission::create([
            'author_id' => $author->id,
            'submitted_by_user_id' => $editor->id,
            'title' => 'Test',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
            'status' => SubmissionStatus::Submitted,
        ]);

        $this->assertTrue($submission->wasSubmittedOnBehalf());
    }

    public function test_was_submitted_on_behalf_false_when_null(): void
    {
        $author = User::factory()->create();

        $submission = Submission::create([
            'author_id' => $author->id,
            'submitted_by_user_id' => null,
            'title' => 'Test',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
            'status' => SubmissionStatus::Submitted,
        ]);

        $this->assertFalse($submission->wasSubmittedOnBehalf());
    }

    public function test_was_submitted_on_behalf_false_when_same_as_author(): void
    {
        $author = User::factory()->create();

        $submission = Submission::create([
            'author_id' => $author->id,
            'submitted_by_user_id' => $author->id,
            'title' => 'Test',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
            'status' => SubmissionStatus::Submitted,
        ]);

        $this->assertFalse($submission->wasSubmittedOnBehalf());
    }
}
