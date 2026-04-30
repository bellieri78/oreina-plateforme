<?php

namespace Tests\Feature\Lepis;

use App\Jobs\SyncLepisBulletinToBrevoList;
use App\Models\LepisBulletin;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use App\Services\BrevoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class SyncLepisBulletinToBrevoListTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_brevo_list_and_imports_only_digital_members(): void
    {
        $bulletin = $this->makeBulletin();
        $this->makeCurrentMember('alice@example.com', 'digital');
        $this->makeCurrentMember('bob@example.com', 'digital');
        $this->makeCurrentMember('paperino@example.com', 'paper');
        $this->makeExpiredMember('old@example.com');

        $this->mock(BrevoService::class, function (MockInterface $mock) {
            $mock->shouldReceive('createList')
                ->once()
                ->with('Lepis 2026 Q2', \Mockery::any())
                ->andReturn(['success' => true, 'data' => ['id' => 123]]);

            $mock->shouldReceive('importContacts')
                ->once()
                ->withArgs(function ($members, $listId) {
                    $emails = $members->pluck('email')->sort()->values()->all();
                    return $listId === 123 && $emails === ['alice@example.com', 'bob@example.com'];
                })
                ->andReturn(['success' => true, 'count' => 2]);
        });

        (new SyncLepisBulletinToBrevoList($bulletin))->handle(app(BrevoService::class));

        $bulletin->refresh();
        $this->assertSame(123, $bulletin->brevo_list_id);
        $this->assertSame('Lepis 2026 Q2', $bulletin->brevo_list_name);
        $this->assertNotNull($bulletin->brevo_synced_at);
        $this->assertFalse($bulletin->brevo_sync_failed);

        // Snapshot must have been written: 2 digital + 1 paper recipients
        $recipients = $bulletin->recipients;
        $this->assertCount(3, $recipients);
        $this->assertSame(2, $recipients->where('format', 'digital')->count());
        $this->assertSame(1, $recipients->where('format', 'paper')->count());
        $this->assertSame(123, $recipients->where('format', 'digital')->first()->brevo_list_id);
    }

    public function test_throws_when_list_creation_fails(): void
    {
        $bulletin = $this->makeBulletin();

        $this->mock(BrevoService::class, function (MockInterface $mock) {
            $mock->shouldReceive('createList')
                ->andReturn(['success' => false, 'error' => 'api down']);
            $mock->shouldNotReceive('importContacts');
        });

        $this->expectException(\RuntimeException::class);
        (new SyncLepisBulletinToBrevoList($bulletin))->handle(app(BrevoService::class));
    }

    public function test_throws_when_import_fails(): void
    {
        $bulletin = $this->makeBulletin();
        $this->makeCurrentMember('alice@example.com');

        $this->mock(BrevoService::class, function (MockInterface $mock) {
            $mock->shouldReceive('createList')
                ->andReturn(['success' => true, 'data' => ['id' => 123]]);
            $mock->shouldReceive('importContacts')
                ->andReturn(['success' => false, 'error' => 'import rejected']);
        });

        $this->expectException(\RuntimeException::class);
        (new SyncLepisBulletinToBrevoList($bulletin))->handle(app(BrevoService::class));
    }

    public function test_failed_callback_marks_sync_failed(): void
    {
        $bulletin = $this->makeBulletin();

        $job = new SyncLepisBulletinToBrevoList($bulletin);
        $job->failed(new \RuntimeException('boom'));

        $bulletin->refresh();
        $this->assertTrue($bulletin->brevo_sync_failed);
    }

    private ?int $membershipTypeId = null;

    private function membershipTypeId(): int
    {
        if ($this->membershipTypeId === null) {
            $this->membershipTypeId = MembershipType::create([
                'name' => 'Standard',
                'slug' => 'standard',
                'price' => 30.00,
                'duration_months' => 12,
                'is_active' => true,
                'sort_order' => 1,
            ])->id;
        }
        return $this->membershipTypeId;
    }

    private function makeBulletin(): LepisBulletin
    {
        return LepisBulletin::create([
            'title' => 'T',
            'issue_number' => 42,
            'quarter' => 'Q2',
            'year' => 2026,
            'pdf_path' => 'lepis/t.pdf',
            'status' => 'members',
            'published_to_members_at' => now(),
        ]);
    }

    private function makeCurrentMember(string $email, string $format = 'digital'): Member
    {
        $user = User::factory()->create(['email' => $email]);
        $member = Member::create([
            'user_id' => $user->id,
            'member_number' => 'M' . substr(md5($email), 0, 6),
            'email' => $email,
            'first_name' => 'First',
            'last_name' => 'Last',
            'address' => '1 rue Test',
            'postal_code' => '75000',
            'city' => 'Paris',
            'country' => 'France',
            'joined_at' => now()->subYear(),
        ]);
        Membership::create([
            'member_id' => $member->id,
            'membership_type_id' => $this->membershipTypeId(),
            'status' => 'active',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
            'amount_paid' => 30.00,
            'lepis_format' => $format,
        ]);
        return $member;
    }

    private function makeExpiredMember(string $email): Member
    {
        $user = User::factory()->create(['email' => $email]);
        $member = Member::create([
            'user_id' => $user->id,
            'member_number' => 'M' . substr(md5($email), 0, 6),
            'email' => $email,
            'first_name' => 'Old',
            'last_name' => 'Member',
            'joined_at' => now()->subYears(3),
        ]);
        Membership::create([
            'member_id' => $member->id,
            'membership_type_id' => $this->membershipTypeId(),
            'status' => 'expired',
            'start_date' => now()->subYears(2),
            'end_date' => now()->subYear(),
            'amount_paid' => 30.00,
        ]);
        return $member;
    }
}
