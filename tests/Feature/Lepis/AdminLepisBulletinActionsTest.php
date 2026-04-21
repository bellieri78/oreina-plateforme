<?php

namespace Tests\Feature\Lepis;

use App\Jobs\SyncLepisBulletinToBrevoList;
use App\Models\EditorialCapability;
use App\Models\LepisBulletin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AdminLepisBulletinActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_lepis_editor_can_publish_to_members(): void
    {
        Queue::fake();
        $editor = $this->lepisEditor();
        $bulletin = $this->makeBulletin(['status' => 'draft']);

        $response = $this->actingAs($editor)
            ->post(route('admin.lepis.publish-to-members', $bulletin));

        $response->assertRedirect();
        $this->assertSame('members', $bulletin->fresh()->status);
        Queue::assertPushed(SyncLepisBulletinToBrevoList::class);
    }

    public function test_non_privileged_user_cannot_reach_admin_lepis(): void
    {
        $user = User::factory()->create();
        $bulletin = $this->makeBulletin(['status' => 'draft']);

        $response = $this->actingAs($user)
            ->post(route('admin.lepis.publish-to-members', $bulletin));

        // The CheckAdmin middleware blocks non-privileged users.
        $this->assertNotSame(200, $response->status());
        $this->assertSame('draft', $bulletin->fresh()->status);
    }

    public function test_make_public_transitions_from_members(): void
    {
        $editor = $this->lepisEditor();
        $bulletin = $this->makeBulletin([
            'status' => 'members',
            'published_to_members_at' => now(),
        ]);

        $response = $this->actingAs($editor)
            ->post(route('admin.lepis.make-public', $bulletin));

        $response->assertRedirect();
        $this->assertSame('public', $bulletin->fresh()->status);
        $this->assertNotNull($bulletin->fresh()->published_public_at);
    }

    public function test_update_announcement_persists_subject_and_body(): void
    {
        $editor = $this->lepisEditor();
        $bulletin = $this->makeBulletin(['status' => 'members']);

        $response = $this->actingAs($editor)
            ->put(route('admin.lepis.announcement', $bulletin), [
                'announcement_subject' => 'Lepis n°42',
                'announcement_body' => 'Bonjour, {{lien_bulletin}}',
            ]);

        $response->assertRedirect();
        $bulletin->refresh();
        $this->assertSame('Lepis n°42', $bulletin->announcement_subject);
        $this->assertSame('Bonjour, {{lien_bulletin}}', $bulletin->announcement_body);
    }

    public function test_resync_brevo_dispatches_job(): void
    {
        Queue::fake();
        $editor = $this->lepisEditor();
        $bulletin = $this->makeBulletin([
            'status' => 'members',
            'brevo_sync_failed' => true,
        ]);

        $response = $this->actingAs($editor)
            ->post(route('admin.lepis.resync-brevo', $bulletin));

        $response->assertRedirect();
        Queue::assertPushed(SyncLepisBulletinToBrevoList::class);
        $this->assertFalse($bulletin->fresh()->brevo_sync_failed);
    }

    public function test_revert_to_draft_is_reachable(): void
    {
        $editor = $this->lepisEditor();
        $bulletin = $this->makeBulletin([
            'status' => 'members',
            'published_to_members_at' => now(),
            'brevo_synced_at' => null,
        ]);

        $response = $this->actingAs($editor)
            ->post(route('admin.lepis.revert-to-draft', $bulletin));

        $response->assertRedirect();
        $this->assertSame('draft', $bulletin->fresh()->status);
    }

    private function lepisEditor(): User
    {
        $user = User::factory()->create();
        $user->grantCapability(EditorialCapability::LEPIS_EDITOR);
        return $user->fresh();
    }

    private function makeBulletin(array $overrides = []): LepisBulletin
    {
        return LepisBulletin::create(array_merge([
            'title' => 'T',
            'issue_number' => 1,
            'quarter' => 'Q1',
            'year' => 2026,
            'pdf_path' => 'lepis/t.pdf',
            'status' => 'draft',
        ], $overrides));
    }
}
