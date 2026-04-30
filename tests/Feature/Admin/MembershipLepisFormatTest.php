<?php

namespace Tests\Feature\Admin;

use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipLepisFormatTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_requires_lepis_format(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();
        MembershipType::create([
            'name' => 'Standard', 'slug' => 'standard', 'price' => 30,
            'duration_months' => 12, 'is_active' => true, 'sort_order' => 1,
        ]);

        $response = $this->actingAs($admin)->post('/extranet/memberships', [
            'member_id' => $member->id,
            'amount_paid' => 30,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addYear()->format('Y-m-d'),
            // lepis_format intentionally omitted
        ]);

        $response->assertSessionHasErrors('lepis_format');
    }

    public function test_store_persists_lepis_format(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();
        $type = MembershipType::create([
            'name' => 'Standard', 'slug' => 'standard', 'price' => 30,
            'duration_months' => 12, 'is_active' => true, 'sort_order' => 1,
        ]);

        $response = $this->actingAs($admin)->post('/extranet/memberships', [
            'member_id' => $member->id,
            'membership_type_id' => $type->id,
            'amount_paid' => 30,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addYear()->format('Y-m-d'),
            'lepis_format' => 'digital',
        ]);

        $response->assertRedirect();
        $m = Membership::where('member_id', $member->id)->latest('id')->first();
        $this->assertSame('digital', $m->lepis_format);
    }

    public function test_index_filters_by_lepis_format(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();
        $type = MembershipType::create([
            'name' => 'Standard', 'slug' => 'standard', 'price' => 30,
            'duration_months' => 12, 'is_active' => true, 'sort_order' => 1,
        ]);
        Membership::create([
            'member_id' => $member->id, 'membership_type_id' => $type->id, 'status' => 'active',
            'start_date' => now(), 'end_date' => now()->addYear(),
            'amount_paid' => 30, 'lepis_format' => 'digital',
        ]);
        Membership::create([
            'member_id' => $member->id, 'membership_type_id' => $type->id, 'status' => 'active',
            'start_date' => now(), 'end_date' => now()->addYear(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);

        $response = $this->actingAs($admin)->get('/extranet/memberships?lepis_format=digital');
        $response->assertOk();
    }

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    private function makeMember(): Member
    {
        $u = User::factory()->create();
        return Member::create([
            'user_id' => $u->id, 'member_number' => 'M' . random_int(1000, 9999),
            'email' => $u->email, 'first_name' => 'F', 'last_name' => 'L',
            'joined_at' => now(),
        ]);
    }
}
