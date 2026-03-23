<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Membership;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class MembershipController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();

        $currentMembership = $member?->currentMembership();
        $membershipHistory = $member?->memberships()
            ->orderBy('start_date', 'desc')
            ->get() ?? collect();

        $isCurrentMember = $member?->isCurrentMember() ?? false;

        return view('member.membership', compact(
            'user',
            'member',
            'currentMembership',
            'membershipHistory',
            'isCurrentMember'
        ));
    }

    /**
     * Download membership card as PDF
     */
    public function downloadCard()
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            return back()->with('error', 'Profil membre non trouvé.');
        }

        $currentMembership = $member->currentMembership();

        if (!$currentMembership) {
            return back()->with('error', 'Vous n\'avez pas d\'adhésion active.');
        }

        $pdf = Pdf::loadView('member.pdf.membership-card', [
            'member' => $member,
            'membership' => $currentMembership,
        ]);

        $pdf->setPaper([0, 0, 243, 153], 'landscape'); // Credit card size: 85.6mm x 54mm

        return $pdf->download('carte-adherent-oreina-' . date('Y') . '.pdf');
    }

    /**
     * Download membership attestation
     */
    public function downloadAttestation()
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            return back()->with('error', 'Profil membre non trouvé.');
        }

        $currentMembership = $member->currentMembership();

        if (!$currentMembership) {
            return back()->with('error', 'Vous n\'avez pas d\'adhésion active.');
        }

        $pdf = Pdf::loadView('member.pdf.attestation', [
            'member' => $member,
            'membership' => $currentMembership,
        ]);

        return $pdf->download('attestation-adhesion-oreina-' . date('Y') . '.pdf');
    }
}
