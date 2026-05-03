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
}
