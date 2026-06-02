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

class WorkGroupShowDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function makeActiveMember(string $first = 'David', string $last = 'DEMERGES'): array
    {
        $type = MembershipType::firstOrCreate(
            ['slug' => 'std'],
            ['name' => 'Std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );
        $u = User::factory()->create();
        $m = Member::create([
            'user_id' => $u->id, 'member_number' => 'M'.uniqid(), 'email' => $u->email,
            'first_name' => $first, 'last_name' => $last, 'joined_at' => now(), 'is_active' => true,
        ]);
        Membership::create([
            'member_id' => $m->id, 'membership_type_id' => $type->id, 'status' => 'active',
            'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);
        return [$u, $m];
    }

    public function test_dashboard_renders_core_sections_for_active_member(): void
    {
        [$u, $m] = $this->makeActiveMember();
        $wg = WorkGroup::create(['name' => 'Atlas Grand Est', 'is_active' => true, 'has_forum' => true]);
        $wg->members()->attach($m->id, ['role' => 'coordinator', 'status' => 'active', 'joined_at' => now()->subDays(2)]);

        $this->actingAs($u)
            ->get(route('member.work-groups.show', $wg))
            ->assertOk()
            ->assertSee('Atlas Grand Est')
            ->assertSee('À propos de ce groupe', false)
            ->assertSee('Activité du groupe', false)
            ->assertSee('Groupes de travail');
    }

    public function test_next_meeting_card_shown_when_event_exists(): void
    {
        [$u, $m] = $this->makeActiveMember();
        $wg = WorkGroup::create(['name' => 'GT Reunion', 'is_active' => true]);
        $wg->members()->attach($m->id, ['role' => 'member', 'status' => 'active', 'joined_at' => now()]);
        $wg->events()->create([
            'title' => 'Point d etape Atlas', 'slug' => 'point-etape-atlas',
            'start_date' => now()->addDays(7), 'status' => 'published',
            'visibility' => Event::VIS_GROUP, 'organizer_id' => $u->id,
        ]);

        $this->actingAs($u)
            ->get(route('member.work-groups.show', $wg))
            ->assertOk()
            ->assertSee('Prochaine réunion', false)
            ->assertSee('Point d etape Atlas');
    }

    public function test_next_meeting_card_hidden_when_no_event(): void
    {
        [$u, $m] = $this->makeActiveMember();
        $wg = WorkGroup::create(['name' => 'GT Sans Reunion', 'is_active' => true]);
        $wg->members()->attach($m->id, ['role' => 'member', 'status' => 'active', 'joined_at' => now()]);

        $this->actingAs($u)
            ->get(route('member.work-groups.show', $wg))
            ->assertOk()
            ->assertDontSee('Prochaine réunion', false);
    }

    public function test_coordinator_sees_plan_meeting_cta_when_no_event(): void
    {
        [$u, $m] = $this->makeActiveMember();
        $wg = WorkGroup::create(['name' => 'GT Coord Sans Reunion', 'is_active' => true]);
        $wg->members()->attach($m->id, ['role' => 'coordinator', 'status' => 'active', 'joined_at' => now()]);

        $this->actingAs($u)
            ->get(route('member.work-groups.show', $wg))
            ->assertOk()
            ->assertSee('Planifier une réunion', false);
    }

    public function test_quick_links_card_shown_when_drive_url_set(): void
    {
        [$u, $m] = $this->makeActiveMember();
        $wg = WorkGroup::create([
            'name' => 'GT Drive', 'is_active' => true,
            'has_collaborative_space' => true, 'collaborative_space_url' => 'https://drive.example/x',
        ]);
        $wg->members()->attach($m->id, ['role' => 'member', 'status' => 'active', 'joined_at' => now()]);

        $this->actingAs($u)
            ->get(route('member.work-groups.show', $wg))
            ->assertOk()
            ->assertSee('Liens rapides')
            ->assertSee('Drive collaboratif');
    }

    public function test_quick_links_card_hidden_when_no_url(): void
    {
        [$u, $m] = $this->makeActiveMember();
        $wg = WorkGroup::create(['name' => 'GT No Links', 'is_active' => true]);
        $wg->members()->attach($m->id, ['role' => 'member', 'status' => 'active', 'joined_at' => now()]);

        $this->actingAs($u)
            ->get(route('member.work-groups.show', $wg))
            ->assertOk()
            ->assertDontSee('Liens rapides');
    }

    public function test_resources_card_hidden_for_preview_non_member(): void
    {
        // Adhérent à jour mais NON membre du groupe => aperçu sans la carte ressources
        [$viewerUser] = $this->makeActiveMember('Visiteur', 'CURIEUX');
        $wg = WorkGroup::create([
            'name' => 'GT Prive', 'is_active' => true, 'has_resources' => true, 'join_policy' => 'request',
        ]);

        $this->actingAs($viewerUser)
            ->get(route('member.work-groups.show', $wg))
            ->assertOk()
            ->assertDontSee('Ressources du groupe');
    }
}
