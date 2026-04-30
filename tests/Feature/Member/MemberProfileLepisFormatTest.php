<?php

namespace Tests\Feature\Member;

use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberProfileLepisFormatTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_shows_paper_format_when_active_membership_is_paper(): void
    {
        $user = User::factory()->create();
        $member = Member::create([
            'user_id' => $user->id, 'member_number' => 'M1', 'email' => $user->email,
            'first_name' => 'F', 'last_name' => 'L', 'joined_at' => now(),
        ]);
        MembershipType::create(['name' => 'S', 'slug' => 's', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]);
        Membership::create([
            'member_id' => $member->id,
            'membership_type_id' => MembershipType::first()->id,
            'status' => 'active',
            'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);

        $response = $this->actingAs($user)->get('/espace-membre/profil');

        $response->assertOk()->assertSee('Papier');
    }

    public function test_profile_shows_digital_format_when_active_membership_is_digital(): void
    {
        $user = User::factory()->create();
        $member = Member::create([
            'user_id' => $user->id, 'member_number' => 'M2', 'email' => $user->email,
            'first_name' => 'F', 'last_name' => 'L', 'joined_at' => now(),
        ]);
        MembershipType::create(['name' => 'S', 'slug' => 's', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]);
        Membership::create([
            'member_id' => $member->id,
            'membership_type_id' => MembershipType::first()->id,
            'status' => 'active',
            'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'digital',
        ]);

        $response = $this->actingAs($user)->get('/espace-membre/profil');

        $response->assertOk()->assertSee('Numerique');
    }
}
