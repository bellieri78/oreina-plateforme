<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    public const TYPE_MAGAZINE = 'magazine';
    public const TYPE_HORS_SERIE = 'hors_serie';
    public const TYPE_RENCONTRE = 'rencontre';
    public const TYPE_AUTRE = 'autre';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'product_type',
        'sku',
        'price',
        'year',
        'issue_number',
        'event_date',
        'event_location',
        'is_active',
        'stock_quantity',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'event_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_MAGAZINE => 'Magazine',
            self::TYPE_HORS_SERIE => 'Hors-série',
            self::TYPE_RENCONTRE => 'Rencontre annuelle',
            self::TYPE_AUTRE => 'Autre',
        ];
    }

    public function getTypeLabel(): string
    {
        return self::getTypeOptions()[$this->product_type] ?? $this->product_type;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('product_type', $type);
    }

    public function scopeMagazines($query)
    {
        return $query->where('product_type', self::TYPE_MAGAZINE);
    }

    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    public static function findOrCreateMagazineForYear(int $year, float $price): self
    {
        $slug = "magazine-oreina-{$year}";

        return self::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => "Magazine Oreina {$year}",
                'description' => "Abonnement au magazine Oreina pour l'année {$year}",
                'product_type' => self::TYPE_MAGAZINE,
                'price' => $price,
                'year' => $year,
                'is_active' => $year >= date('Y'),
            ]
        );
    }
}
