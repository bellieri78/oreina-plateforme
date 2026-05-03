<?php

namespace Tests\Unit\Services;

use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use App\Services\MemberDirectoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberDirectoryServiceTest extends TestCase
{
    use RefreshDatabase;

    private MemberDirectoryService $service;
    private Member $excluding;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MemberDirectoryService();
        $this->excluding = $this->makeMember(['first_name' => 'Self', 'last_name' => 'EXCLUDING']);
    }

    private function makeMember(array $attrs = []): Member
    {
        $user = User::factory()->create();
        return Member::create(array_merge([
            'user_id' => $user->id,
            'member_number' => 'M' . uniqid(),
            'email' => $user->email,
            'first_name' => 'X', 'last_name' => 'Y',
            'joined_at' => now(),
        ], $attrs));
    }

    private function makeOptInMember(array $attrs = []): Member
    {
        $member = $this->makeMember(array_merge(['directory_opt_in' => true], $attrs));
        $type = MembershipType::firstOrCreate(
            ['slug' => 'std'],
            ['name' => 'Std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );
        Membership::create([
            'member_id' => $member->id, 'membership_type_id' => $type->id,
            'status' => 'active', 'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);
        return $member;
    }

    public function test_filter_excludes_self(): void
    {
        $other = $this->makeOptInMember(['postal_code' => '75001', 'first_name' => 'Other']);

        $results = $this->service->filter([], $this->excluding);

        $this->assertSame([$other->id], $results->pluck('id')->all());
    }

    public function test_filter_by_single_department(): void
    {
        $a = $this->makeOptInMember(['postal_code' => '34170', 'first_name' => 'A']);
        $b = $this->makeOptInMember(['postal_code' => '75001', 'first_name' => 'B']);

        $results = $this->service->filter(['departments' => ['34']], $this->excluding);

        $this->assertSame([$a->id], $results->pluck('id')->all());
    }

    public function test_filter_by_multiple_departments(): void
    {
        $a = $this->makeOptInMember(['postal_code' => '34170', 'first_name' => 'A']);
        $b = $this->makeOptInMember(['postal_code' => '75001', 'first_name' => 'B']);
        $c = $this->makeOptInMember(['postal_code' => '69001', 'first_name' => 'C']);

        $results = $this->service->filter(['departments' => ['34', '75']], $this->excluding);

        $this->assertEqualsCanonicalizing([$a->id, $b->id], $results->pluck('id')->all());
    }

    public function test_filter_by_groups_uses_jsonb_contains(): void
    {
        $a = $this->makeOptInMember(['directory_groups' => ['rhopalo', 'zygenes']]);
        $b = $this->makeOptInMember(['directory_groups' => ['micro']]);
        $c = $this->makeOptInMember(['directory_groups' => ['zygenes', 'macro']]);

        $results = $this->service->filter(['groups' => ['zygenes']], $this->excluding);

        $this->assertEqualsCanonicalizing([$a->id, $c->id], $results->pluck('id')->all());
    }

    public function test_filter_by_search_matches_first_or_last_name_case_insensitive(): void
    {
        $a = $this->makeOptInMember(['first_name' => 'Marie', 'last_name' => 'DUPONT']);
        $b = $this->makeOptInMember(['first_name' => 'Jean', 'last_name' => 'MARTIN']);

        $results = $this->service->filter(['q' => 'dup'], $this->excluding);
        $this->assertSame([$a->id], $results->pluck('id')->all());

        $results = $this->service->filter(['q' => 'MAR'], $this->excluding);
        $this->assertEqualsCanonicalizing([$a->id, $b->id], $results->pluck('id')->all());
    }

    public function test_to_json_row_includes_phone_only_when_visible(): void
    {
        $member = $this->makeOptInMember([
            'first_name' => 'Marie', 'last_name' => 'DUPONT',
            'mobile' => '0612345678', 'postal_code' => '34170',
            'directory_phone_visible' => true,
            'directory_groups' => ['rhopalo'],
        ]);

        $row = $this->service->toJsonRow($member);

        $this->assertSame('Marie', $row['first_name']);
        $this->assertSame('DUPONT', $row['last_name']);
        $this->assertSame('34', $row['department']);
        $this->assertSame('0612345678', $row['phone']);
        $this->assertSame(['rhopalo'], $row['groups']);
    }

    public function test_to_json_row_omits_phone_when_not_visible(): void
    {
        $member = $this->makeOptInMember([
            'mobile' => '0612345678',
            'directory_phone_visible' => false,
        ]);

        $row = $this->service->toJsonRow($member);

        $this->assertNull($row['phone']);
    }

    public function test_to_json_row_omits_phone_when_visible_but_mobile_empty(): void
    {
        $member = $this->makeOptInMember([
            'mobile' => null,
            'directory_phone_visible' => true,
        ]);

        $row = $this->service->toJsonRow($member);

        $this->assertNull($row['phone']);
    }
}
