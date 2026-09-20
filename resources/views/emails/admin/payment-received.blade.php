@component('mail::message')
# 💰 تم استلام الدفع

تم تنفيذ عملية دفع **بنجاح**.

## 📋 تفاصيل الدفع

**رقم الطلب:** #{{ $order->order_number }}
**العميل:** {{ $order->shipping_name }}
**المبلغ:** {{ number_format($order->total, 2) }} درهم مغربي
**طريقة الدفع:** {{ ucfirst($order->payment_method) }}
**تاريخ الدفع:** {{ $order->paid_at->format('d/m/Y H:i') }}

@component('mail::panel')
✅ تم تأكيد الدفع. يمكنك الآن البدء في تجهيز الطلب.
@endcomponent

@component('mail::button', ['url' => route('admin.orders.show', $order->id)])
عرض الطلب
@endcomponent

مع التحية،
نظام {{ config('app.name') }}
@endcomponent
