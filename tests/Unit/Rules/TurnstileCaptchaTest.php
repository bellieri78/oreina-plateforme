<?php

namespace Tests\Unit\Rules;

use App\Rules\TurnstileCaptcha;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class TurnstileCaptchaTest extends TestCase
{
    public function test_passes_when_disabled(): void
    {
        config(['services.turnstile.enabled' => false]);
        Http::fake();

        $v = Validator::make(
            ['token' => 'any-value'],
            ['token' => [new TurnstileCaptcha()]]
        );

        $this->assertTrue($v->passes());
        Http::assertNothingSent();
    }

    public function test_passes_when_cloudflare_confirms(): void
    {
        config(['services.turnstile.enabled' => true, 'services.turnstile.secret_key' => 'x']);
        Http::fake([
            'challenges.cloudflare.com/*' => Http::response(['success' => true], 200),
        ]);

        $v = Validator::make(
            ['token' => 'valid-token'],
            ['token' => [new TurnstileCaptcha()]]
        );

        $this->assertTrue($v->passes());
    }

    public function test_fails_when_cloudflare_rejects(): void
    {
        config(['services.turnstile.enabled' => true, 'services.turnstile.secret_key' => 'x']);
        Http::fake([
            'challenges.cloudflare.com/*' => Http::response(['success' => false, 'error-codes' => ['invalid-input-response']], 200),
        ]);

        $v = Validator::make(
            ['token' => 'bad-token'],
            ['token' => [new TurnstileCaptcha()]]
        );

        $this->assertFalse($v->passes());
    }

    public function test_fails_on_network_error(): void
    {
        config(['services.turnstile.enabled' => true, 'services.turnstile.secret_key' => 'x']);
        Http::fake([
            'challenges.cloudflare.com/*' => Http::response('', 500),
        ]);

        $v = Validator::make(
            ['token' => 'whatever'],
            ['token' => [new TurnstileCaptcha()]]
        );

        $this->assertFalse($v->passes());
    }

    public function test_fails_when_token_empty_and_enabled(): void
    {
        config(['services.turnstile.enabled' => true, 'services.turnstile.secret_key' => 'x']);

        $v = Validator::make(
            ['token' => ''],
            ['token' => [new TurnstileCaptcha()]]
        );

        $this->assertFalse($v->passes());
    }
}
