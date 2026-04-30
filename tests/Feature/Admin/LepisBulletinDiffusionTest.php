<?php

namespace Tests\Feature\Admin;

use App\Models\LepisBulletin;
use App\Models\LepisBulletinRecipient;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LepisBulletinDiffusionTest extends TestCase
{
    use RefreshDatabase;

    public function test_diffusion_card_visible_only_when_status_is_members_or_public(): void
    {
        $admin = $this->makeAdmin();
        $draft = $this->makeBulletin('draft');
        $members = $this->makeBulletin('members');

        $resDraft = $this->actingAs($admin)->get("/extranet/lepis/{$draft->id}/edit");
        $resDraft->assertOk()->assertDontSee('Diffusion');

        $resMembers = $this->actingAs($admin)->get("/extranet/lepis/{$members->id}/edit");
        $resMembers->assertOk()->assertSee('Diffusion');
    }

    public function test_diffusion_card_shows_paper_and_digital_counts(): void
    {
        $admin = $this->makeAdmin();
        $bulletin = $this->makeBulletin('members');
        $this->makeRecipient($bulletin, 'paper');
        $this->makeRecipient($bulletin, 'paper');
        $this->makeRecipient($bulletin, 'digital');

        $response = $this->actingAs($admin)->get("/extranet/lepis/{$bulletin->id}/edit");

        $response->assertOk()
            ->assertSee('Papier')
            ->assertSee('Numerique');
    }

    public function test_export_csv_contains_only_paper_recipients(): void
    {
        $admin = $this->makeAdmin();
        $bulletin = $this->makeBulletin('members');

        $u1 = User::factory()->create(['email' => 'paper@x.com']);
        $paperMember = Member::create([
            'user_id' => $u1->id, 'member_number' => 'M111', 'email' => 'paper@x.com',
            'first_name' => 'Paul', 'last_name' => 'Papier', 'joined_at' => now(),
        ]);
        LepisBulletinRecipient::create([
            'lepis_bulletin_id' => $bulletin->id, 'member_id' => $paperMember->id,
            'format' => 'paper',
            'email_at_snapshot' => 'paper@x.com',
            'postal_address_at_snapshot' => ['address' => '1 rue P', 'postal_code' => '75001', 'city' => 'Paris', 'country' => 'France'],
            'included_at' => now(),
        ]);

        $u2 = User::factory()->create(['email' => 'digital@x.com']);
        $digitalMember = Member::create([
            'user_id' => $u2->id, 'member_number' => 'M222', 'email' => 'digital@x.com',
            'first_name' => 'Diane', 'last_name' => 'Digitale', 'joined_at' => now(),
        ]);
        LepisBulletinRecipient::create([
            'lepis_bulletin_id' => $bulletin->id, 'member_id' => $digitalMember->id,
            'format' => 'digital',
            'email_at_snapshot' => 'digital@x.com',
            'included_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get("/extranet/lepis/{$bulletin->id}/recipients/export?format=paper");

        $response->assertOk();
        $csv = $response->streamedContent();
        $this->assertStringContainsString('Paul', $csv);
        $this->assertStringContainsString('PAPIER', $csv);
        $this->assertStringNotContainsString('Diane', $csv);
    }

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    private function makeBulletin(string $status): LepisBulletin
    {
        return LepisBulletin::create([
            'title' => 'T', 'issue_number' => random_int(1, 1000), 'quarter' => 'Q1', 'year' => 2026,
            'pdf_path' => 'x.pdf', 'status' => $status,
            'published_to_members_at' => $status !== 'draft' ? now() : null,
        ]);
    }

    private function makeRecipient(LepisBulletin $bulletin, string $format): LepisBulletinRecipient
    {
        $u = User::factory()->create();
        $member = Member::create([
            'user_id' => $u->id, 'member_number' => 'M' . random_int(1, 99999),
            'email' => $u->email, 'first_name' => 'F', 'last_name' => 'L',
            'joined_at' => now(),
        ]);
        return LepisBulletinRecipient::create([
            'lepis_bulletin_id' => $bulletin->id, 'member_id' => $member->id,
            'format' => $format, 'included_at' => now(),
        ]);
    }
}
