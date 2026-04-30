<?php

namespace Tests\Feature\Console;

use App\Models\LepisBulletin;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LepisBackfillRecipientsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_backfills_all_published_bulletins(): void
    {
        MembershipType::create(['name' => 'S', 'slug' => 's', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]);
        $bDraft = LepisBulletin::create([
            'title' => 'Draft', 'issue_number' => 1, 'quarter' => 'Q1', 'year' => 2026,
            'pdf_path' => 'd.pdf', 'status' => 'draft',
        ]);
        $bMembers = LepisBulletin::create([
            'title' => 'Members', 'issue_number' => 2, 'quarter' => 'Q2', 'year' => 2026,
            'pdf_path' => 'm.pdf', 'status' => 'members',
            'published_to_members_at' => now()->subDays(15),
        ]);
        $bPublic = LepisBulletin::create([
            'title' => 'Public', 'issue_number' => 3, 'quarter' => 'Q3', 'year' => 2026,
            'pdf_path' => 'p.pdf', 'status' => 'public',
            'published_to_members_at' => now()->subDays(60),
            'published_public_at' => now()->subDays(15),
        ]);

        $u = User::factory()->create();
        $member = Member::create([
            'user_id' => $u->id, 'member_number' => 'M1', 'email' => $u->email,
            'first_name' => 'F', 'last_name' => 'L', 'joined_at' => now()->subYear(),
            'address' => '1 rue X', 'postal_code' => '75000', 'city' => 'Paris', 'country' => 'France',
        ]);
        Membership::create([
            'member_id' => $member->id, 'status' => 'active',
            'membership_type_id' => MembershipType::first()->id,
            'start_date' => now()->subYear(), 'end_date' => now()->addYear(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);

        $this->artisan('lepis:backfill-recipients')->assertSuccessful();

        $this->assertCount(0, $bDraft->fresh()->recipients);
        $this->assertCount(1, $bMembers->fresh()->recipients);
        $this->assertCount(1, $bPublic->fresh()->recipients);
    }
}
