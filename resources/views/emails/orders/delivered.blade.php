@component('mail::message')
# ✅ تم تسليم الطلب

مرحبًا **{{ $order->shipping_name }}**،

يسعدنا إعلامك بأن طلبك رقم **#{{ $order->order_number }}**
تم تسليمه بنجاح 🎉

**📦 تاريخ التسليم:**
{{ $order->delivered_at->format('d/m/Y H:i') }}

نأمل أن تكون راضيًا عن مشترياتك.

@component('mail::button', ['url' => route('reviews.create', $order->id)])
ترك تقييم
@endcomponent

شكرًا لثقتك بنا 🌟

{{ config('app.name') }}
@endcomponent
