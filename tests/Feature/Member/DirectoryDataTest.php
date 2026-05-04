<?php

namespace Tests\Feature\Member;

use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectoryDataTest extends TestCase
{
    use RefreshDatabase;

    private User $authUser;
    private Member $authMember;

    protected function setUp(): void
    {
        parent::setUp();

        $type = MembershipType::firstOrCreate(
            ['slug' => 'std'],
            ['name' => 'Std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );

        $this->authUser = User::factory()->create();
        $this->authMember = Member::create([
            'user_id' => $this->authUser->id, 'member_number' => 'MS', 'email' => $this->authUser->email,
            'first_name' => 'Self', 'last_name' => 'CALLER', 'joined_at' => now(),
            'directory_opt_in' => true, // l'auth member est aussi dans l'annuaire
            'directory_groups' => ['rhopalo'],
            'postal_code' => '75001',
        ]);
        Membership::create([
            'member_id' => $this->authMember->id, 'membership_type_id' => $type->id,
            'status' => 'active', 'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);
    }

    private function makeOptInOther(array $attrs = []): Member
    {
        $type = MembershipType::firstOrCreate(['slug' => 'std'], []);
        $u = User::factory()->create();
        $m = Member::create(array_merge([
            'user_id' => $u->id, 'member_number' => 'M' . uniqid(), 'email' => $u->email,
            'first_name' => 'Other', 'last_name' => 'X', 'joined_at' => now(),
            'directory_opt_in' => true,
            'postal_code' => '34170',
        ], $attrs));
        Membership::create([
            'member_id' => $m->id, 'membership_type_id' => $type->id,
            'status' => 'active', 'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);
        return $m;
    }

    public function test_data_excludes_self(): void
    {
        $other = $this->makeOptInOther();

        $response = $this->actingAs($this->authUser)->getJson('/espace-membre/annuaire/data');

        $response->assertOk();
        $ids = collect($response->json('members'))->pluck('id')->all();
        $this->assertContains($other->id, $ids);
        $this->assertNotContains($this->authMember->id, $ids);
    }

    public function test_data_excludes_non_opt_in(): void
    {
        $optedOut = $this->makeOptInOther(['directory_opt_in' => false]);

        $response = $this->actingAs($this->authUser)->getJson('/espace-membre/annuaire/data');

        $ids = collect($response->json('members'))->pluck('id')->all();
        $this->assertNotContains($optedOut->id, $ids);
    }

    public function test_data_excludes_non_current_members(): void
    {
        $u = User::factory()->create();
        $m = Member::create([
            'user_id' => $u->id, 'member_number' => 'MEX', 'email' => $u->email,
            'first_name' => 'Expired', 'last_name' => 'X', 'joined_at' => now(),
            'directory_opt_in' => true, 'postal_code' => '34170',
        ]);
        // Pas d'adhésion → non current

        $response = $this->actingAs($this->authUser)->getJson('/espace-membre/annuaire/data');

        $ids = collect($response->json('members'))->pluck('id')->all();
        $this->assertNotContains($m->id, $ids);
    }

    public function test_filter_by_dept(): void
    {
        $a = $this->makeOptInOther(['postal_code' => '34170', 'first_name' => 'A']);
        $b = $this->makeOptInOther(['postal_code' => '75001', 'first_name' => 'B']);

        $response = $this->actingAs($this->authUser)->getJson('/espace-membre/annuaire/data?dept=34');

        $ids = collect($response->json('members'))->pluck('id')->all();
        $this->assertSame([$a->id], $ids);
    }

    public function test_filter_by_groups(): void
    {
        $a = $this->makeOptInOther(['directory_groups' => ['zygenes']]);
        $b = $this->makeOptInOther(['directory_groups' => ['micro']]);

        $response = $this->actingAs($this->authUser)->getJson('/espace-membre/annuaire/data?groups=zygenes');

        $ids = collect($response->json('members'))->pluck('id')->all();
        $this->assertSame([$a->id], $ids);
    }

    public function test_filter_by_search_q(): void
    {
        $a = $this->makeOptInOther(['first_name' => 'Marie', 'last_name' => 'DUPONT']);
        $b = $this->makeOptInOther(['first_name' => 'Jean', 'last_name' => 'MARTIN']);

        $response = $this->actingAs($this->authUser)->getJson('/espace-membre/annuaire/data?q=dupont');

        $ids = collect($response->json('members'))->pluck('id')->all();
        $this->assertSame([$a->id], $ids);
    }

    public function test_phone_returned_only_when_visible_and_set(): void
    {
        $withPhone = $this->makeOptInOther([
            'first_name' => 'WithPhone', 'mobile' => '0612345678',
            'directory_phone_visible' => true,
        ]);
        $withoutVisible = $this->makeOptInOther([
            'first_name' => 'Hidden', 'mobile' => '0699999999',
            'directory_phone_visible' => false,
        ]);

        $response = $this->actingAs($this->authUser)->getJson('/espace-membre/annuaire/data');

        $rows = collect($response->json('members'));
        $withPhoneRow = $rows->firstWhere('id', $withPhone->id);
        $hiddenRow = $rows->firstWhere('id', $withoutVisible->id);

        $this->assertSame('0612345678', $withPhoneRow['phone']);
        $this->assertNull($hiddenRow['phone']);
    }
}
