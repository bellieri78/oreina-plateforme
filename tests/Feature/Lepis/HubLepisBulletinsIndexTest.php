<?php

namespace Tests\Feature\Lepis;

use App\Models\LepisBulletin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HubLepisBulletinsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_listing_shows_members_and_public_bulletins(): void
    {
        LepisBulletin::create($this->attrs(['issue_number' => 1, 'status' => 'members', 'title' => 'Members Bulletin']));
        LepisBulletin::create($this->attrs(['issue_number' => 2, 'status' => 'public', 'title' => 'Public Bulletin']));

        $response = $this->get(route('hub.lepis.bulletins.index'));

        $response->assertOk();
        $response->assertSee('Members Bulletin');
        $response->assertSee('Public Bulletin');
    }

    public function test_listing_never_shows_drafts(): void
    {
        LepisBulletin::create($this->attrs(['issue_number' => 1, 'status' => 'draft', 'title' => 'Secret Draft']));

        $response = $this->get(route('hub.lepis.bulletins.index'));

        $response->assertOk();
        $response->assertDontSee('Secret Draft');
    }

    public function test_listing_is_sorted_by_year_desc_then_issue_desc(): void
    {
        LepisBulletin::create($this->attrs(['issue_number' => 1, 'year' => 2025, 'status' => 'public', 'title' => 'Older']));
        LepisBulletin::create($this->attrs(['issue_number' => 42, 'year' => 2026, 'status' => 'public', 'title' => 'Latest']));

        $response = $this->get(route('hub.lepis.bulletins.index'));
        $content = $response->getContent();

        $posLatest = strpos($content, 'Latest');
        $posOlder  = strpos($content, 'Older');
        $this->assertNotFalse($posLatest);
        $this->assertNotFalse($posOlder);
        $this->assertLessThan($posOlder, $posLatest);
    }

    private function attrs(array $o = []): array
    {
        return array_merge([
            'title' => 'Default',
            'issue_number' => 1,
            'quarter' => 'Q1',
            'year' => 2026,
            'pdf_path' => 'lepis/t.pdf',
            'status' => 'public',
        ], $o);
    }
}
