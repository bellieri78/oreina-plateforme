<?php

namespace Tests\Feature\Lepis;

use App\Models\LepisBulletin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberLepisRedirectsTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_lepis_index_redirects_to_public_listing(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/espace-membre/lepis');
        $response->assertRedirect(route('hub.lepis.bulletins.index'));
        $this->assertSame(301, $response->status());
    }

    public function test_member_lepis_download_redirects_to_public_download(): void
    {
        $user = User::factory()->create();
        $bulletin = LepisBulletin::create([
            'title' => 'T', 'issue_number' => 1, 'quarter' => 'Q1', 'year' => 2026,
            'pdf_path' => 'lepis/t.pdf', 'status' => 'members',
        ]);

        $response = $this->actingAs($user)->get("/espace-membre/lepis/{$bulletin->id}/telecharger");

        $response->assertRedirect(route('hub.lepis.bulletins.download', $bulletin));
        $this->assertSame(301, $response->status());
    }
}
