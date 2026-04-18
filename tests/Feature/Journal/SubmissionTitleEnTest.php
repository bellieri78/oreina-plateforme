<?php

namespace Tests\Feature\Journal;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionTitleEnTest extends TestCase
{
    use RefreshDatabase;

    public function test_submission_can_store_title_en(): void
    {
        $author = User::factory()->create();

        $submission = Submission::create([
            'author_id' => $author->id,
            'title' => 'Étude phylogénétique des Lycaenidae',
            'title_en' => 'Phylogenetic study of Lycaenidae',
            'abstract' => '',
            'manuscript_file' => 'placeholder.pdf',
            'status' => 'submitted',
        ]);

        $this->assertEquals(
            'Phylogenetic study of Lycaenidae',
            $submission->fresh()->title_en
        );
    }

    public function test_title_en_is_nullable(): void
    {
        $author = User::factory()->create();

        $submission = Submission::create([
            'author_id' => $author->id,
            'title' => 'Titre sans traduction',
            'abstract' => '',
            'manuscript_file' => 'placeholder.pdf',
            'status' => 'submitted',
        ]);

        $this->assertNull($submission->fresh()->title_en);
    }
}
