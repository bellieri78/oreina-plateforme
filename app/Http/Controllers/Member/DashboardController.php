<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\JournalIssue;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();

        // Get membership info
        $currentMembership = $member?->currentMembership();
        $isCurrentMember = $member?->isCurrentMember() ?? false;

        // Get recent donations
        $recentDonations = $member?->donations()
            ->orderBy('donation_date', 'desc')
            ->limit(3)
            ->get() ?? collect();

        // Get latest journal issues (for members)
        $latestIssues = [];
        if ($isCurrentMember) {
            $latestIssues = JournalIssue::where('status', 'published')
                ->orderBy('publication_date', 'desc')
                ->limit(3)
                ->get();
        }

        // Stats
        $stats = [
            'total_donations' => $member?->donations()->sum('amount') ?? 0,
            'donation_count' => $member?->donations()->count() ?? 0,
            'membership_years' => $member?->memberships()->where('status', 'active')->count() ?? 0,
        ];

        return view('member.dashboard', compact(
            'user',
            'member',
            'currentMembership',
            'isCurrentMember',
            'recentDonations',
            'latestIssues',
            'stats'
        ));
    }
}
