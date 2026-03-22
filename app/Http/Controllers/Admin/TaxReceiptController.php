<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Services\RecuFiscalPdfService;

class TaxReceiptController extends Controller
{
    protected RecuFiscalPdfService $pdfService;

    public function __construct(RecuFiscalPdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Affiche le reçu fiscal en mode stream (aperçu)
     */
    public function view(Donation $donation)
    {
        return $this->pdfService->stream($donation);
    }

    /**
     * Télécharge le reçu fiscal PDF
     */
    public function download(Donation $donation)
    {
        // Génère le numéro si pas encore attribué
        if (!$donation->tax_receipt_number) {
            $donation->tax_receipt_number = Donation::generateReceiptNumber();
            $donation->tax_receipt_sent = true;
            $donation->tax_receipt_sent_at = now();
            $donation->save();
        }

        return $this->pdfService->download($donation);
    }
}
