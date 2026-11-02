<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class PlatformSetting extends Model
{
    use LogsActivity;

    protected $fillable = ['key', 'value', 'label', 'type', 'group'];

    /**
     * Get a setting value by key, with optional default.
     */
    public static function get(string $key, $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => in_array(strtolower($setting->value), ['1', 'true', 'yes']),
            'number' => (float) $setting->value,
            default => $setting->value,
        };
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value): void
    {
        static::where('key', $key)->update([
            'value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value,
        ]);
    }

    /**
     * Get all settings grouped by their group.
     */
    public static function grouped(): array
    {
        return static::orderBy('group')
            ->orderBy('key')
            ->get()
            ->groupBy('group')
            ->toArray();
    }
}
