<?php

namespace Tests\Unit\Models;

use App\Models\EditorialCapability;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserGhostTest extends TestCase
{
    use RefreshDatabase;

    public function test_ghost_scope_returns_only_uninvited_unclaimed_users(): void
    {
        $ghost = User::factory()->ghost()->create();
        $claimed = User::factory()->ghost()->claimed()->create();
        $normal = User::factory()->create();

        $results = User::ghost()->pluck('id');

        $this->assertTrue($results->contains($ghost->id));
        $this->assertFalse($results->contains($claimed->id));
        $this->assertFalse($results->contains($normal->id));
    }

    public function test_claimed_scope_returns_only_claimed_users(): void
    {
        $ghost = User::factory()->ghost()->create();
        $claimed = User::factory()->ghost()->claimed()->create();
        $normal = User::factory()->create();

        $results = User::claimed()->pluck('id');

        $this->assertFalse($results->contains($ghost->id));
        $this->assertTrue($results->contains($claimed->id));
        $this->assertTrue($results->contains($normal->id));
    }

    public function test_is_ghost_returns_true_for_ghost_user(): void
    {
        $ghost = User::factory()->ghost()->create();
        $this->assertTrue($ghost->isGhost());
    }

    public function test_is_ghost_returns_false_for_claimed_user(): void
    {
        $claimed = User::factory()->ghost()->claimed()->create();
        $this->assertFalse($claimed->isGhost());
    }

    public function test_is_ghost_returns_false_for_regular_user(): void
    {
        $user = User::factory()->create();
        $this->assertFalse($user->isGhost());
    }

    public function test_with_capability_scope_does_not_include_ghosts(): void
    {
        $ghost = User::factory()->ghost()->create();
        $editor = User::factory()->create();
        $editor->capabilities()->create([
            'capability' => EditorialCapability::EDITOR,
            'granted_at' => now(),
        ]);

        $results = User::withCapability(EditorialCapability::EDITOR)->pluck('id');

        $this->assertFalse($results->contains($ghost->id));
        $this->assertTrue($results->contains($editor->id));
    }

    public function test_invited_by_relation_returns_the_inviter(): void
    {
        $inviter = User::factory()->create();
        $ghost = User::factory()->ghost()->create(['invited_by_user_id' => $inviter->id]);

        $this->assertEquals($inviter->id, $ghost->invitedBy->id);
    }
}
