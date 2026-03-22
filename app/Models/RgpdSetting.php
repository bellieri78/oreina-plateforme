<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class RgpdSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    protected $casts = [
        'value' => 'integer',
    ];

    /**
     * Get a setting value by key.
     */
    public static function getValue(string $key, int $default = 0): int
    {
        return Cache::remember("rgpd_setting.{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value.
     */
    public static function setValue(string $key, int $value): bool
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("rgpd_setting.{$key}");

        return $setting->wasRecentlyCreated || $setting->wasChanged();
    }

    /**
     * Get all settings as array.
     */
    public static function getAllSettings(): array
    {
        return static::pluck('value', 'key')->toArray();
    }
}
