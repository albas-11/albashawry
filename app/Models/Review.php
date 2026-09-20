<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'rating',
        'title',
        'comment',
        'is_verified',
        'is_approved',
        'reviewable_id',
        'reviewable_type',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Polymorphic relation
    public function reviewable()
    {
        return $this->morphTo();
    }

    // Les scopes sont conservés mais facultatifs
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    // Accesseurs
    public function getStarsAttribute()
    {
        return str_repeat('⭐', $this->rating);
    }

    // Forcer l'approbation automatique à la création
    protected static function booted()
    {
        static::creating(function ($review) {
            $review->is_approved = true;
        });
    }
}
