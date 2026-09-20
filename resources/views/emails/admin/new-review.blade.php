@component('mail::message')
# ⭐ تقييم جديد من عميل

قام أحد العملاء بترك **تقييم جديد** على متجرك.

## 📋 تفاصيل التقييم

**التقييم:** {{ $review->rating }}/5 ⭐
**العميل:** {{ $review->user->name }}
**رقم الطلب:** #{{ $review->order->order_number ?? 'غير متوفر' }}

@if($review->title)
### {{ $review->title }}
@endif

@component('mail::panel')
{{ $review->comment }}
@endcomponent

**📅 تاريخ التقييم:** {{ $review->created_at->format('d/m/Y H:i') }}

@component('mail::button', ['url' => route('admin.reviews.show', $review->id)])
عرض التقييم
@endcomponent

مع التحية،
نظام {{ config('app.name') }}
@endcomponent
