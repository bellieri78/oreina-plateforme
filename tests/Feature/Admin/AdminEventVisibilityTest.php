<?php

namespace Tests\Feature\Admin;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEventVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_create_restricted_event_with_roles(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.events.store'), [
                'title' => 'Reunion bureau',
                'start_date' => now()->addWeek()->format('Y-m-d\TH:i'),
                'status' => 'published',
                'visibility' => 'restricted',
                'audience_roles' => ['ca', 'bureau'],
            ])->assertRedirect();

        $event = Event::where('title', 'Reunion bureau')->firstOrFail();
        $this->assertSame('restricted', $event->visibility);
        $this->assertEqualsCanonicalizing(['ca', 'bureau'], $event->audience_roles);
    }

    public function test_restricted_requires_roles(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.events.store'), [
                'title' => 'Sans cible',
                'start_date' => now()->addWeek()->format('Y-m-d\TH:i'),
                'status' => 'published',
                'visibility' => 'restricted',
            ])->assertSessionHasErrors('audience_roles');
    }

    public function test_members_visibility_clears_roles(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.events.store'), [
                'title' => 'Pour adherents',
                'start_date' => now()->addWeek()->format('Y-m-d\TH:i'),
                'status' => 'published',
                'visibility' => 'members',
                'audience_roles' => ['ca'],
            ])->assertRedirect();

        $event = Event::where('title', 'Pour adherents')->firstOrFail();
        $this->assertNull($event->audience_roles);
    }
}
