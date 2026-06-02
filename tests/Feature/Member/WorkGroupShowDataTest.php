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

class WorkGroupShowDataTest extends TestCase
{
    use RefreshDatabase;

    private function activeMemberUser(): array
    {
        $type = MembershipType::firstOrCreate(
            ['slug' => 'std'],
            ['name' => 'Std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );
        $u = User::factory()->create();
        $m = Member::create([
            'user_id' => $u->id, 'member_number' => 'M'.uniqid(), 'email' => $u->email,
            'first_name' => 'David', 'last_name' => 'DEMERGES', 'joined_at' => now(), 'is_active' => true,
        ]);
        Membership::create([
            'member_id' => $m->id, 'membership_type_id' => $type->id, 'status' => 'active',
            'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);
        return [$u, $m];
    }

    public function test_show_exposes_dashboard_data(): void
    {
        [$u, $m] = $this->activeMemberUser();
        $wg = WorkGroup::create([
            'name' => 'Atlas Grand Est', 'is_active' => true, 'has_resources' => true,
            'has_collaborative_space' => true, 'collaborative_space_url' => 'https://drive.example/atlas',
            'website_url' => 'https://atlas.example',
        ]);
        $wg->members()->attach($m->id, ['role' => 'coordinator', 'status' => 'active', 'joined_at' => now()->subDays(3)]);
        $wg->events()->create([
            'title' => 'Point etape', 'slug' => 'point-etape',
            'start_date' => now()->addDays(10), 'status' => 'published',
            'visibility' => Event::VIS_GROUP, 'organizer_id' => $u->id,
        ]);
        $wg->resources()->create(['type' => 'file', 'category' => 'documents_cadre', 'title' => 'Doc 1']);
        $wg->resources()->create(['type' => 'link', 'category' => 'bibliographie', 'title' => 'Bib 1', 'external_url' => 'https://x.example']);

        $resp = $this->actingAs($u)->get(route('member.work-groups.show', $wg));

        $resp->assertOk();
        $resp->assertViewHas('resourceCounts', function ($counts) {
            return is_array($counts)
                && ($counts['documents_cadre'] ?? null) === 1
                && ($counts['bibliographie'] ?? null) === 1
                && array_key_exists('outils', $counts);
        });
        $resp->assertViewHas('resourceTotal', 2);
        $resp->assertViewHas('nextEvent', fn ($e) => $e !== null && $e->title === 'Point etape');
        $resp->assertViewHas('latestMembers', fn ($c) => $c->count() === 1);
        $resp->assertViewHas('quickLinks', function ($links) {
            $labels = collect($links)->pluck('label')->all();
            return in_array('Drive collaboratif', $labels, true)
                && in_array('Site web', $labels, true);
        });
    }
}
