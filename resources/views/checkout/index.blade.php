@extends('layouts.app')

@section('title', __('Checkout'))

@section('content')
    <style>
        :root {
            --brand: #1b1e6c;
            --brand-700: #151856;
            --brand-50: #eef0ff;
            --ink: #0f172a;
        }

        .focus-brand:focus {
            outline: 2px solid var(--brand);
            outline-offset: 2px;
        }

        .card-soft {
            box-shadow: 0 1px 0 rgba(15, 23, 42, .03), 0 10px 30px rgba(15, 23, 42, .06);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #E5E7EB;
            border-radius: 20px;
        }
    </style>

    @php
        $discount = (float) ($appliedCoupon['discount'] ?? 0);
        $netSubtotal = (float) $subtotal - $discount;
        $tax = 0;
        $initialTotal = $netSubtotal;
    @endphp

    <div class="min-h-screen bg-gradient-to-b from-white via-white to-gray-50">

        {{-- Breadcrumb --}}
        <div class="bg-white/80 backdrop-blur border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <nav class="flex items-center space-x-1.5 text-xs text-gray-600">
                    <a href="{{ route('cart.index') }}" class="hover:text-[#1b1e6c] transition">@lang('message.Cart')</a>
                    <svg class="w-2.5 h-2.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-gray-900 font-semibold">@lang('message.Checkout')</span>
                </nav>
            </div>
        </div>
@if ($errors->any())
    <div class="alert alert-danger mb-4" style="background-color: #f8d7da; color: #842029; padding: 1rem; border-radius: 0.5rem;">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger mb-4" style="background-color: #f8d7da; color: #842029; padding: 1rem; border-radius: 0.5rem;">
        <strong>خطأ:</strong> {{ session('error') }}
    </div>
@endif
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <div class="flex items-baseline justify-between mb-8 border-b border-gray-100 pb-5">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">@lang('message.Checkout')</h1>
                    <p class="mt-1 text-xs text-gray-400 uppercase tracking-widest">
                        @lang('message.Information') <span class="mx-2 text-gray-300">•</span> @lang('message.Payment')
                    </p>
                </div>

                <span
                    class="text-[10px] font-extrabold uppercase tracking-[0.18em] text-[#1b1e6c] bg-[rgba(27,30,108,.06)] px-3 py-1 rounded-full">
                    {{ count($cart) }} @lang('message.items')
                </span>
            </div>

            <form method="POST" action="{{ route('checkout.process') }}" id="checkout-form">
                @csrf

                <input type="hidden" name="net_subtotal" value="{{ number_format($netSubtotal, 2, '.', '') }}">
                <input type="hidden" name="discount" value="{{ number_format($discount, 2, '.', '') }}">
                <input type="hidden" name="tax" value="{{ number_format($tax, 2, '.', '') }}">
                <input type="hidden" name="grand_total" id="grand-total-hidden"
                    value="{{ number_format($initialTotal, 2, '.', '') }}">

                <input type="hidden" name="cart_payload" value='@json($cart)'>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                    {{-- LEFT --}}
                    <div class="lg:col-span-7 space-y-6">

                        <section class="bg-white border border-gray-100 rounded-2xl p-6 card-soft">
                            <div class="flex items-center justify-between mb-5 border-b border-gray-100 pb-4">
                                <h2 class="text-sm sm:text-base font-black text-gray-900 uppercase tracking-widest">
                                    @lang('message.Shipping Information')
                                </h2>

                                <span class="text-[10px] font-extrabold uppercase tracking-[0.18em] text-gray-400">
                                    @lang('message.Required') *
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                                @foreach ([
                                        'name' => 'Full Name',
                                        'email' => 'Email',
                                        'phone' => 'Phone',
                                        'address' => 'Address',
                                        'city' => 'City',
                                        'state' => 'State / Region',
                                        'country' => 'Country',
                                        'zip' => 'ZIP / Postal Code',
                                    ] as $field => $label)
                                    <div class="{{ in_array($field, ['name', 'address']) ? 'md:col-span-2' : '' }}">
                                        <label for="{{ $field }}"
                                            class="block text-[11px] font-extrabold text-gray-500 uppercase tracking-widest mb-2">
                                            @lang('message.' . $label)
                                            @if (!in_array($field, ['state']))
                                                <span class="text-red-400">*</span>
                                            @endif
                                        </label>

                                        @if ($field === 'country')
                                           <select name="country" id="country" required
        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-0 focus:border-[#1b1e6c] text-sm transition-all outline-none text-gray-700 focus-brand">
    <option value="">@lang('message.Select Country')</option>
    @foreach ([
        // دول الخليج
        'Saudi Arabia' => 'SA',
        'UAE' => 'AE',
        'Kuwait' => 'KW',
        'Qatar' => 'QA',
        'Bahrain' => 'BH',
        'Oman' => 'OM',
        // باقي الدول العربية
        'Morocco' => 'MA',
        'Egypt' => 'EG',
        'Algeria' => 'DZ',
        'Tunisia' => 'TN',
        'Libya' => 'LY',
        'Sudan' => 'SD',
        'Palestine' => 'PS',
        'Jordan' => 'JO',
        'Lebanon' => 'LB',
        'Syria' => 'SY',
        'Iraq' => 'IQ',
        'Yemen' => 'YE',
        'Mauritania' => 'MR',
        'Djibouti' => 'DJ',
        'Somalia' => 'SO',
        'Comoros' => 'KM'
    ] as $c => $code)
        <option value="{{ $code }}"
            {{ old('country', auth()->user()->country ?? '') == $code ? 'selected' : '' }}>
            @lang('' . $c)
        </option>
    @endforeach
</select>
                                        @else
                                            <input
                                                type="{{ $field === 'email' ? 'email' : ($field === 'phone' ? 'tel' : 'text') }}"
                                                name="{{ $field }}" id="{{ $field }}"
                                                value="{{ old($field, auth()->user()->$field ?? '') }}"
                                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-0 focus:border-[#1b1e6c] text-sm transition-all outline-none placeholder:text-gray-300 text-gray-700 focus-brand"
                                                @if (!in_array($field, ['state'])) required @endif
                                                placeholder="@lang('message.Enter') @lang('message.' . $label)">
                                        @endif

                                        @error($field)
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>

                            {{-- Extra info --}}
                            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4">
                                    <div class="text-[10px] font-extrabold uppercase tracking-widest text-gray-500">
                                        <i class="fa-solid fa-truck-fast mr-2 text-gray-400"></i> @lang('message.Delivery note')
                                    </div>
                                    <p class="mt-2 text-xs text-gray-600">
                                        @lang('message.Please make sure your phone number is correct so the courier can reach you.')
                                    </p>
                                </div>
                                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4">
                                    <div class="text-[10px] font-extrabold uppercase tracking-widest text-gray-500">
                                        <i class="fa-solid fa-location-dot mr-2 text-gray-400"></i> @lang('message.Address tip')
                                    </div>
                                    <p class="mt-2 text-xs text-gray-600">
                                        @lang('message.Add building / apartment / landmark to avoid delivery delays.')
                                    </p>
                                </div>
                            </div>
                        </section>

                        {{-- Payment Method --}}
                        <section class="bg-white border border-gray-100 rounded-2xl p-6 card-soft">
                            <div class="flex items-center justify-between mb-5 border-b border-gray-100 pb-4">
                                <h2 class="text-sm sm:text-base font-black text-gray-900 uppercase tracking-widest">
                                    @lang('message.Payment Method')
                                </h2>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach ([['id' => 'payment_cod', 'val' => 'cod', 'label' => 'Cash on Delivery', 'desc' => 'Pay at your door', 'icon' => 'fa-truck'], ['id' => 'payment_card', 'val' => 'online', 'label' => 'Online Payment', 'desc' => 'Telr - Safe & encrypted', 'icon' => 'fa-credit-card']] as $payment)
                                    <label for="{{ $payment['id'] }}"
                                        class="relative flex flex-col p-5 border border-gray-200 rounded-2xl cursor-pointer hover:border-[#1b1e6c] transition-all has-[:checked]:border-[#1b1e6c] has-[:checked]:bg-[rgba(27,30,108,.04)] group">
                                        <input type="radio" name="payment_method" id="{{ $payment['id'] }}"
                                            value="{{ $payment['val'] }}" class="payment-method-radio sr-only"
                                            {{ old('payment_method', 'cod') === $payment['val'] ? 'checked' : '' }}>

                                        <div
                                            class="mb-4 w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50
                                                    group-has-[:checked]:bg-[#1b1e6c] group-has-[:checked]:text-white transition-colors">
                                            <i class="fa-solid {{ $payment['icon'] }} text-lg"></i>
                                        </div>

                                        <span class="font-black text-gray-900 text-sm">@lang('message.' . $payment['label'])</span>
                                        <span class="text-xs text-gray-500 mt-1">@lang('message.' . $payment['desc'])</span>

                                    </label>
                                @endforeach
                            </div>

                            {{-- Telr Info --}}
                            <div id="telr-info"
                                class="mt-4 hidden bg-[rgba(27,30,108,.06)] border border-[rgba(27,30,108,.15)] text-[#1b1e6c] px-4 py-3 rounded-2xl text-sm">
                                <div class="flex items-start">
                                    <i class="fa-solid fa-shield-halved mt-0.5 mr-3"></i>
                                    <div>
                                        <strong class="font-black">@lang('message.Secure Payment')</strong>
                                        <p class="text-xs mt-1">
                                            @lang('message.Your payment is processed securely').
                                            @lang('message.After confirming your order, you will be redirected to complete payment').
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- Shipping Method --}}
                        <section id="shipping-method-section"
                            class="bg-white border border-gray-100 rounded-2xl p-6 card-soft hidden">
                            <div class="flex items-center justify-between mb-5 border-b border-gray-100 pb-4">
                                <h2 class="text-sm sm:text-base font-black text-gray-900 uppercase tracking-widest">
                                    @lang('message.Shipping Method')
                                </h2>
                                <span class="text-[10px] font-extrabold uppercase tracking-[0.18em] text-gray-400">
                                    @lang('message.Online only')
                                </span>
                            </div>

                            <div id="shipping-options-loading" class="hidden">
                                <div class="flex items-center gap-3 text-gray-500 text-sm">
                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                    <span>@lang('message.Loading shipping options...')</span>
                                </div>
                            </div>

                            <div id="shipping-error"
                                class="hidden mt-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl text-sm">
                                @lang('message.Unable to load shipping options. Please try again.')
                            </div>

                            <div id="shipping-options-container" class="mt-4 grid gap-3"></div>

                            <p class="mt-4 text-xs text-gray-400">
                                @lang('message.Shipping options appear after you select Online Payment and fill City + Country.')
                            </p>
                        </section>

                    </div>

                    {{-- RIGHT / SUMMARY --}}
                    <div class="lg:col-span-5">
                        <div class="bg-white rounded-2xl p-6 border border-gray-100 card-soft sticky top-8">

                            {{-- Header --}}
                            <div class="flex items-center justify-between pb-5 border-b border-gray-100">
                                <h2 class="text-[11px] font-extrabold text-gray-400 uppercase tracking-[0.22em]">
                                    @lang('message.Your Order')
                                </h2>
                                <span
                                    class="text-[10px] font-extrabold uppercase tracking-[0.18em] text-[#1b1e6c] bg-[rgba(27,30,108,.06)] px-3 py-1 rounded-full">
                                    {{ count($cart) }} @lang('message.items')
                                </span>
                            </div>

                            {{-- Items --}}
                            <div class="mt-5 space-y-3 max-h-[420px] overflow-y-auto custom-scrollbar pr-1 no_scrollbar">

                                @foreach ($cart as $item)
                                    @php
                                        $image = $item['image'] ?? null;
                                        if ($image) {
                                            $image = ltrim($image, '/');
                                            if (str_starts_with($image, 'images/images/')) {
                                                $image = str_replace('images/images/', 'images/', $image);
                                            }
                                        }

                                        $unit = (float) ($item['final_price'] ?? ($item['price'] ?? 0));
                                        $qty = (int) ($item['quantity'] ?? 1);

                                        $lensPayload = null;
                                        if (!empty($item['lens_type'])) {
                                            $tmp = json_decode($item['lens_type'], true);
                                            if (json_last_error() === JSON_ERROR_NONE && is_array($tmp)) {
                                                $lensPayload = $tmp;
                                            }
                                        }

                                        $pretty = fn($v) => ucwords(str_replace(['_', '-'], ' ', (string) $v));

                                        $flowKey = null;
                                        $flowIcon = 'fa-circle-info';
                                        $flowTone =
                                            'bg-[rgba(27,30,108,.06)] text-[#1b1e6c] border-[rgba(27,30,108,.15)]';

                                        if ($lensPayload) {
                                            $flow = $lensPayload['flow'] ?? null;

                                            if ($flow === 'book_eye_test') {
                                                $flowKey = 'Book free eyetest';
                                                $flowIcon = 'fa-calendar-check';
                                                $flowTone = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                                            } elseif ($flow === 'add_prescription') {
                                                $flowKey = 'Add prescription';
                                                $flowIcon = 'fa-file-medical';
                                            } elseif ($flow === 'store_measurement') {
                                                $flowKey = 'In-store measurement';
                                                $flowIcon = 'fa-store';
                                                $flowTone = 'bg-violet-50 text-violet-700 border-violet-100';
                                            } else {
                                                $flowKey = $flow ? $pretty($flow) : null;
                                            }
                                        }

                                        $rx = $lensPayload['prescription'] ?? null;
                                        $hasRxUpload = !empty($rx['rx_file_path'] ?? null);
                                        $hasSchedule =
                                            !empty($lensPayload['store']) && !empty($lensPayload['schedule']);

                                        $addonName = $lensPayload['addon']['name'] ?? null;
                                        $addonOpt = $lensPayload['addon']['option'] ?? null;
                                        $addonPrice = (float) ($lensPayload['addon']['price'] ?? 0);

                                        $type = $item['type'] ?? 'product';
                                        $typeBadge = match ($type) {
                                            'contact_lens' => 'bg-green-100 text-green-700',
                                            'accessory' => 'bg-purple-100 text-purple-700',
                                            default => 'bg-blue-100 text-blue-700',
                                        };
                                        $typeLabel = match ($type) {
                                            'contact_lens' => __('message.Contact Lenses'),
                                            'accessory' => __('message.Accessories'),
                                            default => __('message.Eyeware'),
                                        };
                                    @endphp

                                    <div
                                        class="border border-gray-100 rounded-2xl p-4 bg-white hover:border-gray-200 transition">
                                        <div class="flex gap-4">

                                            {{-- Thumb --}}
                                            <div
                                                class="relative w-16 h-16 rounded-2xl overflow-hidden border border-gray-100 bg-gray-50 flex-shrink-0">
                                                <img src="{{ $image ? asset($image) : asset('images/placeholder.jpg') }}"
                                                    alt="{{ $item['name'] }}" class="w-full h-full object-cover"
                                                    loading="lazy"
                                                    onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                                                <span
                                                    class="absolute -top-1 -right-1 w-5 h-5 bg-gray-900 text-white text-[10px] flex items-center justify-center rounded-full font-extrabold">
                                                    {{ $qty }}
                                                </span>
                                            </div>

                                            {{-- Body --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div class="min-w-0">
                                                        <h4
                                                            class="text-[13px] sm:text-sm font-black text-gray-900 leading-snug truncate">
                                                            {{ $item['name'] }}
                                                        </h4>

                                                        <div class="mt-2 flex flex-wrap items-center gap-2">
                                                            <span
                                                                class="text-[9px] px-2 py-0.5 rounded-full {{ $typeBadge }} font-semibold uppercase tracking-wider">
                                                                {{ $typeLabel }}
                                                            </span>

                                                            @if (!empty($item['sku']))
                                                                <span
                                                                    class="text-[10px] text-gray-400 uppercase tracking-widest">
                                                                    @lang('message.SKU'): {{ $item['sku'] }}
                                                                </span>
                                                            @endif

                                                            @if ($lensPayload && $flowKey)
                                                                <span
                                                                    class="text-[10px] font-extrabold uppercase tracking-widest px-2 py-1 rounded-full border {{ $flowTone }}">
                                                                    <i
                                                                        class="fa-solid {{ $flowIcon }} mr-1"></i>@lang('message.' . $flowKey)
                                                                </span>
                                                            @endif

                                                            @if (($lensPayload['flow'] ?? null) === 'add_prescription' && $hasRxUpload)
                                                                <span
                                                                    class="text-[10px] font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-1 rounded-full">
                                                                    <i
                                                                        class="fa-solid fa-paperclip mr-1"></i>@lang('message.Uploaded')
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    {{-- Price --}}
                                                    <div class="text-right flex-shrink-0">
                                                        <div class="text-[13px] font-black text-[#1b1e6c] leading-none">
                                                            {{ number_format($unit * $qty, 2) }}
                                                            <span class="text-[10px] font-normal text-gray-400">SAR</span>
                                                        </div>
                                                        <div class="mt-1 text-[10px] text-gray-400">
                                                            {{ number_format($unit, 2) }} SAR × {{ $qty }}
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Lens compact block --}}
                                                @if ($lensPayload)
                                                    <div class="mt-3 bg-gray-50/70 border border-gray-100 rounded-2xl p-3">
                                                        <div class="flex flex-wrap gap-2">

                                                            @if (!empty($lensPayload['type']))
                                                                <span
                                                                    class="text-[10px] font-extrabold px-2 py-1 rounded-full bg-white border border-gray-200 text-gray-700">
                                                                    <i
                                                                        class="fa-solid fa-layer-group mr-1 text-gray-400"></i>{{ $pretty($lensPayload['type']) }}
                                                                </span>
                                                            @endif

                                                            @if (!empty($lensPayload['category']))
                                                                <span
                                                                    class="text-[10px] font-extrabold px-2 py-1 rounded-full bg-white border border-gray-200 text-gray-700">
                                                                    <i
                                                                        class="fa-solid fa-tag mr-1 text-gray-400"></i>{{ $pretty($lensPayload['category']) }}
                                                                </span>
                                                            @endif

                                                            @if (!empty($lensPayload['category_price']))
                                                                <span
                                                                    class="text-[10px] font-extrabold px-2 py-1 rounded-full bg-white border border-gray-200 text-gray-700">
                                                                    <i
                                                                        class="fa-solid fa-plus mr-1 text-gray-400"></i>{{ number_format((float) $lensPayload['category_price'], 2) }}
                                                                    SAR
                                                                </span>
                                                            @endif

                                                            @if ($addonName)
                                                                <span
                                                                    class="text-[10px] font-extrabold px-2 py-1 rounded-full bg-white border border-gray-200 text-gray-700">
                                                                    <i
                                                                        class="fa-solid fa-wand-magic-sparkles mr-1 text-gray-400"></i>
                                                                    {{ $addonName }} @if ($addonOpt)
                                                                        ({{ $pretty($addonOpt) }})
                                                                    @endif
                                                                </span>

                                                                @if ($addonPrice > 0)
                                                                    <span
                                                                        class="text-[10px] font-extrabold px-2 py-1 rounded-full bg-white border border-gray-200 text-gray-700">
                                                                        <i
                                                                            class="fa-solid fa-plus mr-1 text-gray-400"></i>{{ number_format($addonPrice, 2) }}
                                                                        SAR
                                                                    </span>
                                                                @endif
                                                            @endif
                                                        </div>

                                                        <div
                                                            class="mt-3 grid sm:grid-cols-2 gap-2 text-[11px] text-gray-700">
                                                            @if ($hasSchedule)
                                                                <div
                                                                    class="bg-white border border-gray-100 rounded-xl px-3 py-2">
                                                                    <div
                                                                        class="text-[10px] text-gray-400 uppercase tracking-widest font-extrabold">
                                                                        @lang('message.Store')</div>
                                                                    <div class="mt-1 font-semibold text-gray-800 truncate">
                                                                        {{ $lensPayload['store']['name'] ?? '—' }}
                                                                    </div>
                                                                    <div class="mt-1 text-gray-600 text-[11px]">
                                                                        <i
                                                                            class="fa-regular fa-calendar mr-1 text-gray-400"></i>{{ $lensPayload['schedule']['day'] ?? '—' }}
                                                                        <span class="mx-1 text-gray-300">•</span>
                                                                        <i
                                                                            class="fa-regular fa-clock mr-1 text-gray-400"></i>{{ $lensPayload['schedule']['slot'] ?? '—' }}
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            @if (($lensPayload['flow'] ?? null) === 'add_prescription')
                                                                <div
                                                                    class="bg-white border border-gray-100 rounded-xl px-3 py-2">
                                                                    <div
                                                                        class="text-[10px] text-gray-400 uppercase tracking-widest font-extrabold">
                                                                        @lang('message.Prescription')</div>
                                                                    <div class="mt-1">
                                                                        <span
                                                                            class="font-semibold">@lang('message.Mode'):</span>
                                                                        {{ $pretty($lensPayload['prescription_input'] ?? '—') }}
                                                                    </div>
                                                                    <div class="mt-1">
                                                                        <span class="font-semibold">PD:</span>
                                                                        {{ !empty($rx['pd'] ?? null) ? $rx['pd'] : '—' }}
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            @if (!empty($lensPayload['total']))
                                                                <div
                                                                    class="bg-white border border-gray-100 rounded-xl px-3 py-2">
                                                                    <div
                                                                        class="text-[10px] text-gray-400 uppercase tracking-widest font-extrabold">
                                                                        @lang('message.Lens Total')</div>
                                                                    <div class="mt-1 font-black text-[#1b1e6c]">
                                                                        {{ number_format((float) $lensPayload['total'], 2) }}
                                                                        <span class="text-[10px] font-normal">SAR</span>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Totals --}}
                            <div class="mt-6 pt-6 border-t border-gray-100 space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">@lang('message.Subtotal')</span>
                                    <span class="font-semibold text-gray-900">{{ number_format($subtotal, 2) }} SAR</span>
                                </div>

                                @if ($appliedCoupon)
                                    <div class="flex justify-between text-sm text-emerald-700">
                                        <span>@lang('message.Discount')</span>
                                        <span class="font-semibold">-{{ number_format($discount, 2) }} SAR</span>
                                    </div>
                                @endif

                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">@lang('message.Shipping')</span>
                                    <span id="shipping-cost-display"
                                        class="text-emerald-600 font-bold text-[10px] uppercase tracking-widest">
                                        @lang('message.Calculated at next step')
                                    </span>
                                </div>

                                <div class="pt-5 mt-2 border-t border-gray-100 flex items-baseline justify-between">
                                    <span class="text-base font-black text-gray-900">@lang('message.Total')</span>
                                    <div class="text-right leading-none">
                                        <span id="total-display" class="text-3xl font-black text-[#1b1e6c]">
                                            {{ number_format($initialTotal, 2) }}
                                        </span>
                                        <span class="text-xs font-extrabold text-[#1b1e6c] ml-1 uppercase">SAR</span>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="shipping_option_id" id="selected-shipping-option"
                                value="">
                            <input type="hidden" name="shipping_cost" id="selected-shipping-cost" value="0">

                            {{-- CTA --}}
                            <button type="submit" id="submit-button"
                                class="w-full mt-6 py-4 rounded-xl text-xs font-extrabold uppercase tracking-[0.2em]
                                       bg-[#1b1e6c] text-white hover:bg-[#151856] active:scale-[0.99]
                                       transition flex items-center justify-center gap-3 focus-brand disabled:opacity-50 disabled:cursor-not-allowed">
                                <span id="button-text">@lang('message.Place Order')</span>
                                <span id="button-loading" class="hidden">
                                    <i class="fa-solid fa-spinner fa-spin mr-2"></i>@lang('message.Processing')...
                                </span>
                            </button>

                            {{-- Footer trust --}}
                            <div class="mt-4 text-center text-[11px] text-gray-400">
                                <span
                                    class="inline-flex items-center gap-2 bg-gray-50 border border-gray-100 px-3 py-2 rounded-xl">
                                    <i class="fa-solid fa-lock text-gray-300"></i>
                                    <span class="font-semibold">@lang('message.Secure checkout')</span>
                                </span>
                            </div>

                            <div class="mt-5 flex items-center justify-center gap-4 text-gray-400 text-lg">
                                <i class="fa-brands fa-cc-visa"></i>
                                <i class="fa-brands fa-cc-mastercard"></i>
                                <i class="fa-brands fa-apple-pay"></i>
                            </div>

                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('checkout-form');
            const paymentRadios = document.querySelectorAll('.payment-method-radio');
            const telrInfo = document.getElementById('telr-info');

            const shippingSection = document.getElementById('shipping-method-section');
            const cityInput = document.getElementById('city');
            const countryInput = document.getElementById('country');

            const tCalculated = @json(__('message.Calculated at next step'));
            const tFree = @json(__('message.Free'));
            const tSelectShipping = @json(__('message.Please select a shipping option.'));

            let currentPaymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value ||
                'cod';

            function show(el) {
                if (el) el.classList.remove('hidden');
            }

            function hide(el) {
                if (el) el.classList.add('hidden');
            }

            if (currentPaymentMethod === 'online') {
                show(telrInfo);
                checkAndLoadShippingOptions();
            } else {
                hide(telrInfo);
                hide(shippingSection);
            }

            paymentRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    currentPaymentMethod = this.value;

                    if (this.value === 'online') {
                        show(telrInfo);
                        checkAndLoadShippingOptions();
                    } else {
                        hide(telrInfo);
                        hide(shippingSection);
                        updateShippingCost('', 0);

                        const costDisplay = document.getElementById('shipping-cost-display');
                        if (costDisplay) costDisplay.textContent = tCalculated;
                    }
                });
            });

            if (cityInput) {
                cityInput.addEventListener('blur', function() {
                    if (currentPaymentMethod === 'online' && this.value && countryInput?.value) {
                        checkAndLoadShippingOptions();
                    }
                });
            }

            if (countryInput) {
                countryInput.addEventListener('change', function() {
                    if (currentPaymentMethod === 'online' && this.value && cityInput?.value) {
                        checkAndLoadShippingOptions();
                    }
                });
            }

            function checkAndLoadShippingOptions() {
                const city = cityInput?.value;
                const country = countryInput?.value;

                if (!city || !country) return;

                show(shippingSection);
                loadShippingOptions(city, country);
            }

            function loadShippingOptions(city, country) {
                const loadingEl = document.getElementById('shipping-options-loading');
                const containerEl = document.getElementById('shipping-options-container');
                const errorEl = document.getElementById('shipping-error');

                show(loadingEl);
                hide(errorEl);
                if (containerEl) containerEl.innerHTML = '';

                const subtotal = {{ (float) $netSubtotal }};

                fetch('{{ route('shipping.calculate') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            city,
                            country,
                            order_total: subtotal,
                            payment_method: currentPaymentMethod
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        hide(loadingEl);

                        if (data?.success && Array.isArray(data.options) && data.options.length > 0) {
                            renderShippingOptions(data.options);
                        } else {
                            renderDefaultShipping();
                        }
                    })
                    .catch(() => {
                        hide(loadingEl);
                        renderDefaultShipping();
                    });
            }

            function renderShippingOptions(options) {
                const container = document.getElementById('shipping-options-container');
                if (!container) return;
                container.innerHTML = '';

                options.forEach((option, index) => {
                    const optionHtml = `
                        <label class="relative flex items-center p-4 border border-gray-200 rounded-2xl cursor-pointer hover:border-[#1b1e6c] transition-all has-[:checked]:border-[#1b1e6c] has-[:checked]:bg-[rgba(27,30,108,.04)] group">
                            <input type="radio" name="shipping_option"
                                value="${option.id}"
                                data-cost="${option.cost}"
                                class="shipping-option-radio sr-only" ${index === 0 ? 'checked' : ''}>

                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="font-black text-gray-900">${option.carrier_name}</span>
                                        <p class="text-xs text-gray-500 mt-1">${option.delivery_time}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-lg font-black text-gray-900">${Number(option.cost).toFixed(2)} SAR</span>
                                    </div>
                                </div>
                            </div>

                            <div class="ml-4 w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center group-has-[:checked]:border-[#1b1e6c]">
                                <div class="w-2 h-2 rounded-full bg-[#1b1e6c] scale-0 group-has-[:checked]:scale-100 transition-transform"></div>
                            </div>
                        </label>
                    `;
                    container.insertAdjacentHTML('beforeend', optionHtml);
                });

                updateShippingCost(options[0].id, Number(options[0].cost || 0));

                document.querySelectorAll('.shipping-option-radio').forEach(radio => {
                    radio.addEventListener('change', function() {
                        updateShippingCost(this.value, Number(this.dataset.cost || 0));
                    });
                });
            }

            function renderDefaultShipping() {
                const container = document.getElementById('shipping-options-container');
                if (!container) return;

                const defaultCost = {{ (float) settings('shipping_cost', 0) }};

                container.innerHTML = `
                    <div class="p-4 border border-gray-200 rounded-2xl bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-black text-gray-900">{{ __('message.Standard Shipping') }}</span>
                                <p class="text-xs text-gray-500 mt-1">{{ __('message.3-5 business days') }}</p>
                            </div>
                            <span class="text-lg font-black text-gray-900">${defaultCost.toFixed(2)} SAR</span>
                        </div>
                    </div>
                `;

                updateShippingCost('default', defaultCost);
            }

            function updateShippingCost(optionId, cost) {
                const optEl = document.getElementById('selected-shipping-option');
                const costEl = document.getElementById('selected-shipping-cost');
                if (optEl) optEl.value = optionId || '';
                if (costEl) costEl.value = String(cost || 0);

                const netSubtotal = {{ (float) $netSubtotal }};
                const total = netSubtotal + (cost || 0);

                const shippingDisplay = document.getElementById('shipping-cost-display');
                if (shippingDisplay) {
                    if ((cost || 0) === 0) {
                        shippingDisplay.innerHTML =
                            `<span class="text-emerald-600 font-bold text-[10px] uppercase tracking-widest">${tFree}</span>`;
                    } else {
                        shippingDisplay.textContent = (cost || 0).toFixed(2) + ' SAR';
                        shippingDisplay.classList.remove('text-emerald-600');
                        shippingDisplay.classList.add('font-semibold', 'text-gray-900');
                    }
                }

                const totalDisplay = document.getElementById('total-display');
                if (totalDisplay) totalDisplay.textContent = total.toFixed(2);

                const grandHidden = document.getElementById('grand-total-hidden');
                if (grandHidden) grandHidden.value = total.toFixed(2);
            }

            if (form) {
                form.addEventListener('submit', function(e) {
                    const payment = document.querySelector('input[name="payment_method"]:checked')?.value ||
                        'cod';

                    if (payment === 'online') {
                        const selectedShipping = document.getElementById('selected-shipping-option')?.value;
                        if (!selectedShipping) {
                            e.preventDefault();
                            alert(tSelectShipping);
                            return;
                        }
                    }

                    // loading state
                    const submitButton = document.getElementById('submit-button');
                    const buttonText = document.getElementById('button-text');
                    const buttonLoading = document.getElementById('button-loading');

                    if (submitButton) submitButton.disabled = true;
                    if (buttonText) buttonText.classList.add('hidden');
                    if (buttonLoading) buttonLoading.classList.remove('hidden');
                });
            }
        });
    </script>
@endsection
