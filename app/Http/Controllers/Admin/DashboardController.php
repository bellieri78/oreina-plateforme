<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Donation;
use App\Models\Event;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Main stats
        $stats = [
            'members_total' => Member::count(),
            'members_active' => Member::where('is_active', true)->count(),
            'members_new_month' => Member::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
            'donations_total' => Donation::sum('amount'),
            'donations_year' => Donation::whereYear('donation_date', now()->year)->sum('amount'),
            'donations_count_year' => Donation::whereYear('donation_date', now()->year)->count(),
            'donations_pending_receipt' => Donation::where('tax_receipt_sent', false)->count(),
            'articles_published' => Article::where('status', 'published')->count(),
            'articles_draft' => Article::where('status', 'draft')->count(),
            'submissions_pending' => Submission::whereIn('status', ['submitted', 'under_review'])->count(),
            'memberships_active' => Membership::where('end_date', '>=', now())->count(),
            'memberships_expired' => Membership::where('end_date', '<', now())->count(),
            'memberships_year_amount' => Membership::whereYear('start_date', now()->year)->sum('amount_paid'),
            'events_upcoming' => Event::where('start_date', '>=', now())->count(),
        ];

        // Monthly donations for chart (last 12 months)
        $donationsChart = Donation::selectRaw("TO_CHAR(donation_date, 'YYYY-MM') as month, SUM(amount) as total")
            ->where('donation_date', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Monthly memberships for chart (last 12 months)
        $membershipsChart = Membership::selectRaw("TO_CHAR(start_date, 'YYYY-MM') as month, COUNT(*) as total")
            ->where('start_date', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Fill missing months
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $months[$month] = [
                'label' => now()->subMonths($i)->translatedFormat('M Y'),
                'donations' => $donationsChart[$month] ?? 0,
                'memberships' => $membershipsChart[$month] ?? 0,
            ];
        }

        $recentMembers = Member::latest()->take(5)->get();
        $recentDonations = Donation::with('member')->latest('donation_date')->take(5)->get();
        $recentMemberships = Membership::with('member')->latest()->take(5)->get();

        // Upcoming events
        $upcomingEvents = Event::where('start_date', '>=', now())
            ->orderBy('start_date')
            ->take(3)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'months', 'recentMembers', 'recentDonations',
            'recentMemberships', 'upcomingEvents'
        ));
    }
}
