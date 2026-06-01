<?php

namespace Tests\Feature\Hub;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HubEventVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function event(array $attrs = []): Event
    {
        return Event::create(array_merge([
            'title' => 'E'.uniqid(), 'slug' => 'e'.uniqid(),
            'start_date' => now()->addWeek(), 'status' => 'published', 'visibility' => Event::VIS_PUBLIC,
        ], $attrs));
    }

    public function test_hub_index_lists_only_public_events(): void
    {
        $pub = $this->event(['title' => 'Sortie publique', 'visibility' => Event::VIS_PUBLIC]);
        $mem = $this->event(['title' => 'Reunion adherents', 'visibility' => Event::VIS_MEMBERS]);

        $this->get(route('hub.events.index'))
            ->assertOk()
            ->assertSee('Sortie publique')
            ->assertDontSee('Reunion adherents');
    }

    public function test_hub_show_404_for_members_event_as_guest(): void
    {
        $mem = $this->event(['visibility' => Event::VIS_MEMBERS]);

        $this->get(route('hub.events.show', $mem))->assertNotFound();
    }

    public function test_hub_show_ok_for_public_event(): void
    {
        $pub = $this->event(['visibility' => Event::VIS_PUBLIC]);

        $this->get(route('hub.events.show', $pub))->assertOk();
    }

    public function test_related_events_exclude_non_public(): void
    {
        $public = $this->event([
            'title' => 'Sortie publique principale', 'visibility' => Event::VIS_PUBLIC, 'event_type' => 'sortie',
        ]);
        $this->event([
            'title' => 'Atelier reserve adherents', 'visibility' => Event::VIS_MEMBERS, 'event_type' => 'sortie',
        ]);

        $this->get(route('hub.events.show', $public))
            ->assertOk()
            ->assertDontSee('Atelier reserve adherents');
    }
}
