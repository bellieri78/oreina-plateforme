<?php

namespace Tests\Unit\Models;

use App\Enums\SubmissionStatus;
use App\Models\EditorialCapability;
use App\Models\Submission;
use App\Models\SubmissionTransition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditorialCapabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_granted_a_capability(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $granter = User::factory()->create(['email_verified_at' => now()]);

        $cap = $user->grantCapability(EditorialCapability::EDITOR, $granter);

        $this->assertInstanceOf(EditorialCapability::class, $cap);
        $this->assertSame($user->id, $cap->user_id);
        $this->assertSame(EditorialCapability::EDITOR, $cap->capability);
        $this->assertSame($granter->id, $cap->granted_by_user_id);
        $this->assertTrue($user->hasCapability(EditorialCapability::EDITOR));
    }

    public function test_granting_same_capability_twice_is_idempotent(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->grantCapability(EditorialCapability::EDITOR);
        $user->grantCapability(EditorialCapability::EDITOR);

        $this->assertSame(1, $user->capabilities()->count());
    }

    public function test_capability_can_be_revoked(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->grantCapability(EditorialCapability::EDITOR);
        $this->assertTrue($user->hasCapability(EditorialCapability::EDITOR));

        $user->revokeCapability(EditorialCapability::EDITOR);
        $this->assertFalse($user->fresh()->hasCapability(EditorialCapability::EDITOR));
    }

    public function test_scope_with_capability_returns_eligible_users(): void
    {
        $editor = User::factory()->create(['email_verified_at' => now()]);
        $reviewer = User::factory()->create(['email_verified_at' => now()]);
        $nothing = User::factory()->create(['email_verified_at' => now()]);

        $editor->grantCapability(EditorialCapability::EDITOR);
        $reviewer->grantCapability(EditorialCapability::REVIEWER);

        $editors = User::withCapability(EditorialCapability::EDITOR)->pluck('id')->all();

        $this->assertContains($editor->id, $editors);
        $this->assertNotContains($reviewer->id, $editors);
        $this->assertNotContains($nothing->id, $editors);
    }

    public function test_submission_has_layout_editor_relation(): void
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        $layoutEditor = User::factory()->create(['email_verified_at' => now()]);

        $submission = Submission::create([
            'author_id' => $author->id,
            'title' => 'Test',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => SubmissionStatus::Accepted,
            'layout_editor_id' => $layoutEditor->id,
        ]);

        $this->assertSame($layoutEditor->id, $submission->layoutEditor->id);
    }

    public function test_submission_has_transitions_relation(): void
    {
        $author = User::factory()->create(['email_verified_at' => now()]);

        $submission = Submission::create([
            'author_id' => $author->id,
            'title' => 'Test',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => SubmissionStatus::Submitted,
        ]);

        SubmissionTransition::create([
            'submission_id' => $submission->id,
            'actor_user_id' => $author->id,
            'action' => SubmissionTransition::ACTION_EDITOR_TAKEN,
        ]);

        $this->assertCount(1, $submission->fresh()->transitions);
    }
}
