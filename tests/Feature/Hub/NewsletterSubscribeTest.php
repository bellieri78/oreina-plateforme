<?php

namespace Tests\Feature\Hub;

use App\Models\Consent;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NewsletterSubscribeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Stub la config Brevo pour avoir l'air configuré pendant les tests.
        config([
            'brevo.api_key' => 'test-key',
            'brevo.lists.newsletter' => 42,
        ]);

        // Empêcher les vrais appels HTTP.
        Http::fake([
            'api.brevo.com/*' => Http::response(['id' => 1], 200),
        ]);
    }

    public function test_subscribe_with_valid_email_and_consent_returns_success(): void
    {
        $response = $this->postJson('/newsletter/subscribe', [
            'email' => 'visitor@example.com',
            'consent' => true,
        ]);

        $response->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_subscribe_without_consent_fails_validation(): void
    {
        $response = $this->postJson('/newsletter/subscribe', [
            'email' => 'visitor@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['consent']);
    }

    public function test_subscribe_with_invalid_email_fails_validation(): void
    {
        $response = $this->postJson('/newsletter/subscribe', [
            'email' => 'not-an-email',
            'consent' => true,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_subscribe_existing_member_marks_newsletter_and_creates_consent(): void
    {
        // Pas de MemberFactory dans ce projet : on crée à la main avec les champs requis.
        $member = Member::create([
            'contact_type' => Member::TYPE_INDIVIDUEL,
            'first_name' => 'Test',
            'last_name' => 'Member',
            'email' => 'member@example.com',
            'member_number' => 'TEST-NL-001',
            'newsletter_subscribed' => false,
        ]);

        $response = $this->postJson('/newsletter/subscribe', [
            'email' => 'member@example.com',
            'consent' => true,
        ]);

        $response->assertOk();

        $this->assertTrue($member->fresh()->newsletter_subscribed);
        $this->assertDatabaseHas('consents', [
            'member_id' => $member->id,
            'type' => Consent::TYPE_NEWSLETTER,
            'status' => true,
            'source' => Consent::SOURCE_NEWSLETTER_HUB,
        ]);
    }

    public function test_subscribe_calls_brevo_api(): void
    {
        $this->postJson('/newsletter/subscribe', [
            'email' => 'visitor@example.com',
            'first_name' => 'Marie',
            'last_name' => 'Curie',
            'consent' => true,
        ])->assertOk();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.brevo.com/v3/contacts'
                && $request['email'] === 'visitor@example.com'
                && $request['attributes']['PRENOM'] === 'Marie'
                && $request['attributes']['NOM'] === 'Curie'
                && in_array(42, $request['listIds']);
        });
    }

    public function test_subscribe_succeeds_locally_when_brevo_list_not_configured(): void
    {
        config(['brevo.lists.newsletter' => null]);

        $response = $this->postJson('/newsletter/subscribe', [
            'email' => 'visitor@example.com',
            'consent' => true,
        ]);

        $response->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_subscribe_is_throttled(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/newsletter/subscribe', [
                'email' => "v{$i}@example.com",
                'consent' => true,
            ])->assertOk();
        }

        $this->postJson('/newsletter/subscribe', [
            'email' => 'v6@example.com',
            'consent' => true,
        ])->assertStatus(429);
    }
}
