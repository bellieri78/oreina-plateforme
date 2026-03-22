<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    public const SOURCE_IMPORT = 'import';
    public const SOURCE_MANUAL = 'manual';
    public const SOURCE_HELLOASSO = 'helloasso';

    protected $fillable = [
        'member_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_amount',
        'purchase_date',
        'payment_method',
        'payment_reference',
        'notes',
        'source',
        'legacy_membership_id',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'purchase_date' => 'date',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function legacyMembership(): BelongsTo
    {
        return $this->belongsTo(Membership::class, 'legacy_membership_id');
    }

    public static function getSourceOptions(): array
    {
        return [
            self::SOURCE_IMPORT => 'Import',
            self::SOURCE_MANUAL => 'Saisie manuelle',
            self::SOURCE_HELLOASSO => 'HelloAsso',
        ];
    }

    public function getSourceLabel(): string
    {
        return self::getSourceOptions()[$this->source] ?? $this->source;
    }

    public function scopeForMember($query, int $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeYear($query, int $year)
    {
        return $query->whereYear('purchase_date', $year);
    }

    public function scopeFromImport($query)
    {
        return $query->where('source', self::SOURCE_IMPORT);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchase) {
            if (empty($purchase->total_amount)) {
                $purchase->total_amount = $purchase->unit_price * $purchase->quantity;
            }
        });
    }
}
