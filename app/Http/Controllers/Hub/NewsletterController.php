<?php

namespace App\Http\Controllers\Hub;

use App\Http\Controllers\Controller;
use App\Models\Consent;
use App\Models\Member;
use App\Services\BrevoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewsletterController extends Controller
{
    public function subscribe(Request $request, BrevoService $brevo): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'consent' => ['required', 'accepted'],
        ]);

        $email = $validated['email'];
        $firstName = $validated['first_name'] ?? null;
        $lastName = $validated['last_name'] ?? null;

        $member = Member::where('email', $email)->first();

        if ($member) {
            DB::transaction(function () use ($member, $request) {
                $member->update(['newsletter_subscribed' => true]);

                $consent = Consent::firstOrCreate(
                    [
                        'member_id' => $member->id,
                        'type' => Consent::TYPE_NEWSLETTER,
                    ],
                    [
                        'status' => true,
                        'consent_date' => now(),
                        'method' => Consent::METHOD_WEB_FORM,
                        'source' => Consent::SOURCE_NEWSLETTER_HUB,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]
                );

                // Si le consent existait déjà mais désactivé, le réactiver via updateStatus.
                if (!$consent->wasRecentlyCreated && !$consent->status) {
                    $consent->updateStatus(
                        true,
                        Consent::METHOD_WEB_FORM,
                        Consent::SOURCE_NEWSLETTER_HUB
                    );
                }
            });
        }

        // Push à Brevo systématiquement (membres ET inconnus) — Brevo gère opt-out/RGPD côté lui ; consent local n'est créé que pour les membres rattachés.
        $brevoResult = $brevo->subscribeNewsletterEmail($email, $firstName, $lastName);

        if (!$brevoResult['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Inscription enregistrée mais erreur côté Brevo. Vous serez bien recontacté·e.',
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Merci ! Vous êtes inscrit·e à la newsletter.',
        ]);
    }
}
