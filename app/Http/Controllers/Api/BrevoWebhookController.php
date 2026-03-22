<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BrevoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BrevoWebhookController extends Controller
{
    public function __construct(
        protected BrevoService $brevo
    ) {}

    /**
     * Handle Brevo webhook events
     *
     * Brevo sends webhooks for:
     * - unsubscribe: Contact unsubscribed
     * - hardBounce: Email hard bounced
     * - softBounce: Email soft bounced
     * - spam: Marked as spam
     * - opened: Email opened
     * - click: Link clicked
     */
    public function handle(Request $request): JsonResponse
    {
        // Log incoming webhook
        Log::channel('webhooks')->info('Brevo webhook received', [
            'event' => $request->input('event'),
            'data' => $request->all(),
        ]);

        // Verify webhook signature if configured
        if (!$this->verifySignature($request)) {
            Log::channel('webhooks')->warning('Brevo webhook signature invalid');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $event = $request->input('event');
        $email = $request->input('email');

        if (!$email) {
            return response()->json(['error' => 'No email provided'], 400);
        }

        switch ($event) {
            case 'unsubscribe':
                $this->handleUnsubscribe($email, $request->all());
                break;

            case 'hardBounce':
                $this->handleHardBounce($email, $request->all());
                break;

            case 'softBounce':
                $this->handleSoftBounce($email, $request->all());
                break;

            case 'spam':
                $this->handleSpamComplaint($email, $request->all());
                break;

            case 'opened':
            case 'click':
                // Track engagement (optional)
                $this->handleEngagement($event, $email, $request->all());
                break;

            default:
                Log::channel('webhooks')->info('Brevo webhook event not handled', [
                    'event' => $event,
                    'email' => $email,
                ]);
        }

        return response()->json(['status' => 'processed']);
    }

    /**
     * Verify Brevo webhook signature
     */
    protected function verifySignature(Request $request): bool
    {
        $secret = config('brevo.webhook_secret');

        // If no secret configured, skip verification (dev mode)
        if (empty($secret)) {
            return true;
        }

        // Brevo uses a simple shared secret in header
        $signature = $request->header('X-Brevo-Signature');

        if (!$signature) {
            return false;
        }

        // Verify the signature
        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Handle unsubscribe event
     */
    protected function handleUnsubscribe(string $email, array $data): void
    {
        $this->brevo->handleUnsubscribe($email);

        Log::channel('webhooks')->info('Brevo unsubscribe processed', [
            'email' => $email,
            'reason' => $data['reason'] ?? 'unknown',
        ]);
    }

    /**
     * Handle hard bounce event
     */
    protected function handleHardBounce(string $email, array $data): void
    {
        $this->brevo->handleHardBounce($email);

        Log::channel('webhooks')->warning('Brevo hard bounce processed', [
            'email' => $email,
            'reason' => $data['reason'] ?? 'unknown',
        ]);
    }

    /**
     * Handle soft bounce event
     */
    protected function handleSoftBounce(string $email, array $data): void
    {
        // Soft bounces are temporary, just log them
        Log::channel('webhooks')->info('Brevo soft bounce', [
            'email' => $email,
            'reason' => $data['reason'] ?? 'unknown',
        ]);
    }

    /**
     * Handle spam complaint
     */
    protected function handleSpamComplaint(string $email, array $data): void
    {
        // Treat spam complaints like unsubscribes
        $this->brevo->handleUnsubscribe($email);

        Log::channel('webhooks')->warning('Brevo spam complaint processed', [
            'email' => $email,
        ]);
    }

    /**
     * Handle engagement events (opens, clicks)
     */
    protected function handleEngagement(string $event, string $email, array $data): void
    {
        // Optional: track engagement metrics
        Log::channel('webhooks')->debug('Brevo engagement', [
            'event' => $event,
            'email' => $email,
            'campaign' => $data['campaign_id'] ?? null,
        ]);
    }
}
