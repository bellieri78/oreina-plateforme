<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LepidopteraOfMonth extends Model
{
    protected $table = 'lepidoptera_of_month';

    protected $fillable = [
        'scientific_name',
        'photographer',
        'photo_path',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('created_at');
    }

    public function photoUrl(): string
    {
        if (str_starts_with($this->photo_path, 'images/')) {
            return asset($this->photo_path);
        }
        return \Storage::disk('public')->url($this->photo_path);
    }
}
