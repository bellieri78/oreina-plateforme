<?php

namespace Tests\Feature\Api;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventApiVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function event(array $attrs = []): Event
    {
        return Event::create(array_merge([
            'title'      => 'E' . uniqid(),
            'slug'       => 'e' . uniqid(),
            'start_date' => now()->addWeek(),
            'status'     => 'published',
            'visibility' => Event::VIS_PUBLIC,
        ], $attrs));
    }

    public function test_api_index_liste_uniquement_les_evenements_publics(): void
    {
        $pub = $this->event(['title' => 'Événement public API', 'visibility' => Event::VIS_PUBLIC]);
        $mem = $this->event(['title' => 'Événement adhérents API', 'visibility' => Event::VIS_MEMBERS]);

        $res = $this->getJson('/api/v1/events')->assertOk();

        $titles = collect($res->json('data'))->pluck('title');
        $this->assertTrue($titles->contains('Événement public API'), 'L\'événement public doit apparaître');
        $this->assertFalse($titles->contains('Événement adhérents API'), 'L\'événement adhérents ne doit pas apparaître');
    }

    public function test_api_upcoming_liste_uniquement_les_evenements_publics(): void
    {
        $pub = $this->event(['title' => 'Prochain public', 'visibility' => Event::VIS_PUBLIC]);
        $mem = $this->event(['title' => 'Prochain adhérents', 'visibility' => Event::VIS_MEMBERS]);
        $rest = $this->event(['title' => 'Prochain restreint', 'visibility' => Event::VIS_RESTRICTED]);

        $response = $this->getJson('/api/v1/events/upcoming')->assertOk();

        $titles = collect($response->json('data'))->pluck('title');
        $this->assertTrue($titles->contains('Prochain public'), 'L\'événement public doit apparaître');
        $this->assertFalse($titles->contains('Prochain adhérents'), 'L\'événement adhérents ne doit pas apparaître');
        $this->assertFalse($titles->contains('Prochain restreint'), 'L\'événement restreint ne doit pas apparaître');
    }

    public function test_api_show_retourne_404_pour_evenement_non_public(): void
    {
        $mem = $this->event(['visibility' => Event::VIS_MEMBERS]);

        $this->getJson('/api/v1/events/' . $mem->slug)->assertNotFound();
    }

    public function test_api_show_retourne_evenement_public(): void
    {
        $pub = $this->event(['title' => 'Détail public', 'visibility' => Event::VIS_PUBLIC]);

        $response = $this->getJson('/api/v1/events/' . $pub->slug)->assertOk();
        $response->assertJsonPath('data.title', 'Détail public');
    }

    public function test_api_index_exclut_evenements_non_publies(): void
    {
        $draft = $this->event(['title' => 'Brouillon', 'status' => 'draft', 'visibility' => Event::VIS_PUBLIC]);
        $pub   = $this->event(['title' => 'Publié public', 'status' => 'published', 'visibility' => Event::VIS_PUBLIC]);

        $res = $this->getJson('/api/v1/events')->assertOk();

        $titles = collect($res->json('data'))->pluck('title');
        $this->assertTrue($titles->contains('Publié public'), 'L\'événement publié doit apparaître');
        $this->assertFalse($titles->contains('Brouillon'), 'Le brouillon ne doit pas apparaître');
    }
}
