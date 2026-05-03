<?php

namespace Tests\Unit\Models;

use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberDirectoryTest extends TestCase
{
    use RefreshDatabase;

    private function makeMember(array $attrs = []): Member
    {
        $user = User::factory()->create();
        return Member::create(array_merge([
            'user_id' => $user->id,
            'member_number' => 'M' . uniqid(),
            'email' => $user->email,
            'first_name' => 'Test',
            'last_name' => 'USER',
            'joined_at' => now(),
        ], $attrs));
    }

    private function attachCurrentMembership(Member $member): void
    {
        $type = MembershipType::firstOrCreate(
            ['slug' => 'standard'],
            ['name' => 'Standard', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );
        Membership::create([
            'member_id' => $member->id,
            'membership_type_id' => $type->id,
            'status' => 'active',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
            'amount_paid' => 30,
            'lepis_format' => 'paper',
        ]);
    }

    public function test_directory_groups_constant_lists_four_groups(): void
    {
        $this->assertSame(['rhopalo', 'micro', 'macro', 'zygenes'], array_keys(Member::DIRECTORY_GROUPS));
    }

    public function test_directory_groups_cast_to_array(): void
    {
        $member = $this->makeMember(['directory_groups' => ['rhopalo', 'zygenes']]);
        $member->refresh();
        $this->assertSame(['rhopalo', 'zygenes'], $member->directory_groups);
    }

    public function test_in_directory_scope_returns_only_opt_in_current_members(): void
    {
        $optInCurrent = $this->makeMember(['directory_opt_in' => true, 'postal_code' => '75001']);
        $this->attachCurrentMembership($optInCurrent);

        $optInExpired = $this->makeMember(['directory_opt_in' => true, 'postal_code' => '34000']);

        $notOptInCurrent = $this->makeMember(['directory_opt_in' => false, 'postal_code' => '69001']);
        $this->attachCurrentMembership($notOptInCurrent);

        $ids = Member::inDirectory()->pluck('id')->all();

        $this->assertContains($optInCurrent->id, $ids);
        $this->assertNotContains($optInExpired->id, $ids);
        $this->assertNotContains($notOptInCurrent->id, $ids);
    }

    public function test_is_in_directory_combines_opt_in_and_current_membership(): void
    {
        $member = $this->makeMember(['directory_opt_in' => true]);
        $this->attachCurrentMembership($member);

        $this->assertTrue($member->isInDirectory());

        $member->update(['directory_opt_in' => false]);
        $this->assertFalse($member->isInDirectory());
    }

    public function test_directory_department_extracts_first_two_chars_of_postal_code(): void
    {
        $member = $this->makeMember(['postal_code' => '34170']);
        $this->assertSame('34', $member->directoryDepartment());

        $member->postal_code = null;
        $this->assertNull($member->directoryDepartment());
    }

    public function test_set_rgpd_consent_directory_to_true_updates_opt_in_at_and_source(): void
    {
        $member = $this->makeMember();

        $member->setRgpdConsent(\App\Models\RgpdConsentHistory::TYPE_DIRECTORY, true, 'member_portal');

        $member->refresh();
        $this->assertTrue($member->directory_opt_in);
        $this->assertNotNull($member->directory_opt_in_at);
        $this->assertSame('member_portal', $member->directory_opt_in_source);
    }

    public function test_set_rgpd_consent_directory_to_false_keeps_groups_and_phone_visible(): void
    {
        $member = $this->makeMember([
            'directory_opt_in' => true,
            'directory_phone_visible' => true,
            'directory_groups' => ['zygenes'],
            'directory_opt_in_at' => now()->subDay(),
            'directory_opt_in_source' => 'member_portal',
        ]);

        $member->setRgpdConsent(\App\Models\RgpdConsentHistory::TYPE_DIRECTORY, false, 'member_portal');

        $member->refresh();
        $this->assertFalse($member->directory_opt_in);
        $this->assertTrue($member->directory_phone_visible, 'phone_visible doit être conservé');
        $this->assertSame(['zygenes'], $member->directory_groups, 'groupes doivent être conservés');
    }

    public function test_set_rgpd_consent_directory_creates_history_entry(): void
    {
        $member = $this->makeMember();

        $member->setRgpdConsent(\App\Models\RgpdConsentHistory::TYPE_DIRECTORY, true, 'questionnaire');

        $this->assertDatabaseHas('rgpd_consent_history', [
            'member_id' => $member->id,
            'consent_type' => 'directory',
            'value' => true,
            'source' => 'questionnaire',
        ]);
    }
}
