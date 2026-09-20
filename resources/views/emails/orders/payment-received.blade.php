@component('mail::message')
# ✓ تم استلام الدفع

مرحبًا **{{ $order->shipping_name }}**،

نود إعلامك بأنه تم استلام دفعتك بنجاح بقيمة
**{{ number_format($order->total, 2) }} درهم مغربي**
وذلك مقابل الطلب رقم **#{{ $order->order_number }}**.

**💳 طريقة الدفع:** {{ ucfirst($order->payment_method) }}
**📅 تاريخ الدفع:** {{ $order->paid_at->format('d/m/Y H:i') }}

سيتم تجهيز طلبك في أقرب وقت ممكن.

@component('mail::button', ['url' => route('orders.show', $order->id)])
عرض الطلب
@endcomponent

شكرًا لك،
{{ config('app.name') }}
@endcomponent
