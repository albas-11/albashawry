<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtoShipment extends Model
{
    protected $fillable = [
        'order_id',
        'oto_id',
        'shipment_id',
        'order_code',
        'delivery_option_id',
        'carrier_name',
        'weight',
        'shipping_cost',
        'currency',
        'status',
        'label_url',
        'tracking_url',
        'destination_address',
        'delivery_options',
        'response_data',
        'picked_at',
        'delivered_at',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'delivery_options' => 'array',
        'picked_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'secondary',
            'created' => 'info',
            'confirmed' => 'primary',
            'picked_up' => 'primary',
            'in_transit' => 'warning',
            'out_for_delivery' => 'warning',
            'delivered' => 'success',
            'cancelled' => 'danger',
            'failed' => 'danger',
            'returned' => 'dark',
        ];

        $class = $badges[$this->status] ?? 'secondary';
        return '<span class="badge bg-' . $class . '">' . ucfirst(str_replace('_', ' ', $this->status)) . '</span>';
    }
}
