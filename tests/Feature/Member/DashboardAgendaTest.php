<?php

namespace Tests\Feature\Member;

use App\Models\Event;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAgendaTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Member $member;

    protected function setUp(): void
    {
        parent::setUp();
        $type = MembershipType::firstOrCreate(
            ['slug' => 'std'],
            ['name' => 'Std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );
        $this->user = User::factory()->create();
        $this->member = Member::create([
            'user_id' => $this->user->id, 'member_number' => 'MA', 'email' => $this->user->email,
            'first_name' => 'Ada', 'last_name' => 'L', 'joined_at' => now(), 'is_active' => true,
        ]);
        Membership::create([
            'member_id' => $this->member->id, 'membership_type_id' => $type->id, 'status' => 'active',
            'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);
    }

    public function test_member_dashboard_shows_members_event_but_not_restricted(): void
    {
        Event::create(['title' => 'Atelier adherents', 'slug' => 'atelier-adh',
            'start_date' => now()->addDays(3), 'status' => 'published', 'visibility' => Event::VIS_MEMBERS]);
        Event::create(['title' => 'Reunion CA secrete', 'slug' => 'ca-secret',
            'start_date' => now()->addDays(4), 'status' => 'published',
            'visibility' => Event::VIS_RESTRICTED, 'audience_roles' => ['ca']]);

        $this->actingAs($this->user)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee('Atelier adherents')
            ->assertDontSee('Reunion CA secrete');
    }
}
