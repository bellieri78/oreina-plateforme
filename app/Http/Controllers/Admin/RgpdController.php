<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\RgpdConsentHistory;
use App\Models\RgpdReview;
use App\Models\RgpdSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RgpdController extends Controller
{
    /**
     * RGPD Dashboard
     */
    public function index()
    {
        // Get retention settings
        $settings = RgpdSetting::getAllSettings();

        // Count alerts by type
        $alerts = $this->countAlerts($settings);

        // Recent reviews
        $recentReviews = RgpdReview::with(['member', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Consent statistics
        $consentStats = [
            'newsletter' => Member::notAnonymized()->where('newsletter_subscribed', true)->count(),
            'communication' => Member::notAnonymized()->where('consent_communication', true)->count(),
            'image' => Member::notAnonymized()->where('consent_image', true)->count(),
            'total' => Member::notAnonymized()->count(),
        ];

        // Anonymization stats
        $anonymizationStats = [
            'anonymized' => Member::where('anonymise', true)->count(),
            'deleted' => Member::onlyTrashed()->count(),
        ];

        return view('admin.rgpd.index', compact(
            'settings',
            'alerts',
            'recentReviews',
            'consentStats',
            'anonymizationStats'
        ));
    }

    /**
     * Count alerts by type based on retention settings
     */
    private function countAlerts(array $settings): array
    {
        $noInteractionMonths = $settings['retention_no_interaction'] ?? 36;
        $notUpdatedMonths = $settings['retention_not_updated'] ?? 60;
        $expiredMembershipMonths = $settings['retention_expired_membership'] ?? 24;
        $inactiveDonorMonths = $settings['retention_inactive_donor'] ?? 48;

        return [
            'no_interaction' => Member::notAnonymized()
                ->noInteractionSince($noInteractionMonths)
                ->count(),
            'not_updated' => Member::notAnonymized()
                ->notUpdatedSince($notUpdatedMonths)
                ->count(),
            'expired_membership' => Member::notAnonymized()
                ->expiredMembershipSince($expiredMembershipMonths)
                ->count(),
            'inactive_donor' => Member::notAnonymized()
                ->inactiveDonorSince($inactiveDonorMonths)
                ->count(),
        ];
    }

    /**
     * List members with RGPD alerts
     */
    public function alerts(Request $request)
    {
        $alertType = $request->get('type', 'no_interaction');
        $settings = RgpdSetting::getAllSettings();

        $query = Member::notAnonymized()->with(['memberships', 'donations']);

        switch ($alertType) {
            case 'no_interaction':
                $months = $settings['retention_no_interaction'] ?? 36;
                $query->noInteractionSince($months);
                break;
            case 'not_updated':
                $months = $settings['retention_not_updated'] ?? 60;
                $query->notUpdatedSince($months);
                break;
            case 'expired_membership':
                $months = $settings['retention_expired_membership'] ?? 24;
                $query->expiredMembershipSince($months);
                break;
            case 'inactive_donor':
                $months = $settings['retention_inactive_donor'] ?? 48;
                $query->inactiveDonorSince($months);
                break;
        }

        $members = $query->orderBy('updated_at', 'asc')->paginate(25);

        $alertTypes = RgpdReview::getAlertTypes();
        $actions = RgpdReview::getActions();

        return view('admin.rgpd.alerts', compact(
            'members',
            'alertType',
            'alertTypes',
            'actions'
        ));
    }

    /**
     * Process RGPD review action
     */
    public function process(Request $request, Member $member)
    {
        $request->validate([
            'alert_type' => 'required|string',
            'action' => 'required|string|in:keep,update,contact,anonymize',
            'notes' => 'nullable|string|max:1000',
            'next_review_date' => 'nullable|date|after:today',
        ]);

        DB::transaction(function () use ($request, $member) {
            // Create review record
            RgpdReview::create([
                'member_id' => $member->id,
                'alert_type' => $request->alert_type,
                'action' => $request->action,
                'notes' => $request->notes,
                'next_review_date' => $request->next_review_date,
                'user_id' => auth()->id(),
            ]);

            // Execute action
            switch ($request->action) {
                case 'keep':
                    $member->markRgpdReviewed($request->notes);
                    break;
                case 'update':
                    $member->markRgpdReviewed($request->notes);
                    break;
                case 'contact':
                    $member->markRgpdReviewed($request->notes);
                    // TODO: Send contact email
                    break;
                case 'anonymize':
                    $member->anonymize();
                    break;
            }
        });

        return redirect()->back()->with('success', 'Action RGPD enregistree avec succes.');
    }

    /**
     * Bulk process multiple members
     */
    public function bulkProcess(Request $request)
    {
        $request->validate([
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:members,id',
            'alert_type' => 'required|string',
            'action' => 'required|string|in:keep,update,contact,anonymize',
            'notes' => 'nullable|string|max:1000',
        ]);

        $count = 0;

        DB::transaction(function () use ($request, &$count) {
            foreach ($request->member_ids as $memberId) {
                $member = Member::find($memberId);
                if (!$member || $member->anonymise) {
                    continue;
                }

                RgpdReview::create([
                    'member_id' => $member->id,
                    'alert_type' => $request->alert_type,
                    'action' => $request->action,
                    'notes' => $request->notes,
                    'user_id' => auth()->id(),
                ]);

                if ($request->action === 'anonymize') {
                    $member->anonymize();
                } else {
                    $member->markRgpdReviewed($request->notes);
                }

                $count++;
            }
        });

        return redirect()->back()->with('success', "{$count} contact(s) traite(s) avec succes.");
    }

    /**
     * RGPD Settings page
     */
    public function settings()
    {
        $settings = RgpdSetting::all();

        return view('admin.rgpd.settings', compact('settings'));
    }

    /**
     * Update RGPD settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'retention_no_interaction' => 'required|integer|min:6|max:120',
            'retention_not_updated' => 'required|integer|min:12|max:120',
            'retention_expired_membership' => 'required|integer|min:6|max:120',
            'retention_inactive_donor' => 'required|integer|min:12|max:120',
        ]);

        foreach ($request->only([
            'retention_no_interaction',
            'retention_not_updated',
            'retention_expired_membership',
            'retention_inactive_donor',
        ]) as $key => $value) {
            RgpdSetting::setValue($key, (int) $value);
        }

        return redirect()->route('admin.rgpd.settings')
            ->with('success', 'Parametres RGPD mis a jour.');
    }

    /**
     * Trash (soft-deleted members)
     */
    public function trash()
    {
        $members = Member::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(25);

        return view('admin.rgpd.trash', compact('members'));
    }

    /**
     * Restore a soft-deleted member
     */
    public function restore(int $id)
    {
        $member = Member::onlyTrashed()->findOrFail($id);
        $member->restore();

        return redirect()->route('admin.rgpd.trash')
            ->with('success', 'Contact restaure avec succes.');
    }

    /**
     * Permanently delete a member
     */
    public function forceDelete(int $id)
    {
        $member = Member::onlyTrashed()->findOrFail($id);

        // Delete photo if exists
        if ($member->photo_path && \Storage::exists($member->photo_path)) {
            \Storage::delete($member->photo_path);
        }

        $member->forceDelete();

        return redirect()->route('admin.rgpd.trash')
            ->with('success', 'Contact supprime definitivement.');
    }

    /**
     * Anonymize a specific member
     */
    public function anonymize(Request $request, Member $member)
    {
        if ($member->anonymise) {
            return redirect()->back()->with('error', 'Ce contact est deja anonymise.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($request, $member) {
            RgpdReview::create([
                'member_id' => $member->id,
                'alert_type' => 'manual',
                'action' => RgpdReview::ACTION_ANONYMIZE,
                'notes' => $request->notes ?? 'Anonymisation manuelle',
                'user_id' => auth()->id(),
            ]);

            $member->anonymize();
        });

        return redirect()->back()->with('success', 'Contact anonymise avec succes.');
    }

    /**
     * Member consent history
     */
    public function consentHistory(Member $member)
    {
        $history = $member->rgpdConsentHistory()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $reviews = $member->rgpdReviews()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.rgpd.consent-history', compact('member', 'history', 'reviews'));
    }

    /**
     * Update member consents
     */
    public function updateConsents(Request $request, Member $member)
    {
        $request->validate([
            'newsletter_subscribed' => 'boolean',
            'consent_communication' => 'boolean',
            'consent_image' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $notes = $request->notes;

        // Newsletter
        $member->setRgpdConsent(
            RgpdConsentHistory::TYPE_NEWSLETTER,
            (bool) $request->newsletter_subscribed,
            RgpdConsentHistory::SOURCE_MANUAL,
            $notes
        );

        // Communication
        $member->setRgpdConsent(
            RgpdConsentHistory::TYPE_COMMUNICATION,
            (bool) $request->consent_communication,
            RgpdConsentHistory::SOURCE_MANUAL,
            $notes
        );

        // Image
        $member->setRgpdConsent(
            RgpdConsentHistory::TYPE_IMAGE,
            (bool) $request->consent_image,
            RgpdConsentHistory::SOURCE_MANUAL,
            $notes
        );

        return redirect()->back()->with('success', 'Consentements mis a jour.');
    }

    /**
     * Export RGPD report for a member
     */
    public function exportMemberData(Member $member)
    {
        $data = [
            'member' => $member->toArray(),
            'memberships' => $member->memberships()->get()->toArray(),
            'donations' => $member->donations()->get()->toArray(),
            'consents' => $member->consents()->get()->toArray(),
            'consent_history' => $member->rgpdConsentHistory()->get()->toArray(),
            'rgpd_reviews' => $member->rgpdReviews()->get()->toArray(),
            'exported_at' => now()->toIso8601String(),
        ];

        $filename = 'rgpd_export_' . $member->id . '_' . date('Y-m-d') . '.json';

        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Type', 'application/json');
    }
}
