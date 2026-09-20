<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_active',
        'configuration',
        'icon',
        'sort_order',
        'min_amount',
        'max_amount',
        'allowed_currencies'
    ];

    protected $casts = [
        'display_name' => 'array',
        'configuration' => 'array',
        'allowed_currencies' => 'array',
        'is_active' => 'boolean',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
    ];

    /**
     * Get translated display name
     */
    public function getTranslatedNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->display_name[$locale] ?? $this->display_name['en'] ?? $this->name;
    }

    /**
     * Check if payment method supports amount
     */
    public function supportsAmount(float $amount): bool
    {
        if ($this->min_amount && $amount < $this->min_amount) {
            return false;
        }

        if ($this->max_amount && $amount > $this->max_amount) {
            return false;
        }

        return true;
    }

    /**
     * Check if payment method supports currency
     */
    public function supportsCurrency(string $currency): bool
    {
        if (!$this->allowed_currencies) {
            return true; // No restriction
        }

        return in_array($currency, $this->allowed_currencies);
    }

    /**
     * Scope: Active methods
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Relationship: Payments
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
