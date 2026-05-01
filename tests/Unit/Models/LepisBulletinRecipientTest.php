<?php

namespace Tests\Unit\Models;

use App\Models\LepisBulletin;
use App\Models\LepisBulletinRecipient;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LepisBulletinRecipientTest extends TestCase
{
    use RefreshDatabase;

    public function test_relationships_are_wired(): void
    {
        $bulletin = LepisBulletin::create([
            'title' => 'T',
            'issue_number' => 1,
            'quarter' => 'Q1',
            'year' => 2026,
            'pdf_path' => 'x.pdf',
            'status' => 'members',
            'published_to_members_at' => now(),
        ]);
        $user = User::factory()->create();
        $member = Member::create([
            'user_id' => $user->id,
            'member_number' => 'M1',
            'email' => 'a@b.test',
            'first_name' => 'A',
            'last_name' => 'B',
            'joined_at' => now(),
        ]);

        $r = LepisBulletinRecipient::create([
            'lepis_bulletin_id' => $bulletin->id,
            'member_id' => $member->id,
            'format' => 'paper',
            'included_at' => now(),
        ]);

        $this->assertSame($bulletin->id, $r->bulletin->id);
        $this->assertSame($member->id, $r->member->id);
        $this->assertCount(1, $bulletin->recipients);
        $this->assertCount(1, $member->lepisBulletinRecipients);
    }

    public function test_postal_address_is_cast_to_array(): void
    {
        $bulletin = LepisBulletin::create([
            'title' => 'T', 'issue_number' => 1, 'quarter' => 'Q1', 'year' => 2026,
            'pdf_path' => 'x.pdf', 'status' => 'members', 'published_to_members_at' => now(),
        ]);
        $user = User::factory()->create();
        $member = Member::create([
            'user_id' => $user->id, 'member_number' => 'M2', 'email' => 'c@d.test',
            'first_name' => 'C', 'last_name' => 'D', 'joined_at' => now(),
        ]);

        $r = LepisBulletinRecipient::create([
            'lepis_bulletin_id' => $bulletin->id,
            'member_id' => $member->id,
            'format' => 'paper',
            'postal_address_at_snapshot' => ['address' => '1 rue X', 'city' => 'Paris'],
            'included_at' => now(),
        ]);

        $this->assertIsArray($r->fresh()->postal_address_at_snapshot);
        $this->assertSame('1 rue X', $r->fresh()->postal_address_at_snapshot['address']);
    }
}
