<?php

namespace Tests\Feature\Admin;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventShowAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_renders_featured_image_when_present(): void
    {
        $admin = $this->makeAdmin();
        $event = Event::create([
            'organizer_id' => $admin->id,
            'title' => 'Conférence',
            'slug' => 'conf',
            'start_date' => now()->addWeek(),
            'featured_image' => 'events/images/cover.jpg',
            'status' => 'published',
        ]);

        $response = $this->actingAs($admin)->get("/extranet/events/{$event->id}");

        $response->assertOk()
            ->assertSee('events/images/cover.jpg', escape: false);
    }

    public function test_show_renders_content_html_correctly(): void
    {
        $admin = $this->makeAdmin();
        $event = Event::create([
            'organizer_id' => $admin->id,
            'title' => 'HTML',
            'slug' => 'html',
            'start_date' => now()->addWeek(),
            'content' => '<p>Programme <strong>détaillé</strong></p>',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($admin)->get("/extranet/events/{$event->id}");

        $response->assertOk()
            ->assertSee('<strong>détaillé</strong>', escape: false)
            ->assertDontSee('&lt;strong&gt;', escape: false);
    }

    public function test_show_displays_upcoming_badge_for_future_event(): void
    {
        $admin = $this->makeAdmin();
        $event = Event::create([
            'organizer_id' => $admin->id,
            'title' => 'Futur',
            'slug' => 'futur',
            'start_date' => now()->addWeek(),
            'status' => 'published',
        ]);

        $response = $this->actingAs($admin)->get("/extranet/events/{$event->id}");

        $response->assertOk()->assertSee('À venir');
    }

    public function test_show_displays_past_badge_for_past_event(): void
    {
        $admin = $this->makeAdmin();
        $event = Event::create([
            'organizer_id' => $admin->id,
            'title' => 'Passé',
            'slug' => 'passe',
            'start_date' => now()->subDays(10),
            'end_date' => now()->subDays(7),
            'status' => 'published',
        ]);

        $response = $this->actingAs($admin)->get("/extranet/events/{$event->id}");

        $response->assertOk()->assertSee('Passé');
    }

    protected function makeAdmin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }
}
