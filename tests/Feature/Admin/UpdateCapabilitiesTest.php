<?php

namespace Tests\Feature\Admin;

use App\Models\EditorialCapability;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateCapabilitiesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_grant_lepis_editor_capability(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $target = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($admin)->put(
            route('admin.users.capabilities.update', $target),
            ['capabilities' => [EditorialCapability::LEPIS_EDITOR]]
        );

        $response->assertRedirect();
        $this->assertTrue($target->fresh()->hasCapability(EditorialCapability::LEPIS_EDITOR));
    }

    public function test_admin_can_grant_multiple_capabilities_including_lepis_editor(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $target = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($admin)->put(
            route('admin.users.capabilities.update', $target),
            ['capabilities' => [
                EditorialCapability::EDITOR,
                EditorialCapability::LEPIS_EDITOR,
            ]]
        );

        $response->assertRedirect();
        $fresh = $target->fresh();
        $this->assertTrue($fresh->hasCapability(EditorialCapability::EDITOR));
        $this->assertTrue($fresh->hasCapability(EditorialCapability::LEPIS_EDITOR));
    }
}
