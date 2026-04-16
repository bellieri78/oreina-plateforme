<?php

namespace Tests\Unit\Policies;

use App\Enums\SubmissionStatus;
use App\Models\EditorialCapability;
use App\Models\Review;
use App\Models\Submission;
use App\Models\User;
use App\Policies\SubmissionPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionPolicyEditorialTest extends TestCase
{
    use RefreshDatabase;

    private function makeSubmission(?int $editorId = null, ?int $layoutId = null, string $status = 'submitted'): Submission
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        return Submission::create([
            'author_id' => $author->id,
            'title' => 'T',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => $status,
            'editor_id' => $editorId,
            'layout_editor_id' => $layoutId,
        ]);
    }

    public function test_chief_editor_can_view_editorial(): void
    {
        $chief = User::factory()->create(['email_verified_at' => now()]);
        $chief->grantCapability(EditorialCapability::CHIEF_EDITOR);
        $submission = $this->makeSubmission();

        $this->assertTrue((new SubmissionPolicy())->viewEditorial($chief, $submission));
    }

    public function test_editor_of_the_article_can_view_editorial(): void
    {
        $editor = User::factory()->create(['email_verified_at' => now()]);
        $editor->grantCapability(EditorialCapability::EDITOR);
        $submission = $this->makeSubmission(editorId: $editor->id);

        $this->assertTrue((new SubmissionPolicy())->viewEditorial($editor, $submission));
    }

    public function test_random_user_cannot_view_editorial(): void
    {
        $stranger = User::factory()->create(['email_verified_at' => now()]);
        $submission = $this->makeSubmission();

        $this->assertFalse((new SubmissionPolicy())->viewEditorial($stranger, $submission));
    }

    public function test_take_editor_only_when_unassigned_and_capable(): void
    {
        $capable = User::factory()->create(['email_verified_at' => now()]);
        $capable->grantCapability(EditorialCapability::EDITOR);

        $nocap = User::factory()->create(['email_verified_at' => now()]);

        $unassigned = $this->makeSubmission(editorId: null);
        $assigned   = $this->makeSubmission(editorId: $capable->id);

        $policy = new SubmissionPolicy();
        $this->assertTrue($policy->takeEditor($capable, $unassigned));
        $this->assertFalse($policy->takeEditor($capable, $assigned));
        $this->assertFalse($policy->takeEditor($nocap, $unassigned));
    }

    public function test_only_chief_editor_or_admin_can_assign_editor(): void
    {
        $chief = User::factory()->create(['email_verified_at' => now()]);
        $chief->grantCapability(EditorialCapability::CHIEF_EDITOR);

        $editor = User::factory()->create(['email_verified_at' => now()]);
        $editor->grantCapability(EditorialCapability::EDITOR);

        $submission = $this->makeSubmission();
        $policy = new SubmissionPolicy();

        $this->assertTrue($policy->assignEditor($chief, $submission));
        $this->assertFalse($policy->assignEditor($editor, $submission));
    }

    public function test_editor_of_article_or_chief_can_assign_reviewer(): void
    {
        $chief = User::factory()->create(['email_verified_at' => now()]);
        $chief->grantCapability(EditorialCapability::CHIEF_EDITOR);

        $articleEditor = User::factory()->create(['email_verified_at' => now()]);
        $articleEditor->grantCapability(EditorialCapability::EDITOR);

        $otherEditor = User::factory()->create(['email_verified_at' => now()]);
        $otherEditor->grantCapability(EditorialCapability::EDITOR);

        $submission = $this->makeSubmission(editorId: $articleEditor->id, status: 'under_peer_review');
        $policy = new SubmissionPolicy();

        $this->assertTrue($policy->assignReviewer($chief, $submission));
        $this->assertTrue($policy->assignReviewer($articleEditor, $submission));
        $this->assertFalse($policy->assignReviewer($otherEditor, $submission));

        // Cannot assign reviewer on accepted/published submissions
        $accepted = $this->makeSubmission(editorId: $articleEditor->id, status: 'accepted');
        $this->assertFalse($policy->assignReviewer($chief, $accepted));
    }

    public function test_manage_capabilities_requires_admin_or_chief_editor(): void
    {
        $chief = User::factory()->create(['email_verified_at' => now()]);
        $chief->grantCapability(EditorialCapability::CHIEF_EDITOR);

        $editor = User::factory()->create(['email_verified_at' => now()]);
        $editor->grantCapability(EditorialCapability::EDITOR);

        $target = User::factory()->create(['email_verified_at' => now()]);
        $policy = new SubmissionPolicy();

        $this->assertTrue($policy->manageCapabilities($chief, $target));
        $this->assertFalse($policy->manageCapabilities($editor, $target));
    }
}
