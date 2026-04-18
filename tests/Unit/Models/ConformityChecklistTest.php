<?php

namespace Tests\Unit\Models;

use App\Enums\ConformityChecklistItem;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConformityChecklistTest extends TestCase
{
    use RefreshDatabase;

    private function makeSubmission(?array $checklist = null): Submission
    {
        $author = User::factory()->create();
        return Submission::create([
            'author_id' => $author->id,
            'title' => 'Test',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
            'status' => 'submitted',
            'conformity_checklist' => $checklist,
        ]);
    }

    public function test_conformity_checked_returns_false_when_checklist_is_null(): void
    {
        $sub = $this->makeSubmission(null);
        $this->assertFalse($sub->conformityChecked(ConformityChecklistItem::BiblioFormat));
    }

    public function test_conformity_checked_returns_false_when_item_not_in_checklist(): void
    {
        $sub = $this->makeSubmission(['biblio_format']);
        $this->assertFalse($sub->conformityChecked(ConformityChecklistItem::FiguresNumbered));
    }

    public function test_conformity_checked_returns_true_when_item_in_checklist(): void
    {
        $sub = $this->makeSubmission(['biblio_format', 'figures_numbered']);
        $this->assertTrue($sub->conformityChecked(ConformityChecklistItem::FiguresNumbered));
    }

    public function test_conformity_progress_returns_zero_over_nine_by_default(): void
    {
        $sub = $this->makeSubmission(null);
        $progress = $sub->conformityProgress();
        $this->assertEquals(0, $progress['checked']);
        $this->assertEquals(9, $progress['total']);
    }

    public function test_conformity_progress_reflects_checklist_count(): void
    {
        $sub = $this->makeSubmission(['biblio_format', 'author_affiliations', 'figures_numbered']);
        $progress = $sub->conformityProgress();
        $this->assertEquals(3, $progress['checked']);
        $this->assertEquals(9, $progress['total']);
    }

    public function test_checklist_persists_as_array_cast(): void
    {
        $sub = $this->makeSubmission(['biblio_format']);
        $fresh = Submission::find($sub->id);
        $this->assertIsArray($fresh->conformity_checklist);
        $this->assertEquals(['biblio_format'], $fresh->conformity_checklist);
    }
}
