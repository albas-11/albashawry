<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TapTransaction extends Model
{
    use HasFactory;

    // تحديد اسم الجدول صراحة لضمان التوافق مع ملف التهجير (Migration)
    protected $table = 'tap_transactions';

    // الحقول القابلة للإضافة الجماعية (Mass Assignment)
       protected $guarded = [];

    /**
     * علاقة الحركة بالطلب (Order)
     * كل حركة دفع تعود لطلب واحد
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}