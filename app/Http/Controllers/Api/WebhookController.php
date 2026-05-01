<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\DonationThankYou;
use App\Mail\WelcomeMember;
use App\Models\Donation;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use App\Services\RecuFiscalPdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class WebhookController extends Controller
{
    /**
     * Handle HelloAsso webhook
     *
     * HelloAsso sends webhooks for:
     * - Order (payment completed)
     * - Payment (payment status change)
     * - Form (form changes)
     */
    public function helloasso(Request $request): JsonResponse
    {
        // Log the incoming webhook
        Log::channel('webhooks')->info('HelloAsso webhook received', [
            'type' => $request->input('eventType'),
            'data' => $request->all(),
        ]);

        // Verify the webhook signature (if configured)
        if (!$this->verifyHelloAssoSignature($request)) {
            Log::channel('webhooks')->warning('HelloAsso webhook signature invalid');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $eventType = $request->input('eventType');

        switch ($eventType) {
            case 'Order':
                return $this->handleHelloAssoOrder($request);
            case 'Payment':
                return $this->handleHelloAssoPayment($request);
            default:
                Log::channel('webhooks')->info('HelloAsso webhook type not handled', ['type' => $eventType]);
                return response()->json(['status' => 'ignored']);
        }
    }

    /**
     * Verify HelloAsso webhook signature
     */
    private function verifyHelloAssoSignature(Request $request): bool
    {
        $secret = config('services.helloasso.webhook_secret');

        // If no secret is configured, skip verification (dev mode)
        if (empty($secret)) {
            return true;
        }

        $signature = $request->header('X-HelloAsso-Signature');
        if (!$signature) {
            return false;
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Handle HelloAsso Order (payment completed)
     */
    private function handleHelloAssoOrder(Request $request): JsonResponse
    {
        $data = $request->input('data');

        if (!$data) {
            return response()->json(['error' => 'No data provided'], 400);
        }

        $formSlug = $data['formSlug'] ?? null;
        $formType = $data['formType'] ?? null;
        $items = $data['items'] ?? [];
        $payer = $data['payer'] ?? [];

        // Handle membership payments
        if ($formType === 'Membership' || str_contains(strtolower($formSlug ?? ''), 'adhesion')) {
            return $this->processMembership($data, $payer, $items);
        }

        // Handle donations
        if ($formType === 'Donation' || str_contains(strtolower($formSlug ?? ''), 'don')) {
            return $this->processDonation($data, $payer, $items);
        }

        return response()->json(['status' => 'processed']);
    }

    /**
     * Handle HelloAsso Payment status change
     */
    private function handleHelloAssoPayment(Request $request): JsonResponse
    {
        $data = $request->input('data');
        $state = $data['state'] ?? null;

        Log::channel('webhooks')->info('HelloAsso payment status', [
            'state' => $state,
            'order_id' => $data['order']['id'] ?? null,
        ]);

        // Handle refunds, cancellations, etc.
        if ($state === 'Refunded' || $state === 'Canceled') {
            // Could update membership status here
        }

        return response()->json(['status' => 'processed']);
    }

    /**
     * Process a membership payment
     */
    private function processMembership(array $data, array $payer, array $items): JsonResponse
    {
        $email = $payer['email'] ?? null;
        $firstName = $payer['firstName'] ?? '';
        $lastName = $payer['lastName'] ?? '';
        $fullName = trim("{$firstName} {$lastName}");

        if (!$email) {
            Log::channel('webhooks')->error('HelloAsso membership: no email provided');
            return response()->json(['error' => 'No email provided'], 400);
        }

        // Find or create user
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $fullName,
                'password' => Hash::make(Str::random(16)),
            ]
        );

        // Find or create member
        $isNewMember = false;
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            $isNewMember = true;
            $member = Member::create([
                'user_id' => $user->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $payer['phone'] ?? null,
                'address' => $payer['address'] ?? null,
                'city' => $payer['city'] ?? null,
                'postal_code' => $payer['zipCode'] ?? null,
                'country' => $payer['country'] ?? 'France',
                'member_number' => $this->generateMemberNumber(),
            ]);
        }

        // Determine membership type from items
        $membershipType = $this->determineMembershipType($items);

        // Create or renew membership
        $startDate = now();
        $endDate = now()->addYear();

        // Check if there's an existing active membership
        $existingMembership = $member->memberships()
            ->where('end_date', '>=', now())
            ->first();

        if ($existingMembership) {
            // Extend the existing membership
            $startDate = $existingMembership->end_date;
            $endDate = $startDate->copy()->addYear();
        }

        $amount = collect($items)->sum('amount') / 100; // Convert cents to euros

        // Extract Lepis format from items[].customFields[]
        $lepisFormat = $this->extractLepisFormat($items);

        $membership = Membership::create([
            'member_id' => $member->id,
            'membership_type_id' => $membershipType?->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'amount_paid' => $amount,
            'payment_method' => 'helloasso',
            'payment_reference' => $data['id'] ?? null,
            'status' => 'active',
            'lepis_format' => $lepisFormat,
        ]);

        // Update member status
        $member->update([
            'status' => 'active',
            'membership_expires_at' => $endDate,
        ]);

        Log::channel('webhooks')->info('HelloAsso membership created', [
            'member_id' => $member->id,
            'membership_id' => $membership->id,
            'amount' => $amount,
            'is_new_member' => $isNewMember,
        ]);

        // Send welcome email to new members
        if ($isNewMember) {
            try {
                Mail::to($member->email)->queue(new WelcomeMember($member));
                Log::channel('webhooks')->info('Welcome email queued', ['member_id' => $member->id]);
            } catch (\Exception $e) {
                Log::channel('webhooks')->error('Failed to send welcome email', [
                    'member_id' => $member->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'member_id' => $member->id,
            'membership_id' => $membership->id,
            'is_new_member' => $isNewMember,
        ]);
    }

    /**
     * Process a donation
     */
    private function processDonation(array $data, array $payer, array $items): JsonResponse
    {
        $email = $payer['email'] ?? null;
        $firstName = $payer['firstName'] ?? '';
        $lastName = $payer['lastName'] ?? '';
        $fullName = trim("{$firstName} {$lastName}");
        $amount = collect($items)->sum('amount') / 100;

        if (!$email) {
            Log::channel('webhooks')->error('HelloAsso donation: no email provided');
            return response()->json(['error' => 'No email provided'], 400);
        }

        Log::channel('webhooks')->info('HelloAsso donation received', [
            'email' => $email,
            'amount' => $amount,
            'donor_name' => $fullName,
        ]);

        // Find existing member if any
        $member = Member::where('email', $email)->first();

        // Determine campaign from form data
        $campaign = $data['formSlug'] ?? $data['formName'] ?? 'HelloAsso';

        // Create donation record
        $donation = Donation::create([
            'member_id' => $member?->id,
            'donor_name' => $fullName ?: 'Donateur anonyme',
            'donor_email' => $email,
            'donor_address' => $payer['address'] ?? null,
            'donor_postal_code' => $payer['zipCode'] ?? null,
            'donor_city' => $payer['city'] ?? null,
            'amount' => $amount,
            'payment_method' => 'helloasso',
            'payment_reference' => $data['id'] ?? null,
            'campaign' => $campaign,
            'donation_date' => now(),
            'tax_receipt_sent' => false,
        ]);

        Log::channel('webhooks')->info('Donation record created', [
            'donation_id' => $donation->id,
            'amount' => $amount,
        ]);

        // Generate Cerfa tax receipt
        $receiptPath = null;
        try {
            $receiptService = app(RecuFiscalPdfService::class);
            $receiptPath = $receiptService->generateForDonation($donation);

            $donation->update([
                'tax_receipt_file' => $receiptPath,
            ]);

            Log::channel('webhooks')->info('Tax receipt generated', [
                'donation_id' => $donation->id,
                'receipt_number' => $donation->tax_receipt_number,
                'file' => $receiptPath,
            ]);
        } catch (\Exception $e) {
            Log::channel('webhooks')->error('Failed to generate tax receipt', [
                'donation_id' => $donation->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Send thank you email with receipt attached
        try {
            Mail::to($donation->donor_email)->queue(new DonationThankYou($donation, $receiptPath));

            $donation->update([
                'tax_receipt_sent' => true,
                'tax_receipt_sent_at' => now(),
            ]);

            Log::channel('webhooks')->info('Thank you email queued', [
                'donation_id' => $donation->id,
                'email' => $donation->donor_email,
            ]);
        } catch (\Exception $e) {
            Log::channel('webhooks')->error('Failed to send thank you email', [
                'donation_id' => $donation->id,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'donation_id' => $donation->id,
            'donation_amount' => $amount,
            'receipt_number' => $donation->tax_receipt_number,
        ]);
    }

    /**
     * Determine membership type from HelloAsso items
     */
    private function determineMembershipType(array $items): ?MembershipType
    {
        foreach ($items as $item) {
            $name = strtolower($item['name'] ?? '');
            $amount = ($item['amount'] ?? 0) / 100;

            // Try to match by name
            if (str_contains($name, 'étudiant') || str_contains($name, 'student')) {
                return MembershipType::where('slug', 'etudiant')->first();
            }
            if (str_contains($name, 'soutien') || str_contains($name, 'bienfaiteur')) {
                return MembershipType::where('slug', 'soutien')->first();
            }
            if (str_contains($name, 'couple') || str_contains($name, 'famille')) {
                return MembershipType::where('slug', 'couple')->first();
            }
        }

        // Default to standard membership
        return MembershipType::where('slug', 'standard')
            ->orWhere('slug', 'adhesion')
            ->first();
    }

    /**
     * Extrait le format Lepis depuis les customFields des items HelloAsso
     */
    private function extractLepisFormat(array $items): string
    {
        foreach ($items as $item) {
            foreach ($item['customFields'] ?? [] as $field) {
                $name = $field['name'] ?? '';
                $answer = $field['answer'] ?? '';
                if (mb_strtolower($name) === 'format lepis') {
                    $normalized = mb_strtolower(trim($answer));
                    if ($normalized === 'numérique' || $normalized === 'numerique' || $normalized === 'digital') {
                        return 'digital';
                    }
                    if ($normalized === 'papier' || $normalized === 'paper') {
                        return 'paper';
                    }
                }
            }
        }

        Log::channel('webhooks')->warning('HelloAsso: lepis_format custom field missing or unrecognized, defaulting to paper');
        return 'paper';
    }

    /**
     * Generate a unique member number
     */
    private function generateMemberNumber(): string
    {
        $year = now()->format('Y');
        $lastMember = Member::whereYear('created_at', now()->year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastMember ? (intval(substr($lastMember->member_number, -4)) + 1) : 1;

        return "OR{$year}" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
