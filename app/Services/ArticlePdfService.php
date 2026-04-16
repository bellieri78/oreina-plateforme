<?php

namespace App\Services;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ArticlePdfService
{
    /**
     * Generate a PDF for a submission/article
     */
    public function generatePdf(Submission $submission, bool $save = true): \Barryvdh\DomPDF\PDF
    {
        $submission->load(['author', 'editor', 'journalIssue']);

        $data = [
            'submission' => $submission,
        ];

        $pdf = Pdf::loadView('pdf.article', $data);

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 150,
            'isPhpEnabled' => true,
        ]);

        if ($save) {
            $this->savePdf($submission, $pdf);
        }

        return $pdf;
    }

    /**
     * Save the PDF to storage and update the submission
     */
    public function savePdf(Submission $submission, \Barryvdh\DomPDF\PDF $pdf): string
    {
        $filename = $this->generateFilename($submission);
        $path = "submissions/pdfs/{$filename}";

        // Delete old PDF if exists
        if ($submission->pdf_file && Storage::disk('public')->exists($submission->pdf_file)) {
            Storage::disk('public')->delete($submission->pdf_file);
        }

        // Save new PDF
        Storage::disk('public')->put($path, $pdf->output());

        // Update submission
        $submission->update(['pdf_file' => $path]);

        return $path;
    }

    /**
     * Generate a filename for the PDF
     */
    protected function generateFilename(Submission $submission): string
    {
        $slug = \Illuminate\Support\Str::slug($submission->title);
        $slug = substr($slug, 0, 50); // Limit length

        $issue = $submission->journalIssue;
        if ($issue) {
            $prefix = "chersotis-{$issue->volume_number}-{$issue->issue_number}";
        } else {
            $prefix = "article";
        }

        return "{$prefix}-{$submission->id}-{$slug}.pdf";
    }

    /**
     * Parse the manuscript content (placeholder - would need actual document parsing)
     * In a real implementation, this would:
     * - Extract text from the uploaded Word/PDF manuscript
     * - Parse sections (Introduction, Methods, etc.)
     * - Convert to HTML for the PDF template
     */
    protected function parseContent(Submission $submission): ?string
    {
        // For now, return null - the template will show placeholders
        // A full implementation would use libraries like:
        // - PhpOffice/PhpWord for .docx files
        // - Smalot/PdfParser for PDF files

        // If we have stored HTML content in the future:
        // return $submission->content_html;

        return null;
    }

    /**
     * Parse references from the manuscript (placeholder)
     */
    protected function parseReferences(Submission $submission): array
    {
        // Would extract references from the manuscript
        // For now, return empty array
        return [];
    }

    /**
     * Stream the PDF for download
     */
    public function download(Submission $submission): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $pdf = $this->generatePdf($submission, false);

        $filename = $this->generateFilename($submission);

        return $pdf->download($filename);
    }

    /**
     * Stream the PDF for viewing in browser
     */
    public function stream(Submission $submission): \Illuminate\Http\Response
    {
        $pdf = $this->generatePdf($submission, false);

        return $pdf->stream($this->generateFilename($submission));
    }

    /**
     * Check if a submission can have a PDF generated
     */
    public function canGeneratePdf(Submission $submission): bool
    {
        return in_array($submission->status, [
            SubmissionStatus::Accepted,
            SubmissionStatus::InProduction,
            SubmissionStatus::Published,
        ]);
    }

    /**
     * Get the URL for the generated PDF
     */
    public function getPdfUrl(Submission $submission): ?string
    {
        if ($submission->pdf_file && Storage::disk('public')->exists($submission->pdf_file)) {
            return Storage::url($submission->pdf_file);
        }

        return null;
    }
}
