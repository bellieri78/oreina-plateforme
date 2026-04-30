<?php

namespace Tests\Feature\Lepis;

use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LepisFormatHelloAssoWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        MembershipType::create([
            'name' => 'Adhésion', 'slug' => 'adhesion', 'price' => 30.00,
            'duration_months' => 12, 'is_active' => true, 'sort_order' => 1,
        ]);
    }

    public function test_helloasso_order_with_papier_custom_field_sets_paper(): void
    {
        $payload = $this->orderPayload(formatLepis: 'Papier');

        $this->postJson('/api/webhooks/helloasso', $payload)->assertOk();

        $membership = Membership::query()->latest('id')->first();
        $this->assertNotNull($membership);
        $this->assertSame('paper', $membership->lepis_format);
    }

    public function test_helloasso_order_with_numerique_custom_field_sets_digital(): void
    {
        $payload = $this->orderPayload(formatLepis: 'Numérique');

        $this->postJson('/api/webhooks/helloasso', $payload)->assertOk();

        $membership = Membership::query()->latest('id')->first();
        $this->assertSame('digital', $membership->lepis_format);
    }

    public function test_helloasso_order_without_custom_field_defaults_to_paper(): void
    {
        $payload = $this->orderPayload(formatLepis: null);

        $this->postJson('/api/webhooks/helloasso', $payload)->assertOk();

        $membership = Membership::query()->latest('id')->first();
        $this->assertSame('paper', $membership->lepis_format);
    }

    private function orderPayload(?string $formatLepis): array
    {
        $customFields = [];
        if ($formatLepis !== null) {
            $customFields[] = ['name' => 'Format Lepis', 'answer' => $formatLepis];
        }
        return [
            'eventType' => 'Order',
            'data' => [
                'id' => random_int(100000, 999999),
                'formType' => 'Membership',
                'formSlug' => 'adhesion-2026',
                'payer' => [
                    'firstName' => 'Test', 'lastName' => 'User',
                    'email' => 'webhook' . random_int(1000, 9999) . '@test.com',
                    'address' => '1 rue Test', 'city' => 'Paris',
                    'zipCode' => '75000', 'country' => 'France',
                ],
                'items' => [
                    ['amount' => 3000, 'name' => 'Adhésion', 'customFields' => $customFields],
                ],
            ],
        ];
    }
}
