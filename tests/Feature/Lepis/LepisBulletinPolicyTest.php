<?php

namespace Tests\Feature\Lepis;

use App\Models\EditorialCapability;
use App\Models\LepisBulletin;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use App\Policies\LepisBulletinPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LepisBulletinPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_download_public_bulletin(): void
    {
        $bulletin = $this->makeBulletin('public');
        $this->assertTrue((new LepisBulletinPolicy())->download(null, $bulletin));
    }

    public function test_guest_cannot_download_members_bulletin(): void
    {
        $bulletin = $this->makeBulletin('members');
        $this->assertFalse((new LepisBulletinPolicy())->download(null, $bulletin));
    }

    public function test_guest_cannot_download_draft_bulletin(): void
    {
        $bulletin = $this->makeBulletin('draft');
        $this->assertFalse((new LepisBulletinPolicy())->download(null, $bulletin));
    }

    public function test_current_member_can_download_members_bulletin(): void
    {
        $bulletin = $this->makeBulletin('members');
        $user = $this->makeUserWithCurrentMembership();
        $this->assertTrue((new LepisBulletinPolicy())->download($user, $bulletin));
    }

    public function test_expired_member_cannot_download_members_bulletin(): void
    {
        $bulletin = $this->makeBulletin('members');
        $user = $this->makeUserWithExpiredMembership();
        $this->assertFalse((new LepisBulletinPolicy())->download($user, $bulletin));
    }

    public function test_lepis_editor_can_download_any_status(): void
    {
        $user = User::factory()->create();
        $user->grantCapability(EditorialCapability::LEPIS_EDITOR);
        $user = $user->fresh();

        foreach (['draft', 'members', 'public'] as $status) {
            $bulletin = $this->makeBulletin($status);
            $this->assertTrue(
                (new LepisBulletinPolicy())->download($user, $bulletin),
                "lepis_editor doit pouvoir télécharger un bulletin {$status}"
            );
        }
    }

    private function makeBulletin(string $status): LepisBulletin
    {
        return LepisBulletin::create([
            'title' => 'T',
            'issue_number' => 1,
            'quarter' => 'Q1',
            'year' => 2026,
            'pdf_path' => 'lepis/t.pdf',
            'status' => $status,
        ]);
    }

    private function makeUserWithCurrentMembership(): User
    {
        $user = User::factory()->create();
        $member = Member::create([
            'user_id' => $user->id,
            'member_number' => 'M' . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT),
            'email' => $user->email,
            'first_name' => 'A',
            'last_name' => 'B',
            'joined_at' => now()->subYear(),
        ]);
        $type = $this->ensureMembershipType();
        Membership::create([
            'member_id' => $member->id,
            'membership_type_id' => $type->id,
            'status' => 'active',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
            'amount_paid' => 0,
        ]);
        return $user->fresh();
    }

    private function makeUserWithExpiredMembership(): User
    {
        $user = User::factory()->create();
        $member = Member::create([
            'user_id' => $user->id,
            'member_number' => 'M' . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT),
            'email' => $user->email,
            'first_name' => 'X',
            'last_name' => 'Y',
            'joined_at' => now()->subYears(3),
        ]);
        $type = $this->ensureMembershipType();
        Membership::create([
            'member_id' => $member->id,
            'membership_type_id' => $type->id,
            'status' => 'expired',
            'start_date' => now()->subYears(2),
            'end_date' => now()->subYear(),
            'amount_paid' => 0,
        ]);
        return $user->fresh();
    }

    private function ensureMembershipType(): MembershipType
    {
        return MembershipType::firstOrCreate(
            ['slug' => 'test-standard'],
            ['name' => 'Adhésion test', 'price' => 0]
        );
    }
}
