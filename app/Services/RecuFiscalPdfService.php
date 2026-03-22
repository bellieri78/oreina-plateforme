<?php

namespace App\Services;

use App\Models\Donation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class RecuFiscalPdfService
{
    /**
     * Informations de l'association (à personnaliser dans config/oreina.php)
     */
    protected array $association;

    public function __construct()
    {
        $this->association = config('oreina.association', [
            'name' => 'OREINA - Les Lépidoptères de France',
            'address' => '123 rue des Papillons',
            'postal_code' => '75001',
            'city' => 'Paris',
            'siret' => '123 456 789 00012',
            'objet' => 'Étude et protection des lépidoptères de France',
            'rna' => 'W123456789',
        ]);
    }

    /**
     * Génère un reçu fiscal PDF pour un don
     */
    public function generateForDonation(Donation $donation): string
    {
        // Génère le numéro de reçu si non existant
        if (!$donation->tax_receipt_number) {
            $donation->tax_receipt_number = Donation::generateReceiptNumber();
            $donation->save();
        }

        $data = [
            'association' => $this->association,
            'donation' => $donation,
            'receipt_number' => $donation->tax_receipt_number,
            'receipt_date' => now(),
            'donor' => [
                'name' => $donation->donor_name,
                'email' => $donation->donor_email,
                'address' => $donation->donor_address ?? $donation->member?->address ?? '',
                'postal_code' => $donation->donor_postal_code ?? $donation->member?->postal_code ?? '',
                'city' => $donation->donor_city ?? $donation->member?->city ?? '',
            ],
            'amount' => $donation->amount,
            'amount_letters' => $this->numberToWords($donation->amount),
            'donation_date' => $donation->donation_date,
            'payment_method' => $this->formatPaymentMethod($donation->payment_method),
            'year' => $donation->donation_date->format('Y'),
        ];

        $pdf = Pdf::loadView('pdf.recu-fiscal', $data);
        $pdf->setPaper('A4', 'portrait');

        // Génère le nom du fichier
        $filename = sprintf(
            'recus-fiscaux/%s/RF-%s-%s.pdf',
            $donation->donation_date->format('Y'),
            $donation->tax_receipt_number,
            str_replace(' ', '-', $donation->donor_name)
        );

        // Sauvegarde le PDF
        Storage::put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Télécharge directement le PDF
     */
    public function download(Donation $donation)
    {
        $data = $this->prepareData($donation);

        $pdf = Pdf::loadView('pdf.recu-fiscal', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = sprintf(
            'Recu-Fiscal-%s-%s.pdf',
            $donation->tax_receipt_number ?? 'DRAFT',
            str_replace(' ', '-', $donation->donor_name)
        );

        return $pdf->download($filename);
    }

    /**
     * Renvoie le contenu PDF en tant que stream
     */
    public function stream(Donation $donation)
    {
        $data = $this->prepareData($donation);

        $pdf = Pdf::loadView('pdf.recu-fiscal', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Recu-Fiscal.pdf');
    }

    /**
     * Prépare les données pour le template
     */
    protected function prepareData(Donation $donation): array
    {
        return [
            'association' => $this->association,
            'donation' => $donation,
            'receipt_number' => $donation->tax_receipt_number ?? 'BROUILLON',
            'receipt_date' => now(),
            'donor' => [
                'name' => $donation->donor_name,
                'email' => $donation->donor_email,
                'address' => $donation->donor_address ?? $donation->member?->address ?? '',
                'postal_code' => $donation->donor_postal_code ?? $donation->member?->postal_code ?? '',
                'city' => $donation->donor_city ?? $donation->member?->city ?? '',
            ],
            'amount' => $donation->amount,
            'amount_letters' => $this->numberToWords($donation->amount),
            'donation_date' => $donation->donation_date,
            'payment_method' => $this->formatPaymentMethod($donation->payment_method),
            'year' => $donation->donation_date->format('Y'),
        ];
    }

    /**
     * Convertit un montant en lettres (français)
     */
    protected function numberToWords(float $amount): string
    {
        $units = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix',
                  'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];
        $tens = ['', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante', 'quatre-vingt', 'quatre-vingt'];

        $euros = (int) $amount;
        $cents = round(($amount - $euros) * 100);

        $result = $this->convertNumber($euros, $units, $tens);
        $result .= $euros > 1 ? ' euros' : ' euro';

        if ($cents > 0) {
            $result .= ' et ' . $this->convertNumber((int) $cents, $units, $tens);
            $result .= $cents > 1 ? ' centimes' : ' centime';
        }

        return ucfirst($result);
    }

    /**
     * Convertit un nombre en mots
     */
    protected function convertNumber(int $n, array $units, array $tens): string
    {
        if ($n < 20) {
            return $units[$n];
        }

        if ($n < 100) {
            $ten = (int) ($n / 10);
            $unit = $n % 10;

            if ($ten == 7 || $ten == 9) {
                $unit += 10;
                $ten--;
            }

            $result = $tens[$ten];
            if ($unit == 1 && $ten < 8) {
                $result .= ' et un';
            } elseif ($unit > 0) {
                $result .= '-' . $units[$unit];
            } elseif ($ten == 8) {
                $result .= 's';
            }

            return $result;
        }

        if ($n < 1000) {
            $hundred = (int) ($n / 100);
            $rest = $n % 100;

            $result = $hundred == 1 ? 'cent' : $units[$hundred] . ' cent';
            if ($rest == 0 && $hundred > 1) {
                $result .= 's';
            } elseif ($rest > 0) {
                $result .= ' ' . $this->convertNumber($rest, $units, $tens);
            }

            return $result;
        }

        if ($n < 1000000) {
            $thousand = (int) ($n / 1000);
            $rest = $n % 1000;

            $result = $thousand == 1 ? 'mille' : $this->convertNumber($thousand, $units, $tens) . ' mille';
            if ($rest > 0) {
                $result .= ' ' . $this->convertNumber($rest, $units, $tens);
            }

            return $result;
        }

        return (string) $n;
    }

    /**
     * Formate le moyen de paiement pour l'affichage
     */
    protected function formatPaymentMethod(?string $method): string
    {
        return match ($method) {
            'helloasso' => 'Paiement en ligne (HelloAsso)',
            'virement' => 'Virement bancaire',
            'cheque' => 'Chèque',
            'especes' => 'Espèces',
            'carte' => 'Carte bancaire',
            default => 'Autre',
        };
    }
}
