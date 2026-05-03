<?php

namespace Tests\Feature\Middleware;

use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class EnsureCurrentMemberTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Route de test isolée
        Route::middleware(['web', 'auth', 'current_member'])
            ->get('/_test/current-member', fn () => response('OK', 200));
    }

    public function test_redirects_unauthenticated_to_login(): void
    {
        $this->get('/_test/current-member')->assertRedirect('/connexion');
    }

    public function test_redirects_user_without_member_record_to_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get('/_test/current-member')
            ->assertRedirect(route('member.dashboard'))
            ->assertSessionHas('error');
    }

    public function test_redirects_member_with_expired_membership(): void
    {
        $user = User::factory()->create();
        $member = Member::create([
            'user_id' => $user->id, 'member_number' => 'M1', 'email' => $user->email,
            'first_name' => 'F', 'last_name' => 'L', 'joined_at' => now(),
        ]);
        $type = MembershipType::create(['name' => 'Std', 'slug' => 'std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]);
        Membership::create([
            'member_id' => $member->id, 'membership_type_id' => $type->id,
            'status' => 'active', 'start_date' => now()->subYear(), 'end_date' => now()->subMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);

        $this->actingAs($user)
            ->get('/_test/current-member')
            ->assertRedirect(route('member.dashboard'));
    }

    public function test_allows_member_with_active_current_membership(): void
    {
        $user = User::factory()->create();
        $member = Member::create([
            'user_id' => $user->id, 'member_number' => 'M2', 'email' => $user->email,
            'first_name' => 'F', 'last_name' => 'L', 'joined_at' => now(),
        ]);
        $type = MembershipType::create(['name' => 'Std', 'slug' => 'std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]);
        Membership::create([
            'member_id' => $member->id, 'membership_type_id' => $type->id,
            'status' => 'active', 'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);

        $this->actingAs($user)
            ->get('/_test/current-member')
            ->assertOk()
            ->assertSee('OK');
    }
}
