<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Setting keys whose values are encrypted at rest.
     */
    private const ENCRYPTED_KEYS = [
        'shopify_access_token',
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        $setting = static::query()->where('key', $key)->first();

        if (! $setting || blank($setting->value)) {
            return $default;
        }

        if (in_array($key, self::ENCRYPTED_KEYS, true)) {
            try {
                return Crypt::decryptString($setting->value);
            } catch (\Exception) {
                return $default;
            }
        }

        return $setting->value;
    }

    public static function set(string $key, ?string $value): void
    {
        $stored = $value;

        if (! blank($value) && in_array($key, self::ENCRYPTED_KEYS, true)) {
            $stored = Crypt::encryptString($value);
        }

        static::query()->updateOrCreate(['key' => $key], ['value' => $stored]);
    }
}
