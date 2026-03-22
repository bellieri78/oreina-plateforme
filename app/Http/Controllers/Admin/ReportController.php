<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Reports dashboard
     */
    public function index()
    {
        $years = range(date('Y'), date('Y') - 5);

        return view('admin.reports.index', compact('years'));
    }

    /**
     * Generate membership report
     */
    public function memberships(Request $request)
    {
        $year = $request->get('year', date('Y'));

        $pdf = $this->reportService->generateMembershipReport($year);

        return $pdf->download("rapport_adhesions_{$year}.pdf");
    }

    /**
     * Generate donation report
     */
    public function donations(Request $request)
    {
        $year = $request->get('year', date('Y'));

        $pdf = $this->reportService->generateDonationReport($year);

        return $pdf->download("rapport_dons_{$year}.pdf");
    }

    /**
     * Generate volunteer report
     */
    public function volunteer(Request $request)
    {
        $year = $request->get('year', date('Y'));

        $pdf = $this->reportService->generateVolunteerReport($year);

        return $pdf->download("rapport_benevolat_{$year}.pdf");
    }

    /**
     * Generate volunteer certificate for a member
     */
    public function volunteerCertificate(Request $request, Member $member)
    {
        $year = $request->get('year', date('Y'));

        $pdf = $this->reportService->generateVolunteerCertificate($member, $year);

        $filename = "attestation_benevolat_{$member->last_name}_{$year}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Generate annual report
     */
    public function annual(Request $request)
    {
        $year = $request->get('year', date('Y'));

        $pdf = $this->reportService->generateAnnualReport($year);

        return $pdf->download("rapport_annuel_{$year}.pdf");
    }
}
