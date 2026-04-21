<?php

namespace Tests\Feature\Lepis;

use App\Exceptions\Lepis\InvalidTransitionException;
use App\Exceptions\Lepis\MissingPdfException;
use App\Jobs\SyncLepisBulletinToBrevoList;
use App\Models\LepisBulletin;
use App\Services\LepisBulletinPublicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LepisBulletinPublicationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_publish_to_members_transitions_from_draft(): void
    {
        Queue::fake();
        $bulletin = $this->makeBulletin(['status' => 'draft']);

        app(LepisBulletinPublicationService::class)->publishToMembers($bulletin);

        $bulletin->refresh();
        $this->assertSame('members', $bulletin->status);
        $this->assertNotNull($bulletin->published_to_members_at);
        Queue::assertPushed(SyncLepisBulletinToBrevoList::class);
    }

    public function test_publish_to_members_rejects_bulletin_without_pdf(): void
    {
        Queue::fake();
        $bulletin = $this->makeBulletin(['status' => 'draft', 'pdf_path' => null]);

        $this->expectException(MissingPdfException::class);
        app(LepisBulletinPublicationService::class)->publishToMembers($bulletin);
    }

    public function test_publish_to_members_rejects_non_draft(): void
    {
        Queue::fake();
        $bulletin = $this->makeBulletin(['status' => 'members']);

        $this->expectException(InvalidTransitionException::class);
        app(LepisBulletinPublicationService::class)->publishToMembers($bulletin);
    }

    public function test_make_public_transitions_from_members(): void
    {
        $bulletin = $this->makeBulletin(['status' => 'members']);

        app(LepisBulletinPublicationService::class)->makePublic($bulletin);

        $bulletin->refresh();
        $this->assertSame('public', $bulletin->status);
        $this->assertNotNull($bulletin->published_public_at);
    }

    public function test_make_public_rejects_draft(): void
    {
        $bulletin = $this->makeBulletin(['status' => 'draft']);

        $this->expectException(InvalidTransitionException::class);
        app(LepisBulletinPublicationService::class)->makePublic($bulletin);
    }

    public function test_make_public_rejects_already_public(): void
    {
        $bulletin = $this->makeBulletin(['status' => 'public']);

        $this->expectException(InvalidTransitionException::class);
        app(LepisBulletinPublicationService::class)->makePublic($bulletin);
    }

    public function test_revert_to_draft_works_when_brevo_not_synced(): void
    {
        $bulletin = $this->makeBulletin([
            'status' => 'members',
            'published_to_members_at' => now(),
            'brevo_synced_at' => null,
            'brevo_list_id' => 123,
            'brevo_list_name' => 'Lepis 2026 Q1',
            'brevo_sync_failed' => true,
        ]);

        app(LepisBulletinPublicationService::class)->revertToDraft($bulletin);

        $bulletin->refresh();
        $this->assertSame('draft', $bulletin->status);
        $this->assertNull($bulletin->published_to_members_at);
        $this->assertNull($bulletin->brevo_list_id);
        $this->assertNull($bulletin->brevo_list_name);
        $this->assertFalse($bulletin->brevo_sync_failed);
    }

    public function test_revert_to_draft_fails_after_brevo_sync(): void
    {
        $bulletin = $this->makeBulletin([
            'status' => 'members',
            'published_to_members_at' => now(),
            'brevo_synced_at' => now(),
            'brevo_list_id' => 42,
        ]);

        $this->expectException(InvalidTransitionException::class);
        app(LepisBulletinPublicationService::class)->revertToDraft($bulletin);
    }

    public function test_resync_brevo_works_from_members_phase(): void
    {
        Queue::fake();
        $bulletin = $this->makeBulletin([
            'status' => 'members',
            'brevo_sync_failed' => true,
        ]);

        app(LepisBulletinPublicationService::class)->resyncBrevo($bulletin);

        $bulletin->refresh();
        $this->assertFalse($bulletin->brevo_sync_failed);
        Queue::assertPushed(SyncLepisBulletinToBrevoList::class);
    }

    public function test_resync_brevo_works_from_public_phase(): void
    {
        Queue::fake();
        $bulletin = $this->makeBulletin([
            'status' => 'public',
            'published_to_members_at' => now()->subWeek(),
            'published_public_at' => now(),
        ]);

        app(LepisBulletinPublicationService::class)->resyncBrevo($bulletin);

        Queue::assertPushed(SyncLepisBulletinToBrevoList::class);
    }

    public function test_resync_brevo_rejects_draft(): void
    {
        Queue::fake();
        $bulletin = $this->makeBulletin(['status' => 'draft']);

        $this->expectException(InvalidTransitionException::class);
        app(LepisBulletinPublicationService::class)->resyncBrevo($bulletin);
    }

    private function makeBulletin(array $overrides = []): LepisBulletin
    {
        return LepisBulletin::create(array_merge([
            'title' => 'Test',
            'issue_number' => 1,
            'quarter' => 'Q1',
            'year' => 2026,
            'pdf_path' => 'lepis/test.pdf',
            'status' => 'draft',
        ], $overrides));
    }
}
