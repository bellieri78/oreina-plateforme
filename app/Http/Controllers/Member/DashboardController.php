<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\JournalIssue;
use App\Models\Member;
use App\Models\Submission;
use App\Models\WorkGroup;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();

        $currentMembership = $member?->currentMembership();
        $isCurrentMember = $member?->isCurrentMember() ?? false;

        // Recent donations
        $recentDonations = $member?->donations()
            ->orderBy('donation_date', 'desc')
            ->limit(3)
            ->get() ?? collect();

        // Latest journal issues (for members)
        $latestIssues = collect();
        if ($isCurrentMember) {
            $latestIssues = JournalIssue::where('status', 'published')
                ->orderBy('publication_date', 'desc')
                ->limit(3)
                ->get();
        }

        // Work Groups
        $workGroups = WorkGroup::active()->withCount('members')->orderBy('name')->limit(3)->get();
        $myGroupIds = $member?->workGroups()->pluck('work_groups.id')->toArray() ?? [];

        // Upcoming events
        $upcomingEvents = Event::where('status', 'published')
            ->where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->limit(5)
            ->get();

        // User submissions to Chersotis (available for all users)
        $mySubmissions = Submission::where('author_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

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
            'workGroups',
            'myGroupIds',
            'stats',
            'upcomingEvents',
            'mySubmissions'
        ));
    }
}
