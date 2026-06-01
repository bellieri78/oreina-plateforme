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

class WorkGroupEventTest extends TestCase
{
    use RefreshDatabase;

    private function member(): array
    {
        $type = MembershipType::firstOrCreate(
            ['slug' => 'std'],
            ['name' => 'Std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );
        $u = User::factory()->create();
        $m = Member::create([
            'user_id' => $u->id, 'member_number' => 'M'.uniqid(), 'email' => $u->email,
            'first_name' => 'A', 'last_name' => 'B', 'joined_at' => now(), 'is_active' => true,
        ]);
        Membership::create([
            'member_id' => $m->id, 'membership_type_id' => $type->id, 'status' => 'active',
            'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);
        return [$u, $m];
    }

    public function test_coordinator_can_create_online_group_event(): void
    {
        [$u, $m] = $this->member();
        $wg = WorkGroup::create(['name' => 'GT Micro', 'is_active' => true]);
        $wg->members()->attach($m->id, ['role' => 'coordinator', 'status' => 'active', 'joined_at' => now()]);

        $this->actingAs($u)
            ->post(route('member.work-groups.events.store', $wg), [
                'title' => 'Visio mensuelle',
                'start_date' => now()->addWeek()->format('Y-m-d\TH:i'),
                'mode' => 'online',
                'meeting_url' => 'https://meet.example.org/abc',
            ])->assertRedirect();

        $event = Event::where('title', 'Visio mensuelle')->firstOrFail();
        $this->assertSame(Event::VIS_GROUP, $event->visibility);
        $this->assertSame($wg->id, $event->work_group_id);
        $this->assertSame('https://meet.example.org/abc', $event->meeting_url);
        $this->assertSame('published', $event->status);
    }

    public function test_non_coordinator_cannot_create(): void
    {
        [$u, $m] = $this->member();
        $wg = WorkGroup::create(['name' => 'GT Macro', 'is_active' => true]);
        $wg->members()->attach($m->id, ['role' => 'member', 'status' => 'active', 'joined_at' => now()]);

        $this->actingAs($u)
            ->post(route('member.work-groups.events.store', $wg), [
                'title' => 'Tentative',
                'start_date' => now()->addWeek()->format('Y-m-d\TH:i'),
                'mode' => 'online',
                'meeting_url' => 'https://meet.example.org/x',
            ])->assertForbidden();

        $this->assertDatabaseMissing('events', ['title' => 'Tentative']);
    }

    public function test_online_mode_requires_meeting_url(): void
    {
        [$u, $m] = $this->member();
        $wg = WorkGroup::create(['name' => 'GT Zyg', 'is_active' => true]);
        $wg->members()->attach($m->id, ['role' => 'coordinator', 'status' => 'active', 'joined_at' => now()]);

        $this->actingAs($u)
            ->post(route('member.work-groups.events.store', $wg), [
                'title' => 'Sans lien',
                'start_date' => now()->addWeek()->format('Y-m-d\TH:i'),
                'mode' => 'online',
            ])->assertSessionHasErrors('meeting_url');
    }
}
