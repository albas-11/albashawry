<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Les champs sensibles à encrypter
     */
    protected $encrypted = [
        'smtp_password',
        'telr_auth_key',
        'aramex_password',
    ];

    /**
     * Get the settings instance (singleton pattern)
     */
    public static function getInstance()
    {
        return static::first() ?? static::create([]);
    }

    /**
     * Encrypt sensitive data before saving
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encrypted) && !empty($value)) {
            $value = Crypt::encryptString($value);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Decrypt sensitive data when retrieving
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->encrypted) && !empty($value)) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return null;
            }
        }

        return $value;
    }

    /**
     * Get logo URL
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : asset('images/default-logo.png');
    }

    /**
     * Get favicon URL
     */
    public function getFaviconUrlAttribute()
    {
        return $this->favicon ? asset('storage/' . $this->favicon) : asset('images/default-favicon.png');
    }

    /**
     * Check if free shipping applies
     */
    public function hasFreeShipping($orderTotal)
    {
        return $this->free_shipping_threshold > 0 && $orderTotal >= $this->free_shipping_threshold;
    }

    /**
     * Get shipping cost for order
     */
    public function getShippingCost($orderTotal)
    {
        return $this->hasFreeShipping($orderTotal) ? 0 : $this->shipping_cost;
    }
}
