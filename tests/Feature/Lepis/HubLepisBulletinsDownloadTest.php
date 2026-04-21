<?php

namespace Tests\Feature\Lepis;

use App\Models\EditorialCapability;
use App\Models\LepisBulletin;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HubLepisBulletinsDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_downloads_public_bulletin(): void
    {
        Storage::fake('public');
        $bulletin = $this->makeBulletinWithPdf('public');

        $response = $this->get(route('hub.lepis.bulletins.download', $bulletin));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type', ''));
    }

    public function test_guest_cannot_download_members_bulletin(): void
    {
        Storage::fake('public');
        $bulletin = $this->makeBulletinWithPdf('members');

        $response = $this->get(route('hub.lepis.bulletins.download', $bulletin));

        $response->assertForbidden();
    }

    public function test_guest_cannot_download_draft_bulletin(): void
    {
        Storage::fake('public');
        $bulletin = $this->makeBulletinWithPdf('draft');

        $response = $this->get(route('hub.lepis.bulletins.download', $bulletin));

        $response->assertForbidden();
    }

    public function test_current_member_can_download_members_bulletin(): void
    {
        Storage::fake('public');
        $bulletin = $this->makeBulletinWithPdf('members');
        $user = $this->makeUserWithCurrentMembership();

        $response = $this->actingAs($user)->get(route('hub.lepis.bulletins.download', $bulletin));

        $response->assertOk();
    }

    public function test_expired_member_cannot_download_members_bulletin(): void
    {
        Storage::fake('public');
        $bulletin = $this->makeBulletinWithPdf('members');
        $user = $this->makeUserWithExpiredMembership();

        $response = $this->actingAs($user)->get(route('hub.lepis.bulletins.download', $bulletin));

        $response->assertForbidden();
    }

    public function test_lepis_editor_downloads_draft_bulletin(): void
    {
        Storage::fake('public');
        $bulletin = $this->makeBulletinWithPdf('draft');
        $editor = User::factory()->create();
        $editor->grantCapability(EditorialCapability::LEPIS_EDITOR);
        $editor = $editor->fresh();

        $response = $this->actingAs($editor)->get(route('hub.lepis.bulletins.download', $bulletin));

        $response->assertOk();
    }

    private function makeBulletinWithPdf(string $status): LepisBulletin
    {
        $file = UploadedFile::fake()->create('test.pdf', 10, 'application/pdf');
        $path = $file->store('lepis', 'public');

        return LepisBulletin::create([
            'title' => 'T',
            'issue_number' => 1,
            'quarter' => 'Q1',
            'year' => 2026,
            'pdf_path' => $path,
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
