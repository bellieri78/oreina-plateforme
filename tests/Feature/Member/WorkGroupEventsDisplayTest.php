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

class WorkGroupEventsDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_page_shows_upcoming_meeting(): void
    {
        $type = MembershipType::firstOrCreate(
            ['slug' => 'std'],
            ['name' => 'Std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );
        $u = User::factory()->create();
        $m = Member::create([
            'user_id' => $u->id, 'member_number' => 'MD', 'email' => $u->email,
            'first_name' => 'D', 'last_name' => 'E', 'joined_at' => now(), 'is_active' => true,
        ]);
        Membership::create([
            'member_id' => $m->id, 'membership_type_id' => $type->id, 'status' => 'active',
            'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);
        $wg = WorkGroup::create(['name' => 'GT Reunion', 'is_active' => true]);
        $wg->members()->attach($m->id, ['role' => 'member', 'status' => 'active', 'joined_at' => now()]);
        $wg->events()->create([
            'title' => 'Reunion de cadrage', 'slug' => 'reunion-cadrage',
            'start_date' => now()->addDays(5), 'status' => 'published',
            'visibility' => Event::VIS_GROUP, 'organizer_id' => $u->id,
        ]);

        $this->actingAs($u)
            ->get(route('member.work-groups.show', $wg))
            ->assertOk()
            ->assertSee('Reunion de cadrage');
    }
}
