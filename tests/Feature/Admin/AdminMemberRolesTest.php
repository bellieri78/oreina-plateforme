<?php

namespace Tests\Feature\Admin;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMemberRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_update_persists_adherent_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $u = User::factory()->create();
        $member = Member::create([
            'user_id' => $u->id, 'member_number' => 'MZ', 'email' => $u->email,
            'first_name' => 'Zoe', 'last_name' => 'M', 'joined_at' => now(), 'is_active' => true,
            'contact_type' => 'individuel',
        ]);

        $payload = [
            'first_name' => 'Zoe',
            'last_name' => 'M',
            'email' => $u->email,
            'contact_type' => 'individuel',
            'adherent_roles' => ['ca', 'validateur'],
        ];

        $this->actingAs($admin)
            ->put(route('admin.members.update', $member), $payload)
            ->assertRedirect();

        $this->assertEqualsCanonicalizing(['ca', 'validateur'], $member->fresh()->adherent_roles);
    }
}
