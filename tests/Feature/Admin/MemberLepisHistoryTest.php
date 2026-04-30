<?php

namespace Tests\Feature\Admin;

use App\Models\LepisBulletin;
use App\Models\LepisBulletinRecipient;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberLepisHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_show_lists_lepis_recipients_chronologically(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();

        $b1 = LepisBulletin::create([
            'title' => 'Q1 2026', 'issue_number' => 1, 'quarter' => 'Q1', 'year' => 2026,
            'pdf_path' => 'x.pdf', 'status' => 'public',
            'published_to_members_at' => now()->subMonths(6),
            'published_public_at' => now()->subMonths(2),
        ]);
        $b2 = LepisBulletin::create([
            'title' => 'Q2 2026', 'issue_number' => 2, 'quarter' => 'Q2', 'year' => 2026,
            'pdf_path' => 'y.pdf', 'status' => 'members',
            'published_to_members_at' => now()->subDays(15),
        ]);

        LepisBulletinRecipient::create([
            'lepis_bulletin_id' => $b1->id, 'member_id' => $member->id,
            'format' => 'paper', 'included_at' => now()->subMonths(6),
        ]);
        LepisBulletinRecipient::create([
            'lepis_bulletin_id' => $b2->id, 'member_id' => $member->id,
            'format' => 'digital', 'included_at' => now()->subDays(15),
        ]);

        $response = $this->actingAs($admin)->get("/extranet/members/{$member->id}");

        $response->assertOk()
            ->assertSeeInOrder(['Q2 2026', 'Q1 2026']);  // anti-chronological
    }

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    private function makeMember(): Member
    {
        $u = User::factory()->create();
        return Member::create([
            'user_id' => $u->id, 'member_number' => 'M' . random_int(1, 99999),
            'email' => $u->email, 'first_name' => 'F', 'last_name' => 'L',
            'joined_at' => now(),
        ]);
    }
}
