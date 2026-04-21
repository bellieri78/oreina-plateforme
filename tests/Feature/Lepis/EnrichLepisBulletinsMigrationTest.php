<?php

namespace Tests\Feature\Lepis;

use App\Models\LepisBulletin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrichLepisBulletinsMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_bulletin_defaults_to_draft_status(): void
    {
        $bulletin = LepisBulletin::create([
            'title' => 'Test',
            'issue_number' => 1,
            'quarter' => 'Q1',
            'year' => 2026,
            'pdf_path' => 'lepis/test.pdf',
        ]);

        $this->assertSame('draft', $bulletin->fresh()->status);
        $this->assertNull($bulletin->published_to_members_at);
        $this->assertNull($bulletin->published_public_at);
    }

    public function test_new_columns_are_nullable(): void
    {
        $bulletin = LepisBulletin::create([
            'title' => 'Test',
            'issue_number' => 1,
            'quarter' => 'Q1',
            'year' => 2026,
            'pdf_path' => 'lepis/test.pdf',
        ]);

        $this->assertNull($bulletin->summary);
        $this->assertNull($bulletin->cover_image);
        $this->assertNull($bulletin->announcement_subject);
        $this->assertNull($bulletin->announcement_body);
        $this->assertNull($bulletin->brevo_list_id);
        $this->assertNull($bulletin->brevo_list_name);
        $this->assertNull($bulletin->brevo_synced_at);
        $this->assertFalse($bulletin->brevo_sync_failed);
    }
}
