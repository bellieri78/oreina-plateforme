<?php

namespace Tests\Feature\Admin;

use App\Enums\ConformityChecklistItem;
use App\Models\EditorialCapability;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConformityChecklistTest extends TestCase
{
    use RefreshDatabase;

    private function makeSubmission(?User $editor = null): Submission
    {
        $author = User::factory()->create();
        return Submission::create([
            'author_id' => $author->id,
            'editor_id' => $editor?->id,
            'title' => 'Test',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
            'status' => 'under_peer_review',
        ]);
    }

    private function makeEditor(string $cap = EditorialCapability::EDITOR): User
    {
        $u = User::factory()->create();
        $u->grantCapability($cap);
        return $u;
    }

    public function test_assigned_editor_can_check_item(): void
    {
        $editor = $this->makeEditor();
        $sub = $this->makeSubmission($editor);

        $response = $this->actingAs($editor)
            ->patch(route('admin.submissions.conformity.update', $sub), [
                'item' => ConformityChecklistItem::BiblioFormat->value,
                'checked' => true,
            ]);

        $response->assertOk();
        $response->assertJson(['checked' => 1, 'total' => 9]);
        $this->assertEquals(['biblio_format'], $sub->fresh()->conformity_checklist);
    }

    public function test_assigned_editor_can_uncheck_item(): void
    {
        $editor = $this->makeEditor();
        $sub = $this->makeSubmission($editor);
        $sub->update(['conformity_checklist' => ['biblio_format', 'figures_numbered']]);

        $response = $this->actingAs($editor)
            ->patch(route('admin.submissions.conformity.update', $sub), [
                'item' => ConformityChecklistItem::BiblioFormat->value,
                'checked' => false,
            ]);

        $response->assertOk();
        $response->assertJson(['checked' => 1, 'total' => 9]);
        $this->assertEquals(['figures_numbered'], array_values($sub->fresh()->conformity_checklist));
    }

    public function test_chief_editor_can_check_without_being_assigned(): void
    {
        $chief = $this->makeEditor(EditorialCapability::CHIEF_EDITOR);
        $sub = $this->makeSubmission();

        $response = $this->actingAs($chief)
            ->patch(route('admin.submissions.conformity.update', $sub), [
                'item' => ConformityChecklistItem::AuthorAffiliations->value,
                'checked' => true,
            ]);

        $response->assertOk();
        $this->assertTrue(in_array('author_affiliations', $sub->fresh()->conformity_checklist));
    }

    public function test_admin_can_check(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $sub = $this->makeSubmission();

        $response = $this->actingAs($admin)
            ->patch(route('admin.submissions.conformity.update', $sub), [
                'item' => ConformityChecklistItem::FiguresNumbered->value,
                'checked' => true,
            ]);

        $response->assertOk();
    }

    public function test_non_assigned_editor_gets_403(): void
    {
        $assigned = $this->makeEditor();
        $stranger = $this->makeEditor();
        $sub = $this->makeSubmission($assigned);

        $response = $this->actingAs($stranger)
            ->patch(route('admin.submissions.conformity.update', $sub), [
                'item' => ConformityChecklistItem::BiblioFormat->value,
                'checked' => true,
            ]);

        $response->assertForbidden();
    }

    public function test_reviewer_gets_403(): void
    {
        $reviewer = $this->makeEditor(EditorialCapability::REVIEWER);
        $sub = $this->makeSubmission();

        $response = $this->actingAs($reviewer)
            ->patch(route('admin.submissions.conformity.update', $sub), [
                'item' => ConformityChecklistItem::BiblioFormat->value,
                'checked' => true,
            ]);

        $response->assertForbidden();
    }

    public function test_toggle_is_idempotent(): void
    {
        $editor = $this->makeEditor();
        $sub = $this->makeSubmission($editor);

        $this->actingAs($editor)->patch(route('admin.submissions.conformity.update', $sub), [
            'item' => 'biblio_format',
            'checked' => true,
        ]);
        $this->actingAs($editor)->patch(route('admin.submissions.conformity.update', $sub), [
            'item' => 'biblio_format',
            'checked' => true,
        ]);

        $this->assertEquals(['biblio_format'], $sub->fresh()->conformity_checklist);
    }

    public function test_invalid_item_returns_422(): void
    {
        $editor = $this->makeEditor();
        $sub = $this->makeSubmission($editor);

        $response = $this->actingAs($editor)
            ->withHeader('Accept', 'application/json')
            ->patch(route('admin.submissions.conformity.update', $sub), [
                'item' => 'not_a_real_item',
                'checked' => true,
            ]);

        $response->assertStatus(422);
    }

    public function test_missing_checked_returns_422(): void
    {
        $editor = $this->makeEditor();
        $sub = $this->makeSubmission($editor);

        $response = $this->actingAs($editor)
            ->withHeader('Accept', 'application/json')
            ->patch(route('admin.submissions.conformity.update', $sub), [
                'item' => 'biblio_format',
            ]);

        $response->assertStatus(422);
    }

    public function test_checklist_persists_across_status_change(): void
    {
        $editor = $this->makeEditor();
        $sub = $this->makeSubmission($editor);
        $sub->update(['conformity_checklist' => ['biblio_format', 'figures_numbered']]);

        $sub->update(['status' => 'revision_requested']);
        $sub->update(['status' => 'under_initial_review']);

        $this->assertEquals(['biblio_format', 'figures_numbered'], $sub->fresh()->conformity_checklist);
    }
}
