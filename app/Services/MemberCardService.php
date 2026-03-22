<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Membership;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class MemberCardService
{
    /**
     * Generate a membership card PDF for a single member
     */
    public function generateCard(Member $member, ?Membership $membership = null): \Barryvdh\DomPDF\PDF
    {
        // Get active membership if not provided
        $membership = $membership ?? $member->currentMembership();

        $data = $this->prepareCardData($member, $membership);

        return Pdf::loadView('pdf.member-card', $data)
            ->setPaper([0, 0, 242, 153], 'landscape'); // Credit card size: 85.6mm x 54mm
    }

    /**
     * Generate multiple membership cards on A4 pages
     */
    public function generateMultipleCards(Collection $members): \Barryvdh\DomPDF\PDF
    {
        $cards = [];

        foreach ($members as $member) {
            $membership = $member->currentMembership();
            if ($membership) {
                $cards[] = $this->prepareCardData($member, $membership);
            }
        }

        return Pdf::loadView('pdf.member-cards-batch', ['cards' => $cards])
            ->setPaper('a4', 'portrait');
    }

    /**
     * Prepare card data for PDF generation
     */
    protected function prepareCardData(Member $member, ?Membership $membership): array
    {
        $memberNumber = $member->member_number ?? 'N/A';
        $validFrom = $membership?->start_date?->format('d/m/Y') ?? '-';
        $validUntil = $membership?->end_date?->format('d/m/Y') ?? '-';
        $membershipType = $membership?->membershipType?->name ?? 'Standard';
        $year = $membership?->start_date?->format('Y') ?? date('Y');

        // Generate a verification code (simple hash)
        $verificationCode = strtoupper(substr(md5($member->id . $member->created_at . ($membership?->id ?? '')), 0, 8));

        return [
            'member' => $member,
            'membership' => $membership,
            'memberNumber' => $memberNumber,
            'validFrom' => $validFrom,
            'validUntil' => $validUntil,
            'membershipType' => $membershipType,
            'year' => $year,
            'verificationCode' => $verificationCode,
            'issueDate' => now()->format('d/m/Y'),
        ];
    }

    /**
     * Download filename for member card
     */
    public function getFilename(Member $member): string
    {
        $name = preg_replace('/[^a-z0-9]/i', '_', $member->full_name);
        return "carte_adherent_{$name}_" . date('Y') . ".pdf";
    }

    /**
     * Download filename for batch cards
     */
    public function getBatchFilename(): string
    {
        return "cartes_adherents_" . date('Y-m-d') . ".pdf";
    }
}
