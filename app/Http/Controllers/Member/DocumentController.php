<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Donation;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();

        // Get donations with receipts
        $donations = $member?->donations()
            ->orderBy('donation_date', 'desc')
            ->get() ?? collect();

        // Get memberships for receipts
        $memberships = $member?->memberships()
            ->orderBy('start_date', 'desc')
            ->get() ?? collect();

        return view('member.documents', compact(
            'user',
            'member',
            'donations',
            'memberships'
        ));
    }

    /**
     * Download fiscal receipt (Cerfa) for a donation
     */
    public function downloadCerfa(Donation $donation)
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();

        // Security check: ensure donation belongs to this member
        if (!$member || $donation->member_id !== $member->id) {
            abort(403, 'Accès non autorisé.');
        }

        $pdf = Pdf::loadView('member.pdf.cerfa', [
            'donation' => $donation,
            'member' => $member,
        ]);

        return $pdf->download('recu-fiscal-oreina-' . $donation->donation_date->format('Y') . '-' . $donation->id . '.pdf');
    }

    /**
     * Download membership receipt
     */
    public function downloadMembershipReceipt($membershipId)
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            abort(403, 'Accès non autorisé.');
        }

        $membership = $member->memberships()->find($membershipId);

        if (!$membership) {
            abort(404, 'Adhésion non trouvée.');
        }

        $pdf = Pdf::loadView('member.pdf.membership-receipt', [
            'membership' => $membership,
            'member' => $member,
        ]);

        return $pdf->download('recu-adhesion-oreina-' . $membership->start_date->format('Y') . '.pdf');
    }
}
