<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPrescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'order_item_id',

        'patient_name',
        'od_sphere',
        'od_cyl',
        'od_axis',
        'os_sphere',
        'os_cyl',
        'os_axis',
        'pd',
        'pd_unknown',

        'rx_file_path',
        'rx_file_mime',
    ];

    protected $casts = [
        'pd_unknown' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function item()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }
}
