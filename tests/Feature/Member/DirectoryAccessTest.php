<?php

namespace Tests\Feature\Member;

use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectoryAccessTest extends TestCase
{
    use RefreshDatabase;

    private function makeCurrentUser(): User
    {
        $user = User::factory()->create();
        $member = Member::create([
            'user_id' => $user->id, 'member_number' => 'MX', 'email' => $user->email,
            'first_name' => 'F', 'last_name' => 'L', 'joined_at' => now(),
        ]);
        $type = MembershipType::firstOrCreate(
            ['slug' => 'std'],
            ['name' => 'Std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );
        Membership::create([
            'member_id' => $member->id, 'membership_type_id' => $type->id,
            'status' => 'active', 'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);
        return $user;
    }

    public function test_guest_redirected_to_login(): void
    {
        $this->get('/espace-membre/annuaire')->assertRedirect('/connexion');
    }

    public function test_user_without_member_record_redirected(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get('/espace-membre/annuaire')
            ->assertRedirect(route('member.dashboard'));
    }

    public function test_member_not_current_redirected_with_error(): void
    {
        $user = User::factory()->create();
        Member::create([
            'user_id' => $user->id, 'member_number' => 'MEX', 'email' => $user->email,
            'first_name' => 'F', 'last_name' => 'L', 'joined_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/espace-membre/annuaire')
            ->assertRedirect(route('member.dashboard'))
            ->assertSessionHas('error');
    }

    public function test_current_member_can_access_directory_index(): void
    {
        $user = $this->makeCurrentUser();
        $this->actingAs($user)
            ->get('/espace-membre/annuaire')
            ->assertOk()
            ->assertSee('Annuaire des adhérents');
    }

    public function test_old_carte_route_redirects_to_annuaire(): void
    {
        $user = $this->makeCurrentUser();
        $this->actingAs($user)
            ->get('/espace-membre/carte')
            ->assertRedirect('/espace-membre/annuaire');
    }
}
