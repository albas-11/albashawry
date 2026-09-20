<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'shipping_name',
        'shipping_email',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_country',
        'shipping_zip',
        'subtotal',
        'discount',
        'coupon_code',
        'shipping_cost',
        'tax',
        'total',
        'status',
        'payment_status',
        'payment_method',
    ];



    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];
     public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function otoShipment()
    {
        return $this->hasOne(OtoShipment::class);
    }
    public function tapTransaction()
    {
        return $this->hasOne(TapTransaction::class, 'order_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function eyeExamBookings()
    {
        return $this->hasMany(EyeExamBooking::class);
    }

    public static function generateOrderNumber()
    {
        return 'ORD-' . strtoupper(uniqid());
    }
}
