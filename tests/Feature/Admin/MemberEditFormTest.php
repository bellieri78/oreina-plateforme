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

    public function test_create_form_does_not_render_sidebar_recap(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/extranet/members/create');

        $response->assertOk()
            ->assertDontSee('Retour à la fiche');
    }

    public function test_edit_form_pre_fills_existing_values(): void
    {
        $admin = $this->makeAdmin();
        $u = User::factory()->create(['email' => 'marie@test.com']);
        $member = Member::create([
            'user_id' => $u->id,
            'member_number' => 'M5555',
            'email' => 'marie@test.com',
            'civilite' => 'Mme',
            'first_name' => 'Marie',
            'last_name' => 'Durand',
            'profession' => 'Botaniste',
            'mobile' => '06 12 34 56 78',
            'consent_image' => true,
            'interests' => 'papillons alpins',
            'joined_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get("/extranet/members/{$member->id}/edit");

        $response->assertOk()
            ->assertSee('Mme', escape: false)
            ->assertSee('Marie')
            ->assertSee('Botaniste')
            ->assertSee('06 12 34 56 78')
            ->assertSee('papillons alpins');
    }

    public function test_update_persists_new_fields(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();

        $response = $this->actingAs($admin)->put("/extranet/members/{$member->id}", [
            'civilite' => 'Mme',
            'first_name' => 'Marie',
            'last_name' => 'Durand',
            'birth_date' => '1980-05-15',
            'profession' => 'Botaniste',
            'email' => $member->email,
            'mobile' => '06 12 34 56 78',
            'telephone_fixe' => '01 23 45 67 89',
            'address' => '1 rue Test',
            'postal_code' => '75001',
            'city' => 'Paris',
            'country' => 'France',
            'contact_type' => 'individuel',
            'interests' => 'papillons alpins',
            'newsletter_subscribed' => '1',
            'is_active' => '1',
            'consent_communication' => '1',
            'consent_image' => '1',
        ]);

        $response->assertRedirect();
        $member->refresh();
        $this->assertSame('Mme', $member->civilite);
        $this->assertSame('1980-05-15', $member->birth_date->format('Y-m-d'));
        $this->assertSame('Botaniste', $member->profession);
        $this->assertSame('06 12 34 56 78', $member->mobile);
        $this->assertSame('01 23 45 67 89', $member->telephone_fixe);
        $this->assertSame('papillons alpins', $member->interests);
        $this->assertTrue((bool) $member->newsletter_subscribed);
        $this->assertTrue((bool) $member->consent_communication);
        $this->assertTrue((bool) $member->consent_image);
    }

    public function test_update_uncheck_unchecks_booleans(): void
    {
        $admin = $this->makeAdmin();
        $u = User::factory()->create();
        $member = Member::create([
            'user_id' => $u->id,
            'member_number' => 'M' . random_int(1000, 99999),
            'email' => $u->email,
            'first_name' => 'F',
            'last_name' => 'L',
            'newsletter_subscribed' => true,
            'consent_image' => true,
            'is_active' => true,
            'joined_at' => now(),
        ]);

        // POST without checkbox names → presence-based pattern unsets them
        $this->actingAs($admin)->put("/extranet/members/{$member->id}", [
            'first_name' => 'F',
            'last_name' => 'L',
            'email' => $member->email,
            'contact_type' => 'individuel',
        ])->assertRedirect();

        $member->refresh();
        $this->assertFalse((bool) $member->newsletter_subscribed);
        $this->assertFalse((bool) $member->consent_image);
        $this->assertFalse((bool) $member->is_active);
    }

    public function test_phone_column_dropped_after_migration(): void
    {
        $this->assertFalse(\Illuminate\Support\Facades\Schema::hasColumn('members', 'phone'));
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
