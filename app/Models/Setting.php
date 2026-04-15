<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value'];

    /**
     * Get a setting value
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $parts = explode('.', $key);
        $group = $parts[0] ?? 'general';
        $settingKey = $parts[1] ?? $key;

        $cacheKey = "setting:{$group}:{$settingKey}";

        return Cache::remember($cacheKey, 3600, function () use ($group, $settingKey, $default) {
            $setting = self::where('group', $group)
                ->where('key', $settingKey)
                ->first();

            if (!$setting) {
                return $default;
            }

            $value = $setting->value;

            // Try to decode JSON
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }

            return $value;
        });
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, mixed $value): void
    {
        $parts = explode('.', $key);
        $group = $parts[0] ?? 'general';
        $settingKey = $parts[1] ?? $key;

        if (is_array($value)) {
            $value = json_encode($value);
        }

        self::updateOrCreate(
            ['group' => $group, 'key' => $settingKey],
            ['value' => $value]
        );

        // Clear cache
        Cache::forget("setting:{$group}:{$settingKey}");
    }

    /**
     * Get all settings for a group
     */
    public static function getGroup(string $group): array
    {
        return self::where('group', $group)
            ->pluck('value', 'key')
            ->map(function ($value) {
                $decoded = json_decode($value, true);
                return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
            })
            ->toArray();
    }

    /**
     * Set multiple settings at once
     */
    public static function setMany(string $group, array $settings): void
    {
        foreach ($settings as $key => $value) {
            self::set("{$group}.{$key}", $value);
        }
    }
}
