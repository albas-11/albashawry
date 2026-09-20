@component('mail::message')
# 🛒 تم استلام طلب جديد

تم تسجيل **طلب جديد** على متجرك بنجاح.

## 📋 تفاصيل الطلب

**رقم الطلب:** #{{ $order->order_number }}
**العميل:** {{ $order->shipping_name }}
**البريد الإلكتروني:** {{ $order->shipping_email }}
**رقم الهاتف:** {{ $order->shipping_phone }}
**المبلغ الإجمالي:** {{ number_format($order->total, 2) }} درهم مغربي

### 🧾 المنتجات المطلوبة

@foreach($order->items as $item)
- **{{ $item->product_name }}** × {{ $item->quantity }} = {{ number_format($item->price * $item->quantity, 2) }} درهم
@endforeach

---

**المجموع الفرعي:** {{ number_format($order->subtotal, 2) }} درهم
@if($order->discount > 0)
**الخصم:** -{{ number_format($order->discount, 2) }} درهم
@endif
**رسوم التوصيل:** {{ number_format($order->shipping_cost, 2) }} درهم
**الإجمالي:** {{ number_format($order->total, 2) }} درهم

## 📍 عنوان الشحن

{{ $order->shipping_address }}
{{ $order->shipping_city }}، {{ $order->shipping_zip }}
{{ $order->shipping_country }}

@component('mail::button', ['url' => route('admin.orders.show', $order->id)])
عرض الطلب
@endcomponent

**⚠️ إجراء مطلوب:**
يرجى معالجة هذا الطلب في أقرب وقت ممكن.

مع التحية،
نظام {{ config('app.name') }}
@endcomponent
