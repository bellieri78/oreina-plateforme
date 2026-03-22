<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\MemberCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\MemberCardMail;

class MemberCardController extends Controller
{
    protected MemberCardService $cardService;

    public function __construct(MemberCardService $cardService)
    {
        $this->cardService = $cardService;
    }

    /**
     * Generate and download a member card PDF
     */
    public function download(Member $member)
    {
        $membership = $member->currentMembership();

        if (!$membership) {
            return redirect()->back()
                ->with('error', 'Ce membre n\'a pas d\'adhesion active. Impossible de generer une carte.');
        }

        $pdf = $this->cardService->generateCard($member, $membership);
        $filename = $this->cardService->getFilename($member);

        return $pdf->download($filename);
    }

    /**
     * Preview a member card in browser
     */
    public function preview(Member $member)
    {
        $membership = $member->currentMembership();

        if (!$membership) {
            return redirect()->back()
                ->with('error', 'Ce membre n\'a pas d\'adhesion active.');
        }

        $pdf = $this->cardService->generateCard($member, $membership);

        return $pdf->stream($this->cardService->getFilename($member));
    }

    /**
     * Generate and download multiple member cards
     */
    public function downloadBatch(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = explode(',', $request->get('ids'));

        $members = Member::whereIn('id', $ids)
            ->with(['memberships' => function ($q) {
                $q->where('status', 'active')
                    ->where('end_date', '>=', now())
                    ->latest('end_date');
            }, 'memberships.membershipType'])
            ->get()
            ->filter(fn($member) => $member->currentMembership() !== null);

        if ($members->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Aucun membre selectionne n\'a d\'adhesion active.');
        }

        $pdf = $this->cardService->generateMultipleCards($members);

        return $pdf->download($this->cardService->getBatchFilename());
    }

    /**
     * Show member cards management page
     */
    public function index(Request $request)
    {
        $query = Member::with(['memberships' => function ($q) {
            $q->where('status', 'active')
                ->where('end_date', '>=', now())
                ->latest('end_date');
        }, 'memberships.membershipType'])
            ->whereHas('memberships', function ($q) {
                $q->where('status', 'active')
                    ->where('end_date', '>=', now());
            });

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                    ->orWhere('last_name', 'ilike', "%{$search}%")
                    ->orWhere('member_number', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        // Filter by membership type
        if ($request->filled('type')) {
            $query->whereHas('memberships', function ($q) use ($request) {
                $q->where('status', 'active')
                    ->where('end_date', '>=', now())
                    ->where('membership_type_id', $request->get('type'));
            });
        }

        $members = $query->orderBy('last_name')->paginate(25)->withQueryString();

        // Get membership types for filter
        $membershipTypes = \App\Models\MembershipType::ordered()->pluck('name', 'id');

        // Stats
        $stats = [
            'total_active' => Member::whereHas('memberships', function ($q) {
                $q->where('status', 'active')
                    ->where('end_date', '>=', now());
            })->count(),
        ];

        return view('admin.member-cards.index', compact('members', 'membershipTypes', 'stats'));
    }

    /**
     * Send member card by email
     */
    public function send(Member $member)
    {
        $membership = $member->currentMembership();

        if (!$membership) {
            return redirect()->back()
                ->with('error', 'Ce membre n\'a pas d\'adhesion active.');
        }

        if (!$member->email) {
            return redirect()->back()
                ->with('error', 'Ce membre n\'a pas d\'adresse email.');
        }

        try {
            $pdf = $this->cardService->generateCard($member, $membership);
            $pdfContent = $pdf->output();

            Mail::to($member->email)->send(new MemberCardMail($member, $pdfContent));

            return redirect()->back()
                ->with('success', "Carte envoyee par email a {$member->email}.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'envoi: ' . $e->getMessage());
        }
    }

    /**
     * Send multiple member cards by email
     */
    public function sendBatch(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = explode(',', $request->get('ids'));

        $members = Member::whereIn('id', $ids)
            ->whereNotNull('email')
            ->with(['memberships' => function ($q) {
                $q->where('status', 'active')
                    ->where('end_date', '>=', now())
                    ->latest('end_date');
            }, 'memberships.membershipType'])
            ->get()
            ->filter(fn($member) => $member->currentMembership() !== null);

        if ($members->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Aucun membre selectionne n\'a d\'adhesion active et d\'email.');
        }

        $sent = 0;
        $errors = 0;

        foreach ($members as $member) {
            try {
                $membership = $member->currentMembership();
                $pdf = $this->cardService->generateCard($member, $membership);
                $pdfContent = $pdf->output();

                Mail::to($member->email)->send(new MemberCardMail($member, $pdfContent));
                $sent++;
            } catch (\Exception $e) {
                $errors++;
            }
        }

        if ($errors > 0) {
            return redirect()->back()
                ->with('warning', "{$sent} carte(s) envoyee(s), {$errors} erreur(s).");
        }

        return redirect()->back()
            ->with('success', "{$sent} carte(s) envoyee(s) par email.");
    }
}
