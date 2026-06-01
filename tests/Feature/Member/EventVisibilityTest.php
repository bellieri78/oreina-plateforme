<?php

namespace Tests\Feature\Member;

use App\Models\Event;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use App\Models\WorkGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function currentMember(array $attrs = []): Member
    {
        $type = MembershipType::firstOrCreate(
            ['slug' => 'std'],
            ['name' => 'Std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );
        $u = User::factory()->create();
        $m = Member::create(array_merge([
            'user_id' => $u->id, 'member_number' => 'M'.uniqid(), 'email' => $u->email,
            'first_name' => 'A', 'last_name' => 'B', 'joined_at' => now(), 'is_active' => true,
        ], $attrs));
        Membership::create([
            'member_id' => $m->id, 'membership_type_id' => $type->id, 'status' => 'active',
            'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);
        return $m;
    }

    private function event(array $attrs = []): Event
    {
        return Event::create(array_merge([
            'title' => 'E'.uniqid(), 'slug' => 'e'.uniqid(),
            'start_date' => now()->addWeek(), 'status' => 'published', 'visibility' => Event::VIS_PUBLIC,
        ], $attrs));
    }

    public function test_simple_member_sees_public_and_members_only(): void
    {
        $m = $this->currentMember();
        $pub = $this->event(['visibility' => Event::VIS_PUBLIC]);
        $mem = $this->event(['visibility' => Event::VIS_MEMBERS]);
        $res = $this->event(['visibility' => Event::VIS_RESTRICTED, 'audience_roles' => ['ca']]);

        $ids = Event::visibleToMember($m)->pluck('id');

        $this->assertTrue($ids->contains($pub->id));
        $this->assertTrue($ids->contains($mem->id));
        $this->assertFalse($ids->contains($res->id));
    }

    public function test_bureau_sees_ca_restricted_event(): void
    {
        $m = $this->currentMember(['adherent_roles' => ['bureau']]);
        $caEvent = $this->event(['visibility' => Event::VIS_RESTRICTED, 'audience_roles' => ['ca']]);
        $valEvent = $this->event(['visibility' => Event::VIS_RESTRICTED, 'audience_roles' => ['validateur']]);

        $ids = Event::visibleToMember($m)->pluck('id');

        $this->assertTrue($ids->contains($caEvent->id));
        $this->assertFalse($ids->contains($valEvent->id));
    }

    public function test_group_event_visible_only_to_active_group_member(): void
    {
        $member = $this->currentMember();
        $outsider = $this->currentMember();
        $wg = WorkGroup::create(['name' => 'GT Zyg', 'is_active' => true]);
        $wg->members()->attach($member->id, ['role' => 'member', 'status' => 'active', 'joined_at' => now()]);

        $ev = $this->event(['visibility' => Event::VIS_GROUP, 'work_group_id' => $wg->id]);

        $this->assertTrue(Event::visibleToMember($member)->pluck('id')->contains($ev->id));
        $this->assertFalse(Event::visibleToMember($outsider)->pluck('id')->contains($ev->id));
    }

    public function test_public_only_scope_excludes_non_public(): void
    {
        $this->event(['visibility' => Event::VIS_PUBLIC]);
        $this->event(['visibility' => Event::VIS_MEMBERS]);

        $this->assertSame(1, Event::publicOnly()->count());
    }
}
