@extends('layouts.app')

@section('title', __('Shopping Cart'))

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
    </style>

    <div class="min-h-screen bg-gradient-to-b from-white via-white to-gray-50">

        {{-- Breadcrumb --}}
        <div class="bg-white/80 backdrop-blur border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <nav class="flex items-center space-x-1.5 text-xs text-gray-600">
                    <a href="{{ route('home') }}" class="hover:text-[#1b1e6c] transition">@lang('message.Home')</a>
                    <svg class="w-2.5 h-2.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-gray-900 font-semibold">@lang('message.Shopping Cart')</span>
                </nav>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <div class="flex items-baseline justify-between mb-8 border-b border-gray-100 pb-5">
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">@lang('message.Shopping Cart')</h1>
                <span
                    class="text-[10px] font-extrabold uppercase tracking-[0.18em] text-[#1b1e6c] bg-[rgba(27,30,108,.06)] px-3 py-1 rounded-full">
                    {{ count($cart) }} @lang('message.items')
                </span>
            </div>

            @if (empty($cart))
                <div class="py-20 text-center bg-white rounded-2xl border border-dashed border-gray-200 card-soft">
                    <div
                        class="w-20 h-20 mx-auto mb-6 bg-white rounded-full flex items-center justify-center border border-gray-100 card-soft">
                        <i class="fa-solid fa-cart-shopping text-2xl text-gray-200"></i>
                    </div>
                    <h3 class="text-lg font-black text-gray-900 mb-2">@lang('message.Your cart is empty')</h3>
                    <p class="text-xs text-gray-400 mb-8 uppercase tracking-widest">
                        @lang('message.Continue Add some products to get started')
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center gap-3">
                        <a href="{{ route('products.eyeglasses') }}"
                            class="px-8 py-3 bg-[#1b1e6c] text-white text-[11px] font-extrabold uppercase tracking-[0.2em]
                              hover:bg-[#151856] active:scale-[0.99] transition rounded-xl focus-brand">
                            @lang('message.Shop eyeglasses')
                        </a>
                        <a href="{{ route('products.sunglasses') }}"
                            class="px-8 py-3 border border-[#1b1e6c] text-[#1b1e6c] text-[11px] font-extrabold uppercase tracking-[0.2em]
                              hover:bg-[#1b1e6c] hover:text-white active:scale-[0.99] transition rounded-xl focus-brand">
                            @lang('message.Shop sunglasses')
                        </a>
                    </div>
                </div>
            @else
                <div class="flex flex-col lg:flex-row gap-8">

                    {{-- Products --}}
                    <div class="lg:basis-[65%] space-y-4">
                        @foreach ($cart as $cartKey => $item)
                            @php
                                // Unit price / qty / line total
                                $unitPrice = (float) ($item['final_price'] ?? ($item['price'] ?? 0));
                                $qty = (int) ($item['quantity'] ?? 1);
                                $lineTotal = $unitPrice * $qty;

                                // Image fix
                                $image = $item['image'] ?? null;
                                if ($image) {
                                    $image = ltrim($image, '/');
                                    if (str_starts_with($image, 'images/images/')) {
                                        $image = str_replace('images/images/', 'images/', $image);
                                    }
                                }

                                // Detect lens payload JSON
                                $lensPayload = null;
                                if (!empty($item['lens_type'])) {
                                    $tmp = json_decode($item['lens_type'], true);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($tmp)) {
                                        $lensPayload = $tmp;
                                    }
                                }

                                $pretty = function ($v) {
                                    return ucwords(str_replace(['_', '-'], ' ', (string) $v));
                                };

                                // Flow label
                                $flowLabelKey = null;
                                $flowIcon = 'fa-circle-info';
                                $flowTone = 'bg-[rgba(27,30,108,.06)] text-[#1b1e6c] border-[rgba(27,30,108,.15)]';

                                if ($lensPayload) {
                                    $flow = $lensPayload['flow'] ?? null;

                                    if ($flow === 'book_eye_test') {
                                        $flowLabelKey = 'Book free eyetest';
                                        $flowIcon = 'fa-calendar-check';
                                        $flowTone = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                                    } elseif ($flow === 'add_prescription') {
                                        $flowLabelKey = 'Add prescription';
                                        $flowIcon = 'fa-file-medical';
                                        $flowTone =
                                            'bg-[rgba(27,30,108,.06)] text-[#1b1e6c] border-[rgba(27,30,108,.15)]';
                                    } elseif ($flow === 'store_measurement') {
                                        $flowLabelKey = 'In-store measurement';
                                        $flowIcon = 'fa-store';
                                        $flowTone = 'bg-violet-50 text-violet-700 border-violet-100';
                                    } else {
                                        $flowLabelKey = $flow ? $pretty($flow) : null;
                                    }
                                }

                                $rx = $lensPayload['prescription'] ?? null;
                                $hasRxUpload = !empty($rx['upload_preview'] ?? null);

                                $hasSchedule = !empty($lensPayload['store']) && !empty($lensPayload['schedule']);
                            @endphp

                            <div class="bg-white rounded-2xl border border-gray-100 card-soft overflow-hidden"
                                data-cart-key="{{ $cartKey }}" data-stock="{{ (int) ($item['stock'] ?? 999999) }}"
                                data-unit-price="{{ number_format((float) $unitPrice, 2, '.', '') }}">
                                <div class="flex flex-col sm:flex-row gap-5 p-5">

                                    {{-- Image --}}
                                    <div class="w-full sm:w-40 md:w-44 lg:w-40 xl:w-44 flex-shrink-0">
                                        <div
                                            class="relative w-full aspect-[4/5] bg-gray-50 rounded-2xl overflow-hidden border border-gray-100">
                                            <img src="{{ $image ? asset($image) : asset('images/placeholder.jpg') }}"
                                                alt="{{ $item['name'] }}"
                                                class="absolute inset-0 w-full h-full object-contain p-2" loading="lazy"
                                                onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                                        </div>
                                    </div>

                                    {{-- Info --}}
                                    <div class="flex-1 flex flex-col">

                                        <div class="flex justify-between items-start gap-3">
                                            <div class="min-w-0">
                                                <h3
                                                    class="font-black text-gray-900 text-base sm:text-lg leading-tight mb-1 truncate">
                                                    {{ $item['name'] }}
                                                </h3>

                                                <div class="flex flex-wrap items-center gap-2">
                                                    @if ($item['type'] === 'product')
                                                        <p class="text-[10px] text-gray-400 uppercase tracking-widest">
                                                            @lang('message.SKU'): {{ $item['sku'] ?? '—' }}
                                                        </p>
                                                    @endif

                                                    <span
                                                        class="text-[9px] px-2 py-0.5 rounded-full
                                                    {{ $item['type'] === 'product' ? 'bg-blue-100 text-blue-700' : '' }}
                                                    {{ $item['type'] === 'contact_lens' ? 'bg-green-100 text-green-700' : '' }}
                                                    {{ $item['type'] === 'accessory' ? 'bg-purple-100 text-purple-700' : '' }}
                                                    font-semibold uppercase tracking-wider">
                                                        @if ($item['type'] === 'product')
                                                            @lang('message.Eyeware')
                                                        @elseif($item['type'] === 'contact_lens')
                                                            @lang('message.Contact Lenses')
                                                        @else
                                                            @lang('message.Accessories')
                                                        @endif
                                                    </span>

                                                    {{-- Flow chip --}}
                                                    @if ($lensPayload && $flowLabelKey)
                                                        <span
                                                            class="text-[10px] font-extrabold uppercase tracking-widest px-2 py-1 rounded-full border {{ $flowTone }}">
                                                            <i class="fa-solid {{ $flowIcon }} mr-1"></i>
                                                            @lang('message.' . $flowLabelKey)
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <button type="button" onclick="removeFromCart('{{ $cartKey }}')"
                                                class="h-10 w-10 inline-flex items-center justify-center rounded-xl
                                                border border-gray-200 text-gray-400 bg-white
                                                hover:border-red-200 hover:bg-red-50 hover:text-red-600
                                                active:scale-[0.98] transition
                                                focus:outline-none focus:ring-4 focus:ring-red-100"
                                                aria-label="@lang('message.Remove')">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>

                                        </div>

                                        {{-- Lens UI --}}
                                        @if ($lensPayload)
                                            <div class="mt-4 border border-gray-100 bg-gray-50/70 rounded-2xl p-4">

                                                {{-- chips --}}
                                                <div class="flex flex-wrap gap-2">
                                                    @if (!empty($lensPayload['type']))
                                                        <span
                                                            class="text-[10px] font-extrabold px-2 py-1 rounded-full bg-white border border-gray-200 text-gray-700">
                                                            <i class="fa-solid fa-layer-group mr-1 text-gray-400"></i>
                                                            {{ $pretty($lensPayload['type']) }}
                                                        </span>
                                                    @endif

                                                    @if (!empty($lensPayload['category']))
                                                        <span
                                                            class="text-[10px] font-extrabold px-2 py-1 rounded-full bg-white border border-gray-200 text-gray-700">
                                                            <i class="fa-solid fa-tag mr-1 text-gray-400"></i>
                                                            {{ $pretty($lensPayload['category']) }}
                                                        </span>
                                                    @endif

                                                    @if (!empty($lensPayload['category_price']))
                                                        <span
                                                            class="text-[10px] font-extrabold px-2 py-1 rounded-full bg-white border border-gray-200 text-gray-700">
                                                            <i class="fa-solid fa-plus mr-1 text-gray-400"></i>
                                                            {{ number_format((float) $lensPayload['category_price'], 2) }}
                                                            SAR
                                                        </span>
                                                    @endif

                                                    @if (!empty($lensPayload['addon']['name'] ?? null))
                                                        @php
                                                            $addonName = $lensPayload['addon']['name'] ?? '';
                                                            $addonOpt = $lensPayload['addon']['option'] ?? null;
                                                            $addonPrice = (float) ($lensPayload['addon']['price'] ?? 0);
                                                        @endphp

                                                        <span
                                                            class="text-[10px] font-extrabold px-2 py-1 rounded-full bg-white border border-gray-200 text-gray-700">
                                                            <i
                                                                class="fa-solid fa-wand-magic-sparkles mr-1 text-gray-400"></i>
                                                            {{ $addonName }}
                                                            @if ($addonOpt)
                                                                ({{ $pretty($addonOpt) }})
                                                            @endif
                                                        </span>

                                                        @if ($addonPrice > 0)
                                                            <span
                                                                class="text-[10px] font-extrabold px-2 py-1 rounded-full bg-white border border-gray-200 text-gray-700">
                                                                <i class="fa-solid fa-plus mr-1 text-gray-400"></i>
                                                                {{ number_format($addonPrice, 2) }} SAR
                                                            </span>
                                                        @endif
                                                    @endif
                                                </div>

                                                <div class="mt-4 grid grid-cols-1 gap-3 text-[11px]">

                                                    {{-- Store + schedule --}}
                                                    @if ($hasSchedule)
                                                        <div class="bg-white border border-gray-100 rounded-xl p-3 w-full">
                                                            <div
                                                                class="text-[10px] text-gray-400 uppercase tracking-widest font-extrabold">
                                                                @lang('message.Store')
                                                            </div>
                                                            <div class="mt-1 font-semibold text-gray-800">
                                                                {{ $lensPayload['store']['name'] ?? '—' }}
                                                            </div>
                                                            <div class="mt-1 text-gray-600">
                                                                <i class="fa-regular fa-calendar mr-1 text-gray-400"></i>
                                                                {{ $lensPayload['schedule']['day'] ?? '—' }}
                                                                <span class="mx-1 text-gray-300">•</span>
                                                                <i class="fa-regular fa-clock mr-1 text-gray-400"></i>
                                                                {{ $lensPayload['schedule']['slot'] ?? '—' }}
                                                            </div>
                                                        </div>
                                                    @endif

                                                    {{-- Prescription full details --}}
                                                    @if (($lensPayload['flow'] ?? null) === 'add_prescription')
                                                        <div class="bg-white border border-gray-100 rounded-xl p-3 w-full">
                                                            <div class="flex items-center justify-between">
                                                                <div
                                                                    class="text-[10px] text-gray-400 uppercase tracking-widest font-extrabold">
                                                                    @lang('message.Prescription')
                                                                </div>

                                                                @if ($hasRxUpload)
                                                                    <span
                                                                        class="text-[10px] font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-full">
                                                                        <i class="fa-solid fa-paperclip mr-1"></i>
                                                                        @lang('message.Uploaded')
                                                                    </span>
                                                                @endif
                                                            </div>

                                                            <div class="mt-2 text-gray-700">
                                                                <span class="font-semibold">@lang('message.Mode'):</span>
                                                                {{ $pretty($lensPayload['prescription_input'] ?? '—') }}
                                                            </div>

                                                            @if (!empty($rx['patient_name'] ?? null))
                                                                <div class="mt-1 text-gray-700">
                                                                    <span class="font-semibold">@lang('message.Name'):</span>
                                                                    {{ $rx['patient_name'] }}
                                                                </div>
                                                            @endif

                                                            <div class="mt-1 text-gray-700">
                                                                <span class="font-semibold">PD:</span>
                                                                {{ !empty($rx['pd'] ?? null) ? $rx['pd'] : '—' }}
                                                                @if (!empty($rx['pd_unknown'] ?? null))
                                                                    <span
                                                                        class="ml-1 text-[10px] text-gray-500">(@lang('message.Unknown'))</span>
                                                                @endif
                                                            </div>

                                                            <div
                                                                class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3 text-[11px]">
                                                                <div
                                                                    class="border border-gray-100 rounded-xl p-3 bg-gray-50">
                                                                    <div
                                                                        class="text-[10px] font-extrabold text-gray-500 uppercase tracking-widest mb-2">
                                                                        @lang('message.Right (OD)')
                                                                    </div>
                                                                    <div class="space-y-1 text-gray-700">
                                                                        <div><span
                                                                                class="font-semibold">@lang('message.Sphere'):</span>
                                                                            {{ $rx['od']['sphere'] ?? '—' }}</div>
                                                                        <div><span
                                                                                class="font-semibold">@lang('message.CYL'):</span>
                                                                            {{ $rx['od']['cyl'] ?? '—' }}</div>
                                                                        <div><span
                                                                                class="font-semibold">@lang('message.Axis'):</span>
                                                                            {{ $rx['od']['axis'] ?? '—' }}</div>
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    class="border border-gray-100 rounded-xl p-3 bg-gray-50">
                                                                    <div
                                                                        class="text-[10px] font-extrabold text-gray-500 uppercase tracking-widest mb-2">
                                                                        @lang('message.Left (OS)')
                                                                    </div>
                                                                    <div class="space-y-1 text-gray-700">
                                                                        <div><span
                                                                                class="font-semibold">@lang('message.Sphere'):</span>
                                                                            {{ $rx['os']['sphere'] ?? '—' }}</div>
                                                                        <div><span
                                                                                class="font-semibold">@lang('message.CYL'):</span>
                                                                            {{ $rx['os']['cyl'] ?? '—' }}</div>
                                                                        <div><span
                                                                                class="font-semibold">@lang('message.Axis'):</span>
                                                                            {{ $rx['os']['axis'] ?? '—' }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                </div>

                                            </div>
                                        @endif

                                        {{-- Price --}}
                                        <div class="mt-4">
                                            @if (!empty($item['has_discount']))
                                                <div class="flex items-center gap-2 text-sm">
                                                    <span class="font-black text-[#1b1e6c]">
                                                        {{ number_format($unitPrice, 2) }} SAR
                                                    </span>
                                                    <span class="text-gray-400 line-through text-xs">
                                                        {{ number_format((float) ($item['original_price'] ?? 0), 2) }} SAR
                                                    </span>
                                                </div>
                                            @else
                                                <div class="flex items-center gap-2 text-sm">
                                                    <span class="font-black text-[#1b1e6c]">
                                                        {{ number_format($unitPrice, 2) }} SAR
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Qty + totals --}}
                                        <div class="mt-auto pt-5">
                                            <div class="flex items-center justify-between gap-4">
                                                <div
                                                    class="flex items-center border border-gray-200 rounded-xl h-10 bg-white overflow-hidden">
                                                    <button type="button" onclick="changeQty('{{ $cartKey }}', -1)"
                                                        class="px-4 text-gray-500 hover:text-[#1b1e6c] hover:bg-[rgba(27,30,108,.06)] h-full transition">
                                                        -
                                                    </button>

                                                    <span id="qty-{{ $cartKey }}"
                                                        class="w-10 text-center text-xs font-black text-gray-800">
                                                        {{ $qty }}
                                                    </span>

                                                    <button type="button" onclick="changeQty('{{ $cartKey }}', +1)"
                                                        class="px-4 text-gray-500 hover:text-[#1b1e6c] hover:bg-[rgba(27,30,108,.06)] h-full transition">
                                                        +
                                                    </button>
                                                </div>

                                                <div class="text-right">
                                                    <p id="lineTotalTop-{{ $cartKey }}"
                                                        class="text-sm font-black text-[#1b1e6c]">
                                                        {{ number_format($lineTotal, 2) }}
                                                        <span class="text-[10px] font-normal">SAR</span>
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <div
                                                    class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-gray-50 border border-gray-100 rounded-2xl p-4">
                                                    <div>
                                                        <div
                                                            class="text-[10px] text-gray-400 font-extrabold uppercase tracking-widest">
                                                            @lang('message.Unit price')
                                                        </div>
                                                        <div class="mt-1 font-black text-[#1b1e6c]">
                                                            {{ number_format($unitPrice, 2) }}
                                                            <span class="text-[10px] font-normal">SAR</span>
                                                        </div>
                                                    </div>

                                                    <div class="sm:text-center">
                                                        <div
                                                            class="text-[10px] text-gray-400 font-extrabold uppercase tracking-widest">
                                                            @lang('message.Qty')
                                                        </div>
                                                        <div class="mt-1 text-base font-black text-gray-900">
                                                            × <span
                                                                id="qtyGrid-{{ $cartKey }}">{{ $qty }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="sm:text-right">
                                                        <div
                                                            class="text-[10px] text-gray-400 font-extrabold uppercase tracking-widest">
                                                            @lang('message.Line total')
                                                        </div>
                                                        <div id="lineTotalGrid-{{ $cartKey }}"
                                                            class="mt-1 text-lg font-black text-[#1b1e6c]">
                                                            {{ number_format($lineTotal, 2) }}
                                                            <span class="text-[10px] font-normal">SAR</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Summary --}}
                    <div class="lg:basis-[35%]">
                        <div class="bg-white p-6 rounded-2xl border border-gray-100 card-soft sticky top-8">
                            <h2 class="text-[11px] font-extrabold text-gray-400 uppercase tracking-[0.2em] mb-6">
                                @lang('message.Order Summary')
                            </h2>

                            <div class="space-y-3 pb-6 border-b border-gray-100">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">@lang('message.Subtotal')</span>
                                    <span id="summarySubtotal"
                                        class="font-semibold text-gray-900">{{ number_format($total, 2) }} SAR</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">@lang('message.Shipping')</span>
                                    <span class="text-emerald-600 font-bold text-[10px] uppercase tracking-widest">
                                        @lang('message.Calculated at next step')
                                    </span>
                                </div>
                            </div>

                            <div class="py-6 flex justify-between items-baseline">
                                <span class="text-base font-black text-gray-900">@lang('message.Total')</span>
                                <div class="text-right">
                                    <span id="summaryTotal"
                                        class="text-3xl font-black text-[#1b1e6c]">{{ number_format($total, 2) }}</span>
                                    <span class="text-xs font-extrabold text-[#1b1e6c] ml-1">SAR</span>
                                </div>
                            </div>

                            <button onclick="window.location.href='{{ route('checkout.index') }}'"
                                class="w-full py-4 rounded-xl text-xs font-extrabold uppercase tracking-[0.2em]
                                       bg-[#1b1e6c] text-white hover:bg-[#151856] active:scale-[0.99]
                                       transition flex items-center justify-center gap-3 focus-brand">
                                @lang('message.Proceed to Checkout')
                                <i class="fa-solid fa-arrow-right"></i>
                            </button>

                            <div class="mt-5 flex items-center justify-center gap-4 text-gray-400 text-lg">
                                <i class="fa-brands fa-cc-visa"></i>
                                <i class="fa-brands fa-cc-mastercard"></i>
                                <i class="fa-brands fa-apple-pay"></i>
                            </div>

                            <div class="mt-4 text-center text-[11px] text-gray-400">
                                <span
                                    class="inline-flex items-center gap-2 bg-gray-50 border border-gray-100 px-3 py-2 rounded-xl">
                                    <i class="fa-solid fa-lock text-gray-300"></i>
                                    <span class="font-semibold">@lang('message.Secure checkout')</span>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });

        const pending = new Set();

        function formatMoney(num) {
            const n = Number(num || 0);
            return n.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function getCard(cartKey) {
            return document.querySelector(`[data-cart-key="${cartKey}"]`);
        }

        function getQty(cartKey) {
            const el = document.getElementById(`qty-${cartKey}`);
            return el ? Number(el.textContent.trim()) : 1;
        }

        function setQtyUI(cartKey, qty) {
            const el = document.getElementById(`qty-${cartKey}`);
            if (el) el.textContent = qty;

            const gridEl = document.getElementById(`qtyGrid-${cartKey}`);
            if (gridEl) gridEl.textContent = qty;
        }

        function setLineTotalUI(cartKey, total) {
            const html = `${formatMoney(total)} <span class="text-[10px] font-normal">SAR</span>`;

            const top = document.getElementById(`lineTotalTop-${cartKey}`);
            if (top) top.innerHTML = html;

            const grid = document.getElementById(`lineTotalGrid-${cartKey}`);
            if (grid) grid.innerHTML = html;
        }

        function recomputeSummaryFromUI() {
            let sum = 0;
            document.querySelectorAll('[data-cart-key]').forEach(card => {
                const key = card.getAttribute('data-cart-key');
                const unit = Number(card.getAttribute('data-unit-price') || 0);
                const qty = getQty(key);
                sum += unit * qty;
            });

            const totalEl = document.getElementById('summaryTotal');
            if (totalEl) totalEl.textContent = formatMoney(sum);

            const subtotalEl = document.getElementById('summarySubtotal');
            if (subtotalEl) subtotalEl.textContent = `${formatMoney(sum)} SAR`;
        }

        function changeQty(cartKey, delta) {
            const current = getQty(cartKey);
            const next = current + delta;
            updateQuantity(cartKey, next);
        }

        async function updateQuantity(cartKey, quantity) {
            if (quantity < 1) return;
            if (pending.has(cartKey)) return;

            const card = getCard(cartKey);
            if (!card) return;

            const stock = Number(card.getAttribute('data-stock') || 999999);
            const unit = Number(card.getAttribute('data-unit-price') || 0);

            if (quantity > stock) {
                Toast.fire({
                    icon: 'warning',
                    title: "@lang('message.No more stock')"
                });
                return;
            }

            pending.add(cartKey);

            // optimistic UI update
            const prevQty = getQty(cartKey);
            setQtyUI(cartKey, quantity);
            setLineTotalUI(cartKey, unit * quantity);
            recomputeSummaryFromUI();

            try {
                const url = "{{ route('cart.update', ':id') }}".replace(':id', encodeURIComponent(cartKey));

                const response = await fetch(url, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        cart_key: cartKey,
                        quantity
                    })
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok || !data.success) {
                    // rollback
                    setQtyUI(cartKey, prevQty);
                    setLineTotalUI(cartKey, unit * prevQty);
                    recomputeSummaryFromUI();

                    Toast.fire({
                        icon: 'error',
                        title: data.message || "@lang('message.Error updating cart')"
                    });
                    return;
                }

                if (typeof data.item_line_total !== 'undefined') {
                    setLineTotalUI(cartKey, data.item_line_total);
                }
                if (typeof data.cart_total !== 'undefined') {
                    const totalEl = document.getElementById('summaryTotal');
                    if (totalEl) totalEl.textContent = formatMoney(data.cart_total);
                }
                if (typeof data.subtotal !== 'undefined') {
                    const subtotalEl = document.getElementById('summarySubtotal');
                    if (subtotalEl) subtotalEl.textContent = `${formatMoney(data.subtotal)} SAR`;
                }

            } catch (err) {
                console.error(err);

                // rollback
                setQtyUI(cartKey, prevQty);
                setLineTotalUI(cartKey, unit * prevQty);
                recomputeSummaryFromUI();

                Toast.fire({
                    icon: 'error',
                    title: "@lang('message.Error updating cart')"
                });
            } finally {
                pending.delete(cartKey);
            }
        }

        async function removeFromCart(cartKey) {
            const result = await Swal.fire({
                title: "@lang('message.Remove item?')",
                text: "@lang('message.This will remove the item from your cart.')",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: "@lang('message.Remove')",
                cancelButtonText: "@lang('message.Cancel')"
            });

            if (!result.isConfirmed) return;

            try {
                const response = await fetch(`/cart/remove`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        cart_key: cartKey
                    })
                });

                const data = await response.json().catch(() => ({}));

                if (data.success) {
                    Toast.fire({
                        icon: 'success',
                        title: "@lang('message.Item removed')"
                    });
                    setTimeout(() => location.reload(), 400);
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: data.message || "@lang('message.Error removing item')"
                    });
                }

            } catch (error) {
                console.error(error);
                Toast.fire({
                    icon: 'error',
                    title: "@lang('message.Error removing item')"
                });
            }
        }
    </script>

@endsection
