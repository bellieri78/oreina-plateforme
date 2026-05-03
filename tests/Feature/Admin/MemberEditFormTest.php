<?php

namespace Tests\Feature\Admin;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberEditFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_validation_rejects_future_birth_date(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();

        $response = $this->actingAs($admin)->put("/extranet/members/{$member->id}", [
            'first_name' => 'Marie',
            'last_name' => 'Durand',
            'email' => $member->email,
            'birth_date' => now()->addYear()->format('Y-m-d'),
            'contact_type' => 'individuel',
        ]);

        $response->assertSessionHasErrors('birth_date');
    }

    public function test_update_validation_rejects_invalid_civilite(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();

        $response = $this->actingAs($admin)->put("/extranet/members/{$member->id}", [
            'first_name' => 'Marie',
            'last_name' => 'Durand',
            'email' => $member->email,
            'civilite' => 'Mlle',  // not in Member::CIVILITES
            'contact_type' => 'individuel',
        ]);

        $response->assertSessionHasErrors('civilite');
    }

    public function test_update_validation_rejects_invalid_contact_type(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();

        $response = $this->actingAs($admin)->put("/extranet/members/{$member->id}", [
            'first_name' => 'Marie',
            'last_name' => 'Durand',
            'email' => $member->email,
            'contact_type' => 'famille',  // not allowed
        ]);

        $response->assertSessionHasErrors('contact_type');
    }

    public function test_edit_form_renders_5_cards(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();

        $response = $this->actingAs($admin)->get("/extranet/members/{$member->id}/edit");

        $response->assertOk()
            ->assertSee('Identité')
            ->assertSee('Contact')
            ->assertSee('Adresse')
            ->assertSee('Préférences')
            ->assertSee('Statut & RGPD');
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
}
