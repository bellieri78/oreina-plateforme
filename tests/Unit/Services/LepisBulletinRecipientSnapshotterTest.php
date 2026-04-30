<?php

namespace Tests\Unit\Services;

use App\Models\LepisBulletin;
use App\Models\LepisBulletinRecipient;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use App\Services\LepisBulletinRecipientSnapshotter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LepisBulletinRecipientSnapshotterTest extends TestCase
{
    use RefreshDatabase;

    public function test_snapshots_active_paper_and_digital_members(): void
    {
        $bulletin = $this->makeBulletin();
        $paper = $this->makeMemberWithMembership('paper', 'paper@test.com', address: '1 rue X');
        $digital = $this->makeMemberWithMembership('digital', 'digital@test.com', address: '2 rue Y');

        $result = (new LepisBulletinRecipientSnapshotter())->snapshot($bulletin);

        $this->assertSame(1, $result->paperCount);
        $this->assertSame(1, $result->digitalCount);
        $this->assertCount(2, $bulletin->fresh()->recipients);
    }

    public function test_skips_membership_expired_at_publication_date(): void
    {
        $bulletin = $this->makeBulletin();
        $this->makeMemberWithMembership('paper', 'old@test.com', startDaysAgo: 800, endDaysAgo: 30, address: '1 rue X');

        $result = (new LepisBulletinRecipientSnapshotter())->snapshot($bulletin);

        $this->assertSame(0, $result->paperCount);
        $this->assertCount(0, $bulletin->fresh()->recipients);
    }

    public function test_falls_back_to_paper_when_lepis_format_is_null(): void
    {
        $bulletin = $this->makeBulletin();
        $this->makeMemberWithMembership(null, 'nullformat@test.com', address: '1 rue X');

        $result = (new LepisBulletinRecipientSnapshotter())->snapshot($bulletin);

        $this->assertSame(1, $result->paperCount);
        $this->assertSame('paper', $bulletin->fresh()->recipients->first()->format);
    }

    public function test_skips_digital_member_without_email(): void
    {
        $bulletin = $this->makeBulletin();
        $member = $this->makeMemberWithMembership('digital', null, address: '1 rue X');

        $result = (new LepisBulletinRecipientSnapshotter())->snapshot($bulletin);

        $this->assertSame(0, $result->digitalCount);
        $this->assertCount(1, $result->skipped);
        $this->assertSame($member->id, $result->skipped[0]['member_id']);
        $this->assertStringContainsString('email', $result->skipped[0]['reason']);
    }

    public function test_skips_paper_member_with_incomplete_address(): void
    {
        $bulletin = $this->makeBulletin();
        $member = $this->makeMemberWithMembership('paper', 'paper@test.com', address: null);

        $result = (new LepisBulletinRecipientSnapshotter())->snapshot($bulletin);

        $this->assertSame(0, $result->paperCount);
        $this->assertCount(1, $result->skipped);
        $this->assertStringContainsString('address', $result->skipped[0]['reason']);
    }

    public function test_is_idempotent_on_second_run(): void
    {
        $bulletin = $this->makeBulletin();
        $this->makeMemberWithMembership('paper', 'p@test.com', address: '1 rue X');

        (new LepisBulletinRecipientSnapshotter())->snapshot($bulletin);
        (new LepisBulletinRecipientSnapshotter())->snapshot($bulletin);

        $this->assertCount(1, $bulletin->fresh()->recipients);
    }

    public function test_freezes_email_and_address_at_snapshot_time(): void
    {
        $bulletin = $this->makeBulletin();
        $member = $this->makeMemberWithMembership('digital', 'before@test.com', address: '1 rue Avant');

        (new LepisBulletinRecipientSnapshotter())->snapshot($bulletin);

        $member->update(['email' => 'after@test.com', 'address' => '99 rue Apres']);

        $r = $bulletin->fresh()->recipients->first();
        $this->assertSame('before@test.com', $r->email_at_snapshot);
    }

    public function test_picks_most_recent_membership_when_multiple(): void
    {
        $bulletin = $this->makeBulletin();
        $member = $this->makeMemberWithMembership('paper', 'm@test.com', address: '1 rue X', endDaysAgo: -30);
        // Add a more recent active membership with format=digital
        Membership::create([
            'member_id' => $member->id,
            'membership_type_id' => $this->membershipTypeId(),
            'status' => 'active',
            'start_date' => now()->subDays(10),
            'end_date' => now()->addYear(),
            'amount_paid' => 30.00,
            'lepis_format' => 'digital',
        ]);

        $result = (new LepisBulletinRecipientSnapshotter())->snapshot($bulletin);

        $this->assertSame(0, $result->paperCount);
        $this->assertSame(1, $result->digitalCount);
    }

    private ?int $membershipTypeId = null;

    private function membershipTypeId(): int
    {
        if ($this->membershipTypeId === null) {
            $this->membershipTypeId = MembershipType::create([
                'name' => 'Standard', 'slug' => 'standard', 'price' => 30.00,
                'duration_months' => 12, 'is_active' => true, 'sort_order' => 1,
            ])->id;
        }
        return $this->membershipTypeId;
    }

    private function makeBulletin(): LepisBulletin
    {
        return LepisBulletin::create([
            'title' => 'T', 'issue_number' => 1, 'quarter' => 'Q2', 'year' => 2026,
            'pdf_path' => 'x.pdf', 'status' => 'members', 'published_to_members_at' => now(),
        ]);
    }

    private function makeMemberWithMembership(
        ?string $format,
        ?string $email,
        ?string $address = null,
        int $startDaysAgo = 30,
        int $endDaysAgo = -365
    ): Member {
        $user = User::factory()->create(['email' => $email ?: 'user' . random_int(1000, 9999) . '@test.com']);
        $member = Member::create([
            'user_id' => $user->id,
            'member_number' => 'M' . random_int(1000, 9999),
            'email' => $email,
            'first_name' => 'F', 'last_name' => 'L',
            'address' => $address,
            'postal_code' => $address ? '75000' : null,
            'city' => $address ? 'Paris' : null,
            'country' => 'France',
            'joined_at' => now()->subYear(),
        ]);
        Membership::create([
            'member_id' => $member->id,
            'membership_type_id' => $this->membershipTypeId(),
            'status' => 'active',
            'start_date' => now()->subDays($startDaysAgo),
            'end_date' => $endDaysAgo >= 0 ? now()->subDays($endDaysAgo) : now()->addDays(-$endDaysAgo),
            'amount_paid' => 30.00,
            'lepis_format' => $format,
        ]);
        return $member;
    }
}
