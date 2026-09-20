<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EyeExamBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'order_item_id',
        'store_id',
        'product_id',
        'product_name',
        'customer_name',
        'customer_phone',
        'flow',
        'booking_date',
        'booking_time',
        'store_name',
        'status',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getStoreLabelAttribute(): ?string
    {
        if ($this->store) {
            $name = $this->store->name;
            if (is_array($name)) {
                return $name[app()->getLocale()] ?? reset($name) ?: null;
            }
            return $name;
        }

        return $this->store_name;
    }
}
