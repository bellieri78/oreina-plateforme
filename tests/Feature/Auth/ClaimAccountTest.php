<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ClaimAccountTest extends TestCase
{
    use RefreshDatabase;

    private function signedUrl(User $user, int $minutes = 60): string
    {
        return URL::temporarySignedRoute(
            'account.claim',
            now()->addMinutes($minutes),
            ['user' => $user->id]
        );
    }

    public function test_valid_signed_url_shows_claim_form(): void
    {
        $ghost = User::factory()->ghost()->create();

        $response = $this->get($this->signedUrl($ghost));

        $response->assertOk();
        $response->assertSee('mot de passe');
        $response->assertSee($ghost->email);
    }

    public function test_tampered_signature_returns_403(): void
    {
        $ghost = User::factory()->ghost()->create();
        $url = $this->signedUrl($ghost).'&tampered=1';

        $response = $this->get($url);

        $response->assertStatus(403);
    }

    public function test_expired_url_returns_403(): void
    {
        $ghost = User::factory()->ghost()->create();
        $url = URL::temporarySignedRoute(
            'account.claim',
            now()->subMinute(),
            ['user' => $ghost->id]
        );

        $response = $this->get($url);

        $response->assertStatus(403);
    }

    public function test_post_password_activates_account_and_logs_in(): void
    {
        $ghost = User::factory()->ghost()->create();
        $signedPost = URL::temporarySignedRoute(
            'account.claim.store',
            now()->addMinutes(60),
            ['user' => $ghost->id]
        );

        $response = $this->post($signedPost, [
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('journal.submissions.index'));
        $response->assertSessionHas('success');

        $ghost->refresh();
        $this->assertNotNull($ghost->claimed_at);
        $this->assertNotNull($ghost->email_verified_at);
        $this->assertTrue(Hash::check('password123', $ghost->password));
        $this->assertAuthenticatedAs($ghost);
    }

    public function test_post_password_unconfirmed_fails_validation(): void
    {
        $ghost = User::factory()->ghost()->create();
        $signedPost = URL::temporarySignedRoute(
            'account.claim.store',
            now()->addMinutes(60),
            ['user' => $ghost->id]
        );

        $response = $this->post($signedPost, [
            'password' => 'password123',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors('password');
        $ghost->refresh();
        $this->assertNull($ghost->claimed_at);
    }

    public function test_post_password_too_short_fails_validation(): void
    {
        $ghost = User::factory()->ghost()->create();
        $signedPost = URL::temporarySignedRoute(
            'account.claim.store',
            now()->addMinutes(60),
            ['user' => $ghost->id]
        );

        $response = $this->post($signedPost, [
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_claiming_an_already_claimed_account_redirects_to_login(): void
    {
        $claimed = User::factory()->ghost()->claimed()->create();

        $response = $this->get($this->signedUrl($claimed));

        $response->assertRedirect(route('hub.login'));
        $response->assertSessionHas('info');
    }

    public function test_form_action_preserves_signature_so_post_succeeds(): void
    {
        $ghost = User::factory()->ghost()->create();
        $url = $this->signedUrl($ghost);

        // Simulate real browser: GET the signed URL, extract the form action
        $response = $this->get($url);
        $response->assertOk();

        // The rendered form must submit to a URL that keeps the signature
        $html = $response->getContent();
        $this->assertMatchesRegularExpression(
            '/action="[^"]*\?[^"]*signature=[^"]+/',
            $html,
            'Le formulaire doit garder les query params signature/expires pour que le POST passe le middleware signed.'
        );
    }
}
