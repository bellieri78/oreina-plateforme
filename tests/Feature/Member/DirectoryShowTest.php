<?php

namespace Tests\Feature\Member;

use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectoryShowTest extends TestCase
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
            'first_name' => 'Self', 'last_name' => 'X', 'joined_at' => now(),
        ]);
        Membership::create([
            'member_id' => $this->authMember->id, 'membership_type_id' => $type->id,
            'status' => 'active', 'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);
    }

    private function makeOther(array $attrs = []): Member
    {
        $type = MembershipType::firstOrCreate(['slug' => 'std'], []);
        $u = User::factory()->create();
        $m = Member::create(array_merge([
            'user_id' => $u->id, 'member_number' => 'M' . uniqid(), 'email' => $u->email,
            'first_name' => 'Other', 'last_name' => 'PERSON', 'joined_at' => now(),
            'postal_code' => '34170',
            'directory_opt_in' => true,
            'directory_groups' => ['rhopalo'],
        ], $attrs));
        Membership::create([
            'member_id' => $m->id, 'membership_type_id' => $type->id,
            'status' => 'active', 'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);
        return $m;
    }

    public function test_show_returns_partial_for_opt_in_current_member(): void
    {
        $other = $this->makeOther(['first_name' => 'Marie', 'last_name' => 'DUPONT']);
        $other->update(['email' => 'marie.dupont@example.com']);

        $this->actingAs($this->authUser)
            ->get(route('member.directory.show', $other))
            ->assertOk()
            ->assertSeeText('Marie DUPONT')
            ->assertSee('marie.dupont@example.com');
    }

    public function test_show_returns_404_for_non_opt_in_member(): void
    {
        $other = $this->makeOther(['directory_opt_in' => false]);

        $this->actingAs($this->authUser)
            ->get(route('member.directory.show', $other))
            ->assertNotFound();
    }

    public function test_show_returns_404_when_member_not_current(): void
    {
        // Member opt-in mais pas d'adhésion à jour
        $u = User::factory()->create();
        $other = Member::create([
            'user_id' => $u->id, 'member_number' => 'MEX', 'email' => $u->email,
            'first_name' => 'X', 'last_name' => 'Y', 'joined_at' => now(),
            'directory_opt_in' => true, 'postal_code' => '34170',
        ]);

        $this->actingAs($this->authUser)
            ->get(route('member.directory.show', $other))
            ->assertNotFound();
    }

    public function test_show_returns_404_for_self(): void
    {
        $this->authMember->update(['directory_opt_in' => true, 'postal_code' => '75001']);
        $this->actingAs($this->authUser)
            ->get(route('member.directory.show', $this->authMember))
            ->assertNotFound();
    }

    public function test_show_displays_phone_when_visible(): void
    {
        $other = $this->makeOther(['mobile' => '0612345678', 'directory_phone_visible' => true]);

        $this->actingAs($this->authUser)
            ->get(route('member.directory.show', $other))
            ->assertOk()
            ->assertSee('0612345678');
    }

    public function test_show_omits_phone_when_visible_false(): void
    {
        $other = $this->makeOther(['mobile' => '0699999999', 'directory_phone_visible' => false]);

        $this->actingAs($this->authUser)
            ->get(route('member.directory.show', $other))
            ->assertOk()
            ->assertDontSee('0699999999')
            ->assertSee($other->email);
    }
}
