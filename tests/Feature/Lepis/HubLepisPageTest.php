<?php

namespace Tests\Feature\Lepis;

use App\Models\LepisBulletin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HubLepisPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_lepis_page_shows_three_latest_visible_bulletins(): void
    {
        LepisBulletin::create($this->attrs(['issue_number' => 1, 'year' => 2025, 'status' => 'public', 'title' => 'Oldest']));
        LepisBulletin::create($this->attrs(['issue_number' => 2, 'year' => 2025, 'status' => 'public', 'title' => 'Older2']));
        LepisBulletin::create($this->attrs(['issue_number' => 3, 'year' => 2026, 'status' => 'public', 'title' => 'Middle']));
        LepisBulletin::create($this->attrs(['issue_number' => 4, 'year' => 2026, 'status' => 'members', 'title' => 'Latest']));
        LepisBulletin::create($this->attrs(['issue_number' => 5, 'year' => 2026, 'status' => 'draft', 'title' => 'SecretDraft']));

        $response = $this->get(route('hub.lepis'));

        $response->assertOk();
        $response->assertSee('Latest');
        $response->assertSee('Middle');
        $response->assertSee('Older2');
        $response->assertDontSee('Oldest');  // 4e → exclu (limit 3)
        $response->assertDontSee('SecretDraft');
    }

    private function attrs(array $o = []): array
    {
        return array_merge([
            'title' => 'T',
            'issue_number' => 1,
            'quarter' => 'Q1',
            'year' => 2026,
            'pdf_path' => 'lepis/t.pdf',
            'status' => 'public',
        ], $o);
    }
}
