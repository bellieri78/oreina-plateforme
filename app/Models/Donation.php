<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    protected $fillable = [
        'member_id',
        'donor_email',
        'donor_name',
        'donor_address',
        'donor_postal_code',
        'donor_city',
        'amount',
        'payment_method',
        'payment_reference',
        'campaign',
        'donation_date',
        'tax_receipt_sent',
        'tax_receipt_number',
        'tax_receipt_sent_at',
        'tax_receipt_file',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'donation_date' => 'date',
        'tax_receipt_sent' => 'boolean',
        'tax_receipt_sent_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeCampaign($query, string $campaign)
    {
        return $query->where('campaign', $campaign);
    }

    public function scopeYear($query, int $year)
    {
        return $query->whereYear('donation_date', $year);
    }

    public function scopePendingReceipt($query)
    {
        return $query->where('tax_receipt_sent', false);
    }

    public static function generateReceiptNumber(): string
    {
        $year = date('Y');
        $lastDonation = self::whereYear('tax_receipt_sent_at', $year)
            ->whereNotNull('tax_receipt_number')
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastDonation ? (int) substr($lastDonation->tax_receipt_number, -4) + 1 : 1;

        return sprintf('RF%s%04d', $year, $sequence);
    }
}
