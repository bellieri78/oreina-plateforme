<?php

namespace Tests\Feature\Admin;

use App\Models\Donation;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberShowFicheTest extends TestCase
{
    use RefreshDatabase;

    public function test_kpi_bar_hides_zero_counters(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();
        // Pas de membership, dons, achats, etc.

        $response = $this->actingAs($admin)->get("/extranet/members/{$member->id}");

        // On vérifie les labels KPI qui sont absents de la nav et uniques à la barre
        $response->assertOk()
            ->assertDontSee('Dons cumulés')        // uniquement dans le KPI bar (nav : "Dons")
            ->assertDontSee('Bulletins reçus')     // uniquement dans le KPI bar (nav : "Bulletins Lepis")
            ->assertDontSee('Publications Chersotis') // uniquement dans le KPI bar
            ->assertDontSee('dashboard-stats');    // barre entière absente si tous les KPIs = 0
    }

    public function test_kpi_bar_shows_donation_sum_in_euros(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();
        Donation::create([
            'member_id' => $member->id,
            'donor_email' => $member->email,
            'donor_name' => $member->full_name,
            'amount' => 100,
            'donation_date' => now(),
            'payment_method' => 'helloasso',
        ]);
        Donation::create([
            'member_id' => $member->id,
            'donor_email' => $member->email,
            'donor_name' => $member->full_name,
            'amount' => 230,
            'donation_date' => now()->subMonth(),
            'payment_method' => 'helloasso',
        ]);

        $response = $this->actingAs($admin)->get("/extranet/members/{$member->id}");

        $response->assertOk()
            ->assertSee('Dons cumulés')
            ->assertSee('330'); // sum of donations
    }

    public function test_lepis_format_in_sidebar_when_active_membership(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();
        $this->makeActiveMembership($member, 'paper');

        $response = $this->actingAs($admin)->get("/extranet/members/{$member->id}");

        $response->assertOk()
            ->assertSee('Format Lepis')
            ->assertSee('Papier');
    }

    public function test_sidebar_engagement_block_hidden_when_no_activity(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();
        // No memberships, no submissions, no suggestions, no work groups
        $response = $this->actingAs($admin)->get("/extranet/members/{$member->id}");

        $response->assertOk()
            ->assertDontSee('Auteur Chersotis')
            ->assertDontSee('Contributeur Lepis')
            ->assertDontSee('Format Lepis');  // pas de membership active
    }

    public function test_membership_card_truncates_at_5(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();
        if (! MembershipType::where('slug', 'standard')->exists()) {
            MembershipType::create([
                'name' => 'Standard', 'slug' => 'standard', 'price' => 30,
                'duration_months' => 12, 'is_active' => true, 'sort_order' => 1,
            ]);
        }
        $typeId = MembershipType::where('slug', 'standard')->first()->id;
        // Create 7 memberships
        for ($i = 0; $i < 7; $i++) {
            Membership::create([
                'member_id' => $member->id,
                'membership_type_id' => $typeId,
                'status' => 'active',
                'start_date' => now()->subYears(7 - $i),
                'end_date' => now()->subYears(6 - $i),
                'amount_paid' => 30,
                'lepis_format' => 'paper',
            ]);
        }

        $response = $this->actingAs($admin)->get("/extranet/members/{$member->id}");

        $response->assertOk()
            ->assertSee('Adhésions (7)')
            ->assertSee('Voir tout')
            ->assertSee('<details', escape: false);
    }

    protected function makeAdmin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    protected function makeMember(): Member
    {
        $u = User::factory()->create();
        return Member::create([
            'user_id' => $u->id,
            'member_number' => 'M' . random_int(1000, 99999),
            'email' => $u->email,
            'first_name' => 'F',
            'last_name' => 'L',
            'joined_at' => now(),
        ]);
    }

    protected function makeActiveMembership(Member $member, string $lepisFormat = 'paper'): Membership
    {
        if (! MembershipType::where('slug', 'standard')->exists()) {
            MembershipType::create([
                'name' => 'Standard', 'slug' => 'standard', 'price' => 30,
                'duration_months' => 12, 'is_active' => true, 'sort_order' => 1,
            ]);
        }
        return Membership::create([
            'member_id' => $member->id,
            'membership_type_id' => MembershipType::where('slug', 'standard')->first()->id,
            'status' => 'active',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
            'amount_paid' => 30,
            'lepis_format' => $lepisFormat,
        ]);
    }
}
