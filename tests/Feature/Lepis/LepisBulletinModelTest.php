<?php

namespace Tests\Feature\Lepis;

use App\Models\LepisBulletin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LepisBulletinModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_scope_visible_on_hub_excludes_drafts(): void
    {
        LepisBulletin::create($this->baseAttrs(['status' => 'draft', 'issue_number' => 1]));
        LepisBulletin::create($this->baseAttrs(['status' => 'members', 'issue_number' => 2]));
        LepisBulletin::create($this->baseAttrs(['status' => 'public', 'issue_number' => 3]));

        $visible = LepisBulletin::visibleOnHub()->pluck('issue_number')->all();

        $this->assertEqualsCanonicalizing([2, 3], $visible);
    }

    public function test_status_helpers_return_expected_booleans(): void
    {
        $draft = LepisBulletin::create($this->baseAttrs(['status' => 'draft']));
        $members = LepisBulletin::create($this->baseAttrs(['status' => 'members', 'issue_number' => 2]));
        $public = LepisBulletin::create($this->baseAttrs(['status' => 'public', 'issue_number' => 3]));

        $this->assertTrue($draft->isDraft());
        $this->assertFalse($draft->isInMembersPhase());
        $this->assertFalse($draft->isPublic());

        $this->assertTrue($members->isInMembersPhase());
        $this->assertFalse($members->isDraft());

        $this->assertTrue($public->isPublic());
        $this->assertFalse($public->isDraft());
    }

    public function test_quarter_label_is_exposed(): void
    {
        $bulletin = LepisBulletin::create($this->baseAttrs(['quarter' => 'Q2']));
        $this->assertSame('Été', $bulletin->quarter_label);
    }

    public function test_brevo_list_url_accessor_builds_correct_url(): void
    {
        $bulletin = LepisBulletin::create($this->baseAttrs(['brevo_list_id' => 789]));
        $this->assertSame('https://app.brevo.com/contact/list/id/789', $bulletin->brevo_list_url);
    }

    public function test_brevo_list_url_returns_null_when_no_list_id(): void
    {
        $bulletin = LepisBulletin::create($this->baseAttrs());
        $this->assertNull($bulletin->brevo_list_url);
    }

    private function baseAttrs(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Test',
            'issue_number' => 1,
            'quarter' => 'Q1',
            'year' => 2026,
            'pdf_path' => 'lepis/test.pdf',
        ], $overrides);
    }
}
