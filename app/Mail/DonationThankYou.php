<?php

namespace App\Mail;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DonationThankYou extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Donation $donation,
        public ?string $receiptPath = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Merci pour votre don - OREINA',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.donations.thank-you',
            with: [
                'donorName' => $this->donation->donor_name,
                'amount' => $this->donation->amount,
                'donationDate' => $this->donation->donation_date,
                'receiptNumber' => $this->donation->tax_receipt_number,
            ],
        );
    }

    public function attachments(): array
    {
        if ($this->receiptPath && file_exists(storage_path('app/' . $this->receiptPath))) {
            return [
                Attachment::fromStorage($this->receiptPath)
                    ->as('Recu-Fiscal-' . $this->donation->tax_receipt_number . '.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
