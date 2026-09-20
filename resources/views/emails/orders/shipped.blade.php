@component('mail::message')
# 📦 تم شحن الطلب

مرحبًا **{{ $order->shipping_name }}**،

خبر سار! 🎉
تم شحن طلبك رقم **#{{ $order->order_number }}** بنجاح.

**📅 تاريخ الشحن:**
{{ $order->shipped_at->format('d/m/Y H:i') }}

من المتوقع أن يصلك الطلب خلال **2 إلى 5 أيام عمل**.

@component('mail::button', ['url' => route('orders.show', $order->id)])
تتبع طلبي
@endcomponent

شكرًا لك،
{{ config('app.name') }}
@endcomponent
