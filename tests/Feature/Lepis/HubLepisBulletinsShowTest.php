<?php

namespace Tests\Feature\Lepis;

use App\Models\LepisBulletin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HubLepisBulletinsShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_detail_page_is_reachable_for_members_bulletin(): void
    {
        $bulletin = $this->makeBulletin([
            'status' => 'members',
            'title' => 'Dossier hibernation',
            'summary' => 'Le sommaire du numéro.',
        ]);

        $response = $this->get(route('hub.lepis.bulletins.show', $bulletin));

        $response->assertOk();
        $response->assertSee('Dossier hibernation');
        $response->assertSee('Le sommaire du numéro.');
    }

    public function test_detail_page_is_reachable_for_public_bulletin(): void
    {
        $bulletin = $this->makeBulletin(['status' => 'public']);
        $response = $this->get(route('hub.lepis.bulletins.show', $bulletin));
        $response->assertOk();
    }

    public function test_detail_page_returns_404_for_draft(): void
    {
        $bulletin = $this->makeBulletin(['status' => 'draft']);
        $response = $this->get(route('hub.lepis.bulletins.show', $bulletin));
        $response->assertNotFound();
    }

    public function test_detail_page_shows_previous_and_next_links(): void
    {
        $older = $this->makeBulletin(['issue_number' => 40, 'year' => 2026, 'status' => 'public']);
        $current = $this->makeBulletin(['issue_number' => 41, 'year' => 2026, 'status' => 'public']);
        $newer = $this->makeBulletin(['issue_number' => 42, 'year' => 2026, 'status' => 'public']);

        $response = $this->get(route('hub.lepis.bulletins.show', $current));

        $response->assertOk();
        $response->assertSee(route('hub.lepis.bulletins.show', $older), false);
        $response->assertSee(route('hub.lepis.bulletins.show', $newer), false);
    }

    private function makeBulletin(array $o = []): LepisBulletin
    {
        return LepisBulletin::create(array_merge([
            'title' => 'T',
            'issue_number' => 1,
            'quarter' => 'Q1',
            'year' => 2026,
            'pdf_path' => 'lepis/t.pdf',
            'status' => 'public',
        ], $o));
    }
}
