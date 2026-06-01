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

class GroupEventInAgendaTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_meeting_appears_in_member_agenda_for_group_member_only(): void
    {
        $type = MembershipType::firstOrCreate(
            ['slug' => 'std'],
            ['name' => 'Std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );

        $make = function (string $num) use ($type) {
            $u = User::factory()->create();
            $m = Member::create([
                'user_id' => $u->id, 'member_number' => $num, 'email' => $u->email,
                'first_name' => 'A', 'last_name' => 'B', 'joined_at' => now(), 'is_active' => true,
            ]);
            Membership::create([
                'member_id' => $m->id, 'membership_type_id' => $type->id, 'status' => 'active',
                'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
                'amount_paid' => 30, 'lepis_format' => 'paper',
            ]);
            return [$u, $m];
        };

        [$uIn, $mIn] = $make('IN');
        [$uOut, $mOut] = $make('OUT');
        $wg = WorkGroup::create(['name' => 'GT X', 'is_active' => true]);
        $wg->members()->attach($mIn->id, ['role' => 'member', 'status' => 'active', 'joined_at' => now()]);
        $wg->events()->create([
            'title' => 'Reunion interne X', 'slug' => 'reunion-interne-x',
            'start_date' => now()->addDays(2), 'status' => 'published',
            'visibility' => Event::VIS_GROUP, 'organizer_id' => $uIn->id,
        ]);

        $this->actingAs($uIn)->get(route('member.dashboard'))->assertSee('Reunion interne X');
        $this->actingAs($uOut)->get(route('member.dashboard'))->assertDontSee('Reunion interne X');
    }
}
