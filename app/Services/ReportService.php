<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\Member;
use App\Models\Membership;
use App\Models\VolunteerActivity;
use App\Models\VolunteerParticipation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Generate annual membership report
     */
    public function generateMembershipReport(int $year): \Barryvdh\DomPDF\PDF
    {
        $memberships = Membership::with('member')
            ->whereYear('start_date', $year)
            ->orWhere(function ($q) use ($year) {
                $q->whereYear('end_date', $year);
            })
            ->orderBy('start_date')
            ->get();

        $stats = [
            'total' => $memberships->count(),
            'active' => $memberships->filter(fn($m) => $m->isActive())->count(),
            'expired' => $memberships->filter(fn($m) => !$m->isActive())->count(),
            'by_type' => $memberships->groupBy('type')->map->count(),
            'by_payment' => $memberships->groupBy('payment_method')->map->count(),
            'total_amount' => $memberships->sum('amount'),
            'by_month' => $memberships->groupBy(fn($m) => $m->start_date->month)->map->count(),
        ];

        $pdf = Pdf::loadView('pdf.reports.memberships', [
            'memberships' => $memberships,
            'stats' => $stats,
            'year' => $year,
            'generated_at' => now(),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Generate annual donation report
     */
    public function generateDonationReport(int $year): \Barryvdh\DomPDF\PDF
    {
        $donations = Donation::with('member')
            ->whereYear('donation_date', $year)
            ->orderBy('donation_date')
            ->get();

        $stats = [
            'total_count' => $donations->count(),
            'total_amount' => $donations->sum('amount'),
            'average_amount' => $donations->avg('amount'),
            'unique_donors' => $donations->pluck('member_id')->unique()->count(),
            'by_payment' => $donations->groupBy('payment_method')->map(fn($g) => [
                'count' => $g->count(),
                'amount' => $g->sum('amount'),
            ]),
            'by_month' => [],
            'with_receipt' => $donations->where('receipt_sent', true)->count(),
        ];

        // Monthly breakdown
        for ($m = 1; $m <= 12; $m++) {
            $monthDonations = $donations->filter(fn($d) => $d->donation_date->month === $m);
            $stats['by_month'][$m] = [
                'count' => $monthDonations->count(),
                'amount' => $monthDonations->sum('amount'),
            ];
        }

        $pdf = Pdf::loadView('pdf.reports.donations', [
            'donations' => $donations,
            'stats' => $stats,
            'year' => $year,
            'generated_at' => now(),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Generate volunteer activity report
     */
    public function generateVolunteerReport(int $year): \Barryvdh\DomPDF\PDF
    {
        $activities = VolunteerActivity::with(['activityType', 'organizer', 'structure', 'participations.member'])
            ->whereYear('activity_date', $year)
            ->orderBy('activity_date')
            ->get();

        $participations = VolunteerParticipation::with(['member', 'activity.activityType'])
            ->whereHas('activity', fn($q) => $q->whereYear('activity_date', $year))
            ->get();

        $stats = [
            'total_activities' => $activities->count(),
            'completed' => $activities->where('status', 'completed')->count(),
            'cancelled' => $activities->where('status', 'cancelled')->count(),
            'total_hours' => $participations->where('status', 'attended')->sum('hours_worked'),
            'unique_volunteers' => $participations->where('status', 'attended')->pluck('member_id')->unique()->count(),
            'total_participations' => $participations->where('status', 'attended')->count(),
            'by_type' => [],
            'by_month' => [],
            'top_volunteers' => [],
        ];

        // By type
        $byType = $activities->groupBy(fn($a) => $a->activityType?->name ?? 'Autre');
        foreach ($byType as $typeName => $typeActivities) {
            $typeParticipations = $participations->filter(fn($p) =>
                ($p->activity->activityType?->name ?? 'Autre') === $typeName && $p->status === 'attended'
            );
            $stats['by_type'][$typeName] = [
                'count' => $typeActivities->count(),
                'participants' => $typeParticipations->count(),
                'hours' => $typeParticipations->sum('hours_worked'),
            ];
        }

        // By month
        for ($m = 1; $m <= 12; $m++) {
            $monthActivities = $activities->filter(fn($a) => $a->activity_date->month === $m);
            $monthParticipations = $participations->filter(fn($p) =>
                $p->activity->activity_date->month === $m && $p->status === 'attended'
            );
            $stats['by_month'][$m] = [
                'activities' => $monthActivities->count(),
                'participants' => $monthParticipations->count(),
                'hours' => $monthParticipations->sum('hours_worked'),
            ];
        }

        // Top volunteers
        $stats['top_volunteers'] = $participations
            ->where('status', 'attended')
            ->groupBy('member_id')
            ->map(fn($group) => [
                'member' => $group->first()->member,
                'activities' => $group->count(),
                'hours' => $group->sum('hours_worked'),
            ])
            ->sortByDesc('hours')
            ->take(15)
            ->values();

        $pdf = Pdf::loadView('pdf.reports.volunteer', [
            'activities' => $activities,
            'stats' => $stats,
            'year' => $year,
            'generated_at' => now(),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Generate volunteer certificate for a member
     */
    public function generateVolunteerCertificate(Member $member, int $year): \Barryvdh\DomPDF\PDF
    {
        $participations = $member->volunteerParticipations()
            ->with(['activity.activityType'])
            ->whereHas('activity', fn($q) => $q->whereYear('activity_date', $year))
            ->where('status', 'attended')
            ->get();

        $stats = [
            'total_activities' => $participations->count(),
            'total_hours' => $participations->sum('hours_worked'),
            'by_type' => $participations->groupBy(fn($p) => $p->activity->activityType?->name ?? 'Autre')
                ->map(fn($g) => [
                    'count' => $g->count(),
                    'hours' => $g->sum('hours_worked'),
                ]),
        ];

        $pdf = Pdf::loadView('pdf.reports.volunteer-certificate', [
            'member' => $member,
            'participations' => $participations,
            'stats' => $stats,
            'year' => $year,
            'generated_at' => now(),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Generate global annual report
     */
    public function generateAnnualReport(int $year): \Barryvdh\DomPDF\PDF
    {
        // Memberships
        $memberships = Membership::whereYear('start_date', $year)->get();
        $membershipStats = [
            'total' => $memberships->count(),
            'total_amount' => $memberships->sum('amount'),
            'by_type' => $memberships->groupBy('type')->map->count(),
        ];

        // Donations
        $donations = Donation::whereYear('donation_date', $year)->get();
        $donationStats = [
            'total_count' => $donations->count(),
            'total_amount' => $donations->sum('amount'),
            'unique_donors' => $donations->pluck('member_id')->unique()->count(),
        ];

        // Volunteer
        $activities = VolunteerActivity::whereYear('activity_date', $year)->get();
        $participations = VolunteerParticipation::whereHas('activity', fn($q) => $q->whereYear('activity_date', $year))
            ->where('status', 'attended')
            ->get();
        $volunteerStats = [
            'total_activities' => $activities->count(),
            'completed_activities' => $activities->where('status', 'completed')->count(),
            'total_hours' => $participations->sum('hours_worked'),
            'unique_volunteers' => $participations->pluck('member_id')->unique()->count(),
        ];

        // Members
        $totalMembers = Member::count();
        $activeMembers = Member::whereHas('memberships', fn($q) =>
            $q->where('end_date', '>=', now())
        )->count();

        $pdf = Pdf::loadView('pdf.reports.annual', [
            'membershipStats' => $membershipStats,
            'donationStats' => $donationStats,
            'volunteerStats' => $volunteerStats,
            'totalMembers' => $totalMembers,
            'activeMembers' => $activeMembers,
            'year' => $year,
            'generated_at' => now(),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }
}
