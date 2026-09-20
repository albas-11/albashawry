<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelrTransaction extends Model
{
    protected $fillable = [
        'order_id',
        'telr_order_ref',
        'cart_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'card_last4',
        'response_data',
        'transaction_ref',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Relation avec Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Vérifier si payé
     */
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    /**
     * Obtenir les détails formatés
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }
}
