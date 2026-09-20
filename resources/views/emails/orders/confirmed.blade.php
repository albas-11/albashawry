@component('mail::message')
# ✅ تم تأكيد الطلب

مرحبًا **{{ $order->shipping_name }}**،

يسعدنا إعلامك بأنه تم **تأكيد طلبك بنجاح** 🎉

## 📋 تفاصيل الطلب

**رقم الطلب:** {{ $order->order_number }}
**تاريخ الطلب:** {{ $order->created_at->format('d/m/Y') }}
**المبلغ الإجمالي:** {{ number_format($order->total, 2) }} درهم مغربي

### 🛒 المنتجات المطلوبة

@foreach($order->items as $item)
- {{ $item->product_name }} × {{ $item->quantity }} — {{ number_format($item->price * $item->quantity, 2) }} درهم
@endforeach

---

**المجموع الفرعي:** {{ number_format($order->subtotal, 2) }} ر.س
@if($order->discount > 0)
**الخصم:** -{{ number_format($order->discount, 2) }} ر.س
@endif
**رسوم الشحن:** {{ number_format($order->shipping_cost, 2) }} ر.س
**الإجمالي:** {{ number_format($order->total, 2) }} ر.س

## 📍 عنوان الشحن

{{ $order->shipping_address }}
{{ $order->shipping_city }}، {{ $order->shipping_zip }}
{{ $order->shipping_country }}

@component('mail::button', ['url' => route('orders.track', $order->id)])
عرض الطلب
@endcomponent

شكرًا لثقتك بنا 🌟

مع خالص التحيات،
{{ config('app.name') }}
@endcomponent
