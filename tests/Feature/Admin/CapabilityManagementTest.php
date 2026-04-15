<?php

namespace Tests\Feature\Admin;

use App\Models\EditorialCapability;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CapabilityManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_chief_editor_can_grant_capabilities(): void
    {
        $chief = User::factory()->create(['email_verified_at' => now(), 'role' => 'admin']);
        $chief->grantCapability(EditorialCapability::CHIEF_EDITOR);

        $target = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($chief)
            ->put(route('admin.users.capabilities.update', $target), [
                'capabilities' => [EditorialCapability::EDITOR, EditorialCapability::REVIEWER],
            ])
            ->assertRedirect(route('admin.users.edit', $target));

        $this->assertTrue($target->fresh()->hasCapability(EditorialCapability::EDITOR));
        $this->assertTrue($target->fresh()->hasCapability(EditorialCapability::REVIEWER));

        $this->assertDatabaseHas('audit_logs', [
            'table_name' => 'editorial_capabilities',
            'record_id' => $target->id,
            'action' => 'INSERT',
        ]);
    }

    public function test_chief_editor_can_revoke_capabilities(): void
    {
        $chief = User::factory()->create(['email_verified_at' => now(), 'role' => 'admin']);
        $chief->grantCapability(EditorialCapability::CHIEF_EDITOR);

        $target = User::factory()->create(['email_verified_at' => now()]);
        $target->grantCapability(EditorialCapability::EDITOR);

        $this->actingAs($chief)
            ->put(route('admin.users.capabilities.update', $target), [
                'capabilities' => [],
            ])
            ->assertRedirect();

        $this->assertFalse($target->fresh()->hasCapability(EditorialCapability::EDITOR));
        $this->assertDatabaseHas('audit_logs', [
            'record_id' => $target->id,
            'action' => 'DELETE',
        ]);
    }

    public function test_non_admin_non_chief_cannot_manage_capabilities(): void
    {
        // Use role=editor + EDITOR capability so the user passes the admin middleware
        // (isEditor() === true via capability) but is NOT an admin or chief editor.
        $editor = User::factory()->create(['email_verified_at' => now(), 'role' => 'editor']);
        $editor->grantCapability(EditorialCapability::EDITOR);
        $target = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($editor)
            ->put(route('admin.users.capabilities.update', $target), [
                'capabilities' => [EditorialCapability::EDITOR],
            ])
            ->assertForbidden();
    }
}
