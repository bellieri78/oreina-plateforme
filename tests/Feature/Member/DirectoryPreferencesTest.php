<?php

namespace Tests\Feature\Member;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectoryPreferencesTest extends TestCase
{
    use RefreshDatabase;

    private function makeMemberAndUser(array $attrs = []): array
    {
        $user = User::factory()->create();
        $member = Member::create(array_merge([
            'user_id' => $user->id, 'member_number' => 'M' . uniqid(),
            'email' => $user->email,
            'first_name' => 'F', 'last_name' => 'L', 'joined_at' => now(),
        ], $attrs));
        return [$user, $member];
    }

    public function test_member_can_opt_in_with_groups(): void
    {
        [$user, $member] = $this->makeMemberAndUser();

        $this->actingAs($user)->put('/espace-membre/profil/preferences', [
            'directory_opt_in' => 1,
            'directory_groups' => ['rhopalo', 'zygenes'],
        ]);

        $member->refresh();
        $this->assertTrue($member->directory_opt_in);
        $this->assertSame(['rhopalo', 'zygenes'], $member->directory_groups);
        $this->assertNotNull($member->directory_opt_in_at);
        $this->assertSame('member_portal', $member->directory_opt_in_source);
    }

    public function test_opt_in_creates_history_with_directory_type(): void
    {
        [$user, $member] = $this->makeMemberAndUser();

        $this->actingAs($user)->put('/espace-membre/profil/preferences', [
            'directory_opt_in' => 1,
            'directory_groups' => ['rhopalo'],
        ]);

        $this->assertDatabaseHas('rgpd_consent_history', [
            'member_id' => $member->id,
            'consent_type' => 'directory',
            'value' => true,
        ]);
    }

    public function test_opt_in_requires_at_least_one_group(): void
    {
        [$user, $member] = $this->makeMemberAndUser();

        $this->actingAs($user)
            ->put('/espace-membre/profil/preferences', [
                'directory_opt_in' => 1,
                'directory_groups' => [],
            ])
            ->assertSessionHasErrors('directory_groups');

        $member->refresh();
        $this->assertFalse($member->directory_opt_in);
    }

    public function test_opt_in_rejects_invalid_group_values(): void
    {
        [$user, $member] = $this->makeMemberAndUser();

        $this->actingAs($user)
            ->put('/espace-membre/profil/preferences', [
                'directory_opt_in' => 1,
                'directory_groups' => ['rhopalo', 'invalid_group'],
            ])
            ->assertSessionHasErrors('directory_groups.*');
    }

    public function test_member_can_revoke_opt_in_keeps_groups(): void
    {
        [$user, $member] = $this->makeMemberAndUser([
            'directory_opt_in' => true,
            'directory_groups' => ['zygenes'],
            'directory_phone_visible' => true,
        ]);

        $this->actingAs($user)->put('/espace-membre/profil/preferences', [
            // directory_opt_in not present → fait défaut à 0/false
        ]);

        $member->refresh();
        $this->assertFalse($member->directory_opt_in);
        // Groupes et phone_visible préservés pour ré-activation
        $this->assertSame(['zygenes'], $member->directory_groups);
        $this->assertTrue($member->directory_phone_visible);
    }

    public function test_revoke_creates_history_entry(): void
    {
        [$user, $member] = $this->makeMemberAndUser([
            'directory_opt_in' => true,
            'directory_groups' => ['zygenes'],
        ]);

        $this->actingAs($user)->put('/espace-membre/profil/preferences', []);

        $this->assertDatabaseHas('rgpd_consent_history', [
            'member_id' => $member->id,
            'consent_type' => 'directory',
            'value' => false,
        ]);
    }

    public function test_already_opt_in_can_toggle_phone_visible(): void
    {
        [$user, $member] = $this->makeMemberAndUser([
            'directory_opt_in' => true,
            'directory_groups' => ['rhopalo'],
            'directory_phone_visible' => false,
        ]);

        $this->actingAs($user)->put('/espace-membre/profil/preferences', [
            'directory_opt_in' => 1,
            'directory_groups' => ['rhopalo'],
            'directory_phone_visible' => 1,
        ]);

        $member->refresh();
        $this->assertTrue($member->directory_phone_visible);
    }

    public function test_already_opt_in_can_change_groups(): void
    {
        [$user, $member] = $this->makeMemberAndUser([
            'directory_opt_in' => true,
            'directory_groups' => ['rhopalo'],
        ]);

        $this->actingAs($user)->put('/espace-membre/profil/preferences', [
            'directory_opt_in' => 1,
            'directory_groups' => ['micro', 'macro'],
        ]);

        $member->refresh();
        $this->assertSame(['micro', 'macro'], $member->directory_groups);
    }
}
