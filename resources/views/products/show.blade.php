@extends('layouts.app')

@php
    $seoTitle = $product->metaTitle();
    $seoDescription = $product->metaDescription();
    $seoCanonical = $product->canonicalUrl();
    $seoImage = $product->getPrimaryImagePath() ? asset($product->getPrimaryImagePath()) : null;
    $seoSiteName = $globalSettings->site_name ?? 'AL Bashawri';
@endphp

@section('title', $seoTitle)
@section('meta_description', $seoDescription)
@section('canonical', $seoCanonical)

@if ($product->seo_noindex || !$product->is_active)
    @section('robots', 'noindex, follow')
@endif

@section('meta')
    {{-- Open Graph --}}
    <meta property="og:type" content="product">
    <meta property="og:site_name" content="{{ $seoSiteName }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $seoCanonical }}">
    <meta property="og:locale" content="{{ app()->getLocale() === 'ar' ? 'ar_SA' : 'en_US' }}">
    @if ($seoImage)
        <meta property="og:image" content="{{ $seoImage }}">
    @endif
    <meta property="product:price:amount" content="{{ $product->final_price }}">
    <meta property="product:price:currency" content="SAR">

    {{-- Twitter --}}
    <meta name="twitter:card" content="{{ $seoImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    @if ($seoImage)
        <meta name="twitter:image" content="{{ $seoImage }}">
    @endif

    {{-- Données structurées Product --}}
    @php
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => (string) $product->name,
            'description' => $seoDescription,
            'sku' => $product->sku,
            'url' => $seoCanonical,
            'offers' => [
                '@type' => 'Offer',
                'url' => $seoCanonical,
                'price' => (string) $product->final_price,
                'priceCurrency' => 'SAR',
                'availability' => $product->isInStock()
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'itemCondition' => 'https://schema.org/NewCondition',
            ],
        ];

        if ($seoImage) {
            $schema['image'] = [$seoImage];
        }
        if ($product->brand) {
            $schema['brand'] = ['@type' => 'Brand', 'name' => (string) $product->brand->name];
        }
        if ($product->category) {
            $schema['category'] = (string) $product->category->name;
        }
        if ($product->total_reviews > 0 && $product->rating > 0) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => (string) $product->rating,
                'reviewCount' => (string) $product->total_reviews,
            ];
        }
    @endphp
    <script type="application/ld+json">
        {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    {{-- Fil d'Ariane --}}
    <script type="application/ld+json">
        {!! json_encode(
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => array_values(
                    array_filter([
                        ['@type' => 'ListItem', 'position' => 1, 'name' => __('message.Home'), 'item' => url('/')],
                        $product->category
                            ? [
                                '@type' => 'ListItem',
                                'position' => 2,
                                'name' => (string) $product->category->name,
                                'item' => route('products.category', $product->category->slug),
                            ]
                            : null,
                        ['@type' => 'ListItem', 'position' => $product->category ? 3 : 2, 'name' => (string) $product->name, 'item' => $seoCanonical],
                    ]),
                ),
            ],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        ) !!}
    </script>
@endsection

@section('content')

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script defer src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        :root {
            --brand: #1b1e6c;
        }

        .brand {
            color: var(--brand);
        }

        .brand-bg {
            background: var(--brand);
        }

        .brand-border {
            border-color: var(--brand) !important;
        }

        .brand-ring {
            --tw-ring-color: rgb(27 30 108 / .20);
        }

        .slot-disabled {
            opacity: .45;
            pointer-events: none;
        }

        .selected-store-pin {
            background: transparent !important;
            border: 0 !important;
        }
        /* بطاقه جديد */
    
        .product-add-cart-btn {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;

    height: 44px;
    padding: 0 20px;

    border: none;
    border-radius: 5px;

    background: #2E3192;
    color: #fff;

    overflow: hidden;
    cursor: pointer;

    font-size: 13px;
    font-weight: 600;

    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.15);

    transition: all 0.3s ease-in-out;
}

.product-add-cart-btn::after {
    content: "";

    position: absolute;
    top: 0;
    right: 0;

    width: 0%;
    height: 100%;

    background: #fff;

    transition: all 0.4s ease-in-out;
}

.product-add-cart-btn:hover::after {
    right: auto;
    left: 0;
    width: 100%;
}

.product-add-cart-btn span {
    position: relative;
    z-index: 2;
    transition: all 0.3s ease-in-out;
}

.product-add-cart-btn:hover span {
    color: #2E3192;
    animation: productCartScaleUp 0.3s ease-in-out;
}

@keyframes productCartScaleUp {
    0% {
        transform: scale(1);
    }

    50% {
        transform: scale(0.95);
    }

    100% {
        transform: scale(1);
    }
}

.product-add-cart-btn:active {
    transform: scale(0.97);
}
    </style>

    <div class="bg-white min-h-screen">
        <div class="bg-gray-50 border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
                <nav class="flex items-center space-x-1.5 text-xs text-gray-600">
                    <a href="{{ route('home') }}" class="hover:text-[color:var(--brand)] transition">
                        @lang('message.Home')
                    </a>
                    <svg class="w-2.5 h-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-gray-900 font-medium">{{ $product->name }}</span>
                </nav>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
            <div class="mx-auto max-w-7xl px-4 lg:px-10 py-6">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 lg:p-6">
                    <div class="flex flex-col lg:flex-row gap-6 lg:gap-10">

                        <div class="lg:basis-1/2">
                            <div
                                class="relative bg-[#F9F9F9] rounded-xl border border-gray-100 h-[420px] overflow-hidden shadow-sm">

                                {{-- Virtual Try-On --}}
                                @if ($product->tryon_available && $product->tryonFile)
                                    <a href="{{ route('tryon.show', $product->id) }}"
                                        class="absolute top-4 right-4 z-20 inline-flex items-center gap-2 px-4 py-2 rounded-full
                                   text-[11px] font-extrabold uppercase tracking-widest bg-white/95 backdrop-blur
                                   border border-[color:var(--brand)] text-[color:var(--brand)]
                                   hover:bg-[color:var(--brand)] hover:text-white transition shadow-sm">
                                        <i class="fas fa-video text-[13px]"></i>
                                        @lang('message.Virtual Try-On')
                                    </a>
                                @endif

                                {{-- MAIN IMAGE --}}
                                <img id="mainProductImage"
                                    src="{{ $product->getPrimaryImagePath() ? asset($product->getPrimaryImagePath()) : asset('images/default-product.jpg') }}"
                                    alt="{{ $product->name }}"
                                    class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300">

                                <div
                                    class="pointer-events-none absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/10 to-transparent">
                                </div>
                            </div>

                            {{-- THUMBNAILS --}}
                            @if ($product->images->count() > 0)
                                <div class="mt-4 flex justify-center gap-2 flex-wrap">
                                    @foreach ($product->images as $index => $image)
                                        <button type="button" onclick="setImage({{ $index + 1 }})"
                                            class="group thumb border border-gray-200 rounded-lg overflow-hidden w-16 h-16 bg-white
                                       hover:border-[color:var(--brand)] focus:outline-none focus:ring-2 focus:ring-[color:var(--brand)]/30 transition">
                                            <img src="{{ asset($image->image) }}"
                                                alt="{{ $product->name }} thumbnail {{ $index + 1 }}"
                                                class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-200">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="lg:basis-1/2 flex flex-col">
                            <div class="pb-4 border-b border-gray-100">
                                <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-[color:var(--brand)]">
                                    {{ $product->brand->name }}
                                </p>

                                <h1 class="mt-1 text-2xl font-extrabold text-gray-900 leading-tight">
                                    {{ $product->name }}
                                </h1>

                                <div class="mt-2 flex items-center justify-between gap-4">
                                    {{-- Rating + Reviews --}}
                                    <div class="flex items-center gap-2 text-[11px]">
                                        <div class="flex items-center text-yellow-500">
                                            @php $rating = $product->rating ?? 0; @endphp
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i
                                                    class="fas {{ $i <= floor($rating) ? 'fa-star' : ($i - $rating < 1 ? 'fa-star-half-alt' : 'fa-star text-gray-300') }} w-3 h-3"></i>
                                            @endfor
                                        </div>
                                        <span class="text-gray-500">
                                            ({{ $product->reviews->count() }} @lang('message.Reviews'))
                                        </span>
                                    </div>

                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest
                                   {{ $product->isInStock() ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                                        {{ $product->isInStock() ? __('message.In Stock') : __('message.Out of Stock') }}
                                    </span>
                                </div>

                                <div class="mt-4 flex items-start justify-between gap-4">
                                    <div class="flex flex-col gap-2">
                                        {{-- Base price --}}
                                        <div class="flex items-center gap-3">
                                            <span class="text-2xl font-extrabold text-[color:var(--brand)]">
                                                {{ number_format($product->discount_price ?? $product->price, 2) }}
                                                <span class="text-xs font-semibold text-gray-500">SAR</span>
                                            </span>

                                            @if ($product->discount_price)
                                                <span class="text-sm text-gray-400 line-through">
                                                    {{ number_format($product->price, 2) }} SAR
                                                </span>

                                                <span
                                                    class="text-[10px] font-extrabold text-red-600 bg-red-50 px-2 py-1 rounded-full">
                                                    -{{ $product->discount_percentage }}%
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Final price --}}
                                        <div id="finalPriceDisplay" class="hidden">
                                            <p class="text-[12px] text-gray-700">
                                                @lang('message.Final price with selected lens'):
                                                <span id="finalPriceValue"
                                                    class="font-extrabold text-[color:var(--brand)]"></span> SAR
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Specifications --}}
                            <div class="mt-5">
                    <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">
                        @lang('message.Frame Specifications')
                    </h3>
                    <div class="grid grid-cols-2 gap-x-6 gap-y-1.5 text-[12px]">
                        <div class="flex justify-between border-b border-gray-100 py-1">
                            <span class="text-gray-400">@lang('message.Type')</span>
                            <span class="font-medium text-gray-800 capitalize">{{ $product->type }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-100 py-1">
                            <span class="text-gray-400">@lang('message.Gender')</span>
                            <span class="font-medium text-gray-800 capitalize">{{ $product->gender }}</span>
                        </div>
                        @if ($product->lens_shape)
                            <div class="flex justify-between border-b border-gray-100 py-1">
                                <span class="text-gray-400">@lang('message.Lens shape')</span>
                                <span class="font-medium text-gray-800 capitalize">{{ $product->lens_shape }}</span>
                            </div>
                        @endif
                        @if ($product->frame_color)
                            <div class="flex justify-between border-b border-gray-100 py-1">
                                <span class="text-gray-400">@lang('message.Frame color')</span>
                                <span class="font-medium text-gray-800">{{ $product->frame_color }}</span>
                            </div>
                        @endif
                        @if ($product->lens_size)
                            <div class="flex justify-between border-b border-gray-100 py-1">
                                <span class="text-gray-400">@lang('message.Lens Size')</span>
                                <span class="font-medium text-gray-800">{{ $product->lens_size }}</span>
                            </div>
                        @endif
                        @if ($product->bridge_width)
                            <div class="flex justify-between border-b border-gray-100 py-1">
                                <span class="text-gray-400">@lang('message.Bridge width')</span>
                                <span class="font-medium text-gray-800">{{ $product->bridge_width }}</span>
                            </div>
                        @endif
                        @if ($product->arm_length)
                            <div class="flex justify-between border-b border-gray-100 py-1">
                                <span class="text-gray-400">@lang('message.Arm length')</span>
                                <span class="font-medium text-gray-800">{{ $product->arm_length }}</span>
                            </div>
                        @endif
                        @if ($product->frame_type)
                            <div class="flex justify-between border-b border-gray-100 py-1">
                                <span class="text-gray-400">@lang('message.Frame type')</span>
                                <span class="font-medium text-gray-800 capitalize">{{ $product->frame_type }}</span>
                            </div>
                        @endif
                    </div>
                </div> 

                            {{-- Description --}}
                            <div class="mt-5">
                                <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">
                                    @lang('message.Description')
                                </h3>
                                <p class="text-[13px] text-gray-700 leading-relaxed">
                                    {{ $product->description }}
                                </p>
                            </div>

                            @if ($product->type === 'eyeglasses' || $product->type === 'sunglasses')
                                <div class="mt-4 hidden">
                                    <label for="lens_type"
                                        class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">
                                        @lang('message.Lens Type')
                                    </label>
                                    <select id="lens_type" name="lens_type"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[color:var(--brand)]/30 focus:border-[color:var(--brand)]">
                                        <option value="">--- @lang('message.Please Select') ---</option>
                                    </select>
                                </div>
                            @endif

                            {{-- Buttons --}}
                            <div class="mt-auto pt-6">
                                <div class="flex items-center gap-3">
                                    {{-- Quantity --}}
                                    <div
                                        class="flex items-center border border-gray-200 rounded-xl bg-white h-11 overflow-hidden">
                                        <button type="button" onclick="updateQuantity(-1)"
                                            class="px-4 text-gray-500 hover:text-gray-900 hover:bg-gray-50 h-full transition">
                                            -
                                        </button>

                                        <input type="text" id="quantity" value="1" readonly
                                            class="w-12 text-center text-sm border-none focus:ring-0 font-bold text-gray-900">

                                        <button type="button" onclick="updateQuantity(1)"
                                            class="px-4 text-gray-500 hover:text-gray-900 hover:bg-gray-50 h-full transition">
                                            +
                                        </button>
                                    </div>
<!-- 
                                    <button type="button"
                                        onclick="event.stopPropagation();
                                @if ($product->type === 'eyeglasses' || $product->type === 'sunglasses' ) window.dispatchEvent(new CustomEvent('open-lens-modal'));
                                @else
                                    addToCart({{ $product->id }}, 'product', parseInt(document.getElementById('quantity').value), null, 0); @endif"
                                        class="flex-1 h-11 rounded-xl text-xs font-extrabold uppercase tracking-widest
                                   flex items-center justify-center gap-2
                                   bg-[color:var(--brand)] text-white
                                   hover:brightness-95 active:scale-[0.99] transition
                                   {{ !$product->isInStock() ? 'opacity-40 cursor-not-allowed' : '' }}"
                                        {{ !$product->isInStock() ? 'disabled' : '' }}>
                                        <i class="fas fa-shopping-bag text-[14px]"></i>
                                        @lang('message.ADD TO CART')
                                    </button> -->
                                    <!-- اضف سله جدا -->


                                    <button
    type="button"
    onclick="event.stopPropagation();
@if ($product->type === 'eyeglasses' || $product->type === 'sunglasses')
    window.dispatchEvent(new CustomEvent('open-lens-modal'));
@else
    addToCart({{ $product->id }}, 'product', parseInt(document.getElementById('quantity').value), null, 0);
@endif"
    class="product-add-cart-btn flex-1
    {{ !$product->isInStock() ? 'opacity-40 cursor-not-allowed' : '' }}"
    {{ !$product->isInStock() ? 'disabled' : '' }}>

    <span>
        @lang('message.ADD TO CART')
    </span>

</button>
                                    {{-- Wishlist --}}
                                    @auth
                                        <button type="button" onclick="toggleWishlist({{ $product->id }}, 'product', this)"
                                            class="h-11 w-11 flex items-center justify-center border border-gray-200 rounded-xl
                                       hover:border-red-200 hover:text-red-600 transition-colors">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    @endauth
                                </div>

                                {{-- SKU & Category --}}
                                <div
                                    class="mt-4 flex justify-between text-[9px] text-gray-400 uppercase tracking-widest font-semibold">
                                    <span>@lang('message.SKU'): {{ $product->sku }}</span>
                                    <span>@lang('message.Category'): {{ $product->category->name }}</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


            {{-- LENS MODAL WIZARD --}}
            @if ($product->type === 'eyeglasses' || $product->type === 'sunglasses')
                <style>
                    :root {
                        --brand: #1b1e6c;
                        --brand-2: #23278a;
                        --brand-rgb: 27 30 108;
                    }

                    .brand {
                        color: var(--brand);
                    }

                    .brand-bg {
                        background: var(--brand);
                    }

                    .brand-bg:hover {
                        background: var(--brand-2);
                    }

                    .brand-border {
                        border-color: var(--brand) !important;
                    }

                    .brand-ring {
                        --tw-ring-color: rgb(var(--brand-rgb) / .18) !important;
                    }

                    .focus-brand:focus {
                        outline: none;
                        border-color: var(--brand);
                        box-shadow: 0 0 0 4px rgb(var(--brand-rgb) / .14);
                    }

                    .slot-disabled {
                        opacity: .45;
                        pointer-events: none;
                    }

                    [x-cloak] {
                        display: none !important;
                    }
                </style>

                <div x-data="lensWizardV2({{ (float) ($product->discount_price ?? $product->price) }}, {{ (int) $product->id }})" x-init="init()" x-on:open-lens-modal.window="open()" x-cloak
                    class="relative">

                    <!-- Backdrop -->
                    <div x-show="isOpen" x-transition.opacity.duration.200ms
                        class="fixed inset-0 z-[120] bg-black/55 backdrop-blur-[2px] flex items-center justify-center p-4">
                        <!-- Modal -->
                        <div @click.away="close()" x-show="isOpen" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-3 scale-[.98]"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-3 scale-[.98]"
                            class="bg-white w-full max-w-4xl rounded-3xl shadow-2xl overflow-hidden max-h-[92vh] flex flex-col border border-gray-100"
                            role="dialog" aria-modal="true">

                            <!-- Header -->
                            <div class="px-6 py-4 border-b bg-white/90 backdrop-blur flex items-center justify-between">
                                <div class="min-w-0">
                                    <h3 class="text-lg sm:text-xl font-black tracking-tight brand" x-text="headerTitle()">
                                    </h3>
                                    <div class="mt-1 flex items-center gap-2 text-xs text-gray-500">
                                        <span class="inline-flex items-center gap-1">
                                            <span>@lang('message.Step')</span>
                                            <span class="font-extrabold brand" x-text="step"></span>
                                            <span>/4</span>
                                        </span>
                                    </div>
                                </div>

                                <button type="button" @click="close()"
                                    class="h-10 w-10 rounded-full grid place-items-center border border-gray-200 text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition"
                                    aria-label="Close">
                                    ✕
                                </button>
                            </div>

                            <!-- Body -->
                            <div
                                class="p-6 space-y-6 overflow-y-auto flex-1 bg-gradient-to-b from-white to-gray-50/60 no_scrollbar">

                                <!-- Step 1 -->
                                <div x-show="step === 1" x-transition.opacity>
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-black brand">@lang('message.Choose lens type')</h4>
                                        <span class="text-[11px] text-gray-500">@lang('message.Choose one option to continue')</span>
                                    </div>

                                    <div class="grid sm:grid-cols-2 gap-4">
                                        <button type="button" @click="selectType('single_vision')"
                                            :class="selected.type === 'single_vision' ?
                                                'brand-border ring-2 brand-ring bg-white' : 'border-gray-200 bg-white'"
                                            class="text-left p-4 border rounded-2xl hover:border-[color:var(--brand)] transition shadow-sm hover:shadow-md">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <div class="font-black text-gray-900">@lang('message.Single vision lenses')</div>
                                                    <div class="text-xs text-gray-500 mt-1">@lang('message.For distance or near vision')</div>
                                                </div>
                                                <span
                                                    class="mt-1 inline-flex items-center justify-center h-8 w-8 rounded-xl border"
                                                    :class="selected.type === 'single_vision' ?
                                                        'brand-border bg-[rgb(27_30_108_/_.06)]' :
                                                        'border-gray-200 bg-gray-50'">
                                                    <span class="h-2.5 w-2.5 rounded-full"
                                                        :class="selected.type === 'single_vision' ? 'brand-bg' : 'bg-gray-300'"></span>
                                                </span>
                                            </div>
                                        </button>

                                        <button type="button" @click="selectType('progressive_store')"
                                            :class="selected.type === 'progressive_store' ?
                                                'brand-border ring-2 brand-ring bg-white' : 'border-gray-200 bg-white'"
                                            class="text-left p-4 border rounded-2xl hover:border-[color:var(--brand)] transition shadow-sm hover:shadow-md">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <div class="font-black text-gray-900">@lang('message.Progressive')</div>
                                                    <div class="text-xs text-gray-500 mt-1">@lang('message.Additional measurements in-store')</div>
                                                </div>
                                                <span
                                                    class="mt-1 inline-flex items-center justify-center h-8 w-8 rounded-xl border"
                                                    :class="selected.type === 'progressive_store' ?
                                                        'brand-border bg-[rgb(27_30_108_/_.06)]' :
                                                        'border-gray-200 bg-gray-50'">
                                                    <span class="h-2.5 w-2.5 rounded-full"
                                                        :class="selected.type === 'progressive_store' ? 'brand-bg' :
                                                            'bg-gray-300'"></span>
                                                </span>
                                            </div>
                                        </button>
                                    </div>

                                    <div class="mt-5 flex justify-end">
                                        <button type="button" @click="next()" :disabled="!selected.type"
                                            class="px-6 py-2.5 rounded-xl text-white text-sm font-black brand-bg disabled:opacity-40 shadow-sm hover:shadow-md transition">
                                            @lang('message.Next')
                                        </button>
                                    </div>
                                </div>

                                <!-- Step 2 -->
                                <div x-show="step === 2" x-transition.opacity>
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-black brand">@lang('message.Choose lens category')</h4>
                                        <button type="button" @click="prev()"
                                            class="text-xs font-black text-gray-600 hover:text-black transition">←
                                            @lang('message.Back')</button>
                                    </div>

                                    <div class="grid gap-3">
                                        <template x-for="cat in categoriesForSelectedType()" :key="cat.id">
                                            <button type="button" @click="selectCategory(cat)"
                                                class="group relative w-full text-left rounded-3xl border bg-white p-4 sm:p-5 transition
                                                    shadow-[0_1px_0_rgba(0,0,0,.04)] hover:shadow-[0_10px_30px_rgba(0,0,0,.08)]
                                                    hover:-translate-y-[1px] focus:outline-none focus-visible:ring-4 focus-visible:ring-[rgb(27_30_108_/_.14)]"
                                                :class="selected.category?.id === cat.id ? 'brand-border ring-2 brand-ring' :
                                                    'border-gray-200 hover:border-[color:var(--brand)]'">

                                                <div class="pointer-events-none absolute inset-0 rounded-3xl opacity-0 transition"
                                                    :class="selected.category?.id === cat.id ?
                                                        'opacity-100 bg-[radial-gradient(1200px_circle_at_20%_0%,rgba(27,30,108,.10),transparent_35%)]' :
                                                        'opacity-0'">
                                                </div>

                                                <div class="relative flex items-start justify-between gap-4">
                                                    <div class="w-full min-w-0">
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <div class="font-black text-gray-900 truncate text-[15px] sm:text-base"
                                                                x-text="cat.name"></div>

                                                            <span x-show="cat.badge"
                                                                class="text-[10px] font-black px-2 py-1 rounded-full
                                                                    bg-[rgb(27_30_108_/_.08)] brand border border-[rgb(27_30_108_/_.12)]"
                                                                x-text="cat.badge"></span>

                                                            <span
                                                                class="ml-auto inline-flex items-center gap-2 text-[11px] font-black px-2.5 py-1 rounded-full border transition"
                                                                :class="selected.category?.id === cat.id ?
                                                                    'brand-border bg-[rgb(27_30_108_/_.06)] brand' :
                                                                    'border-transparent text-gray-400'">
                                                                <span class="h-2 w-2 rounded-full"
                                                                    :class="selected.category?.id === cat.id ? 'brand-bg' :
                                                                        'bg-gray-300'"></span>
                                                                <span
                                                                    x-show="selected.category?.id === cat.id">@lang('message.Selected')</span>
                                                            </span>
                                                        </div>

                                                        <div
                                                            class="mt-2 flex flex-wrap gap-x-3 gap-y-1.5 text-xs text-gray-600">
                                                            <template x-for="f in cat.features" :key="f">
                                                                <span class="inline-flex items-center gap-1">
                                                                    <span
                                                                        class="h-1.5 w-1.5 rounded-full bg-gray-300"></span>
                                                                    <span x-text="f"></span>
                                                                </span>
                                                            </template>
                                                        </div>

                                                        <div
                                                            class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap gap-x-5 gap-y-2 text-[11px] font-semibold brand">
                                                            <span class="inline-flex items-center gap-1.5">
                                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                                                    <path
                                                                        d="M12 2l1.2 4.2L17.4 7.4l-4.2 1.2L12 12.8l-1.2-4.2L6.6 7.4l4.2-1.2L12 2z"
                                                                        fill="#1b1e6c" />
                                                                    <path
                                                                        d="M19 13l.8 2.8 2.8.8-2.8.8L19 20l-.8-2.8-2.8-.8 2.8-.8L19 13z"
                                                                        fill="#1b1e6c" />
                                                                </svg>
                                                                @lang('message.Anti-glare')
                                                            </span>

                                                            <span class="inline-flex items-center gap-1.5">
                                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                                                    <path
                                                                        d="M12 2l8 4v6c0 5-3.4 9.4-8 10-4.6-.6-8-5-8-10V6l8-4z"
                                                                        fill="#1b1e6c" />
                                                                </svg>
                                                                @lang('message.Scratch resistant')
                                                            </span>

                                                            <span class="inline-flex items-center gap-1.5">
                                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                                                    <circle cx="12" cy="12" r="4.2"
                                                                        fill="#1b1e6c" />
                                                                    <path
                                                                        d="M12 2v3M12 19v3M2 12h3M19 12h3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M19.8 4.2l-2.1 2.1M6.3 17.7l-2.1 2.1"
                                                                        stroke="#1b1e6c" stroke-width="2"
                                                                        stroke-linecap="round" />
                                                                </svg>
                                                                @lang('message.UV protection')
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="flex flex-col items-end justify-between gap-3 whitespace-nowrap">
                                                        <div class="text-right">
                                                            <div class="text-[11px] text-gray-400">@lang('message.+ Add')</div>
                                                            <div class="flex items-end gap-1.5">
                                                                <div
                                                                    class="text-xl sm:text-2xl font-black text-gray-900 leading-none">
                                                                    <span x-text="cat.price"></span>
                                                                </div>
                                                                <div
                                                                    class="text-[11px] font-semibold text-gray-500 pb-[2px]">
                                                                    SAR</div>
                                                            </div>
                                                        </div>

                                                        <span
                                                            class="h-10 w-10 rounded-2xl grid place-items-center border transition group-hover:border-[color:var(--brand)]"
                                                            :class="selected.category?.id === cat.id ?
                                                                'brand-border bg-[rgb(27_30_108_/_.06)]' :
                                                                'border-gray-200 bg-white'">
                                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                                                <path d="M9 18l6-6-6-6"
                                                                    :stroke="selected.category?.id === cat.id ? '#1b1e6c' :
                                                                        '#6b7280'"
                                                                    stroke-width="2" stroke-linecap="round"
                                                                    stroke-linejoin="round" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                </div>
                                            </button>
                                        </template>
                                    </div>

                                    <div class="mt-5 flex justify-end">
                                        <button type="button" @click="next()" :disabled="!selected.category"
                                            class="px-6 py-2.5 rounded-xl text-white text-sm font-black brand-bg disabled:opacity-40 shadow-sm hover:shadow-md transition">
                                            @lang('message.Next')
                                        </button>
                                    </div>
                                </div>

                                <!-- Step 3 -->
                                <div x-show="step === 3" x-transition.opacity>
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-black brand">@lang('message.Additional options')</h4>
                                        <button type="button" @click="prev()"
                                            class="text-xs font-black text-gray-600 hover:text-black transition">←
                                            @lang('message.Back')</button>
                                    </div>

                                    <div class="space-y-3">
                                        <template x-for="addon in addonsForSelectedCategory()" :key="addon.id">
                                            <div class="border rounded-2xl p-4 bg-white shadow-sm"
                                                :class="isAddonSelected(addon.id) ? 'brand-border ring-2 brand-ring' :
                                                    'border-gray-200'">
                                                <label class="flex items-start justify-between gap-3 cursor-pointer">
                                                    <div class="flex items-start gap-3">
                                                        <input type="checkbox"
                                                            class="mt-1 h-4 w-4 rounded border-gray-300 text-[color:var(--brand)] focus:ring-2 brand-ring"
                                                            :checked="isAddonSelected(addon.id)"
                                                            @change="toggleAddon(addon)">
                                                        <div class="min-w-0">
                                                            <div class="font-black text-gray-900" x-text="addon.name">
                                                            </div>
                                                            <div class="text-xs text-gray-500 mt-1" x-text="addon.desc">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="text-right whitespace-nowrap">
                                                        <div class="text-[11px] text-gray-400">+</div>
                                                        <div class="text-sm font-black text-gray-900">
                                                            <span x-text="addon.price"></span>
                                                            <span class="text-xs font-semibold text-gray-600">SAR</span>
                                                        </div>
                                                    </div>
                                                </label>

                                                {{-- Choix de la couleur : DOIT rester en dehors du <label>,
                                                     sinon chaque clic re-bascule la checkbox et efface la sélection. --}}
                                                <div x-show="addon.options && addon.options.length && isAddonSelected(addon.id)"
                                                    class="mt-4 pt-3 border-t border-gray-100">
                                                    <div class="text-xs font-black brand mb-2">
                                                        @lang('message.Color')</div>

                                                    <div class="flex flex-wrap items-center gap-3">
                                                        <template x-for="opt in addon.options" :key="opt.value">
                                                            <button type="button"
                                                                @click.stop.prevent="setAddonOption(addon.id, opt.value)"
                                                                class="group inline-flex items-center gap-2"
                                                                :aria-pressed="selected.addon_options?.[addon.id] === opt.value">
                                                                <span
                                                                    class="h-8 w-8 rounded-full grid place-items-center border transition"
                                                                    :class="selected.addon_options?.[addon.id] ===
                                                                        opt.value ?
                                                                        'brand-border ring-4 ring-[rgb(27_30_108_/_.14)] bg-white' :
                                                                        'border-gray-200 bg-white group-hover:border-[color:var(--brand)]'">
                                                                    <span class="h-5 w-5 rounded-full shadow-sm"
                                                                        :style="`background:${opt.color ?? '#1b1e6c'}`"></span>
                                                                </span>

                                                                <span class="text-xs font-black"
                                                                    :class="selected.addon_options?.[addon.id] ===
                                                                        opt.value ? 'brand' :
                                                                        'text-gray-700'"
                                                                    x-text="opt.label"></span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <div class="mt-5 flex justify-end">
                                        <button type="button" @click="next()"
                                            class="px-6 py-2.5 rounded-xl text-white text-sm font-black brand-bg shadow-sm hover:shadow-md transition">
                                            @lang('message.Next')
                                        </button>
                                    </div>
                                </div>

                                <!-- Step 4 -->
                                <div x-show="step === 4" x-transition.opacity>

                                    {{-- SHARED STORE + MAP + SCHEDULE --}}
                                    <div x-show="needsStoreBookingUI()">

                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="text-sm font-black brand" x-text="storeBookingTitle()"></h4>
                                            <button type="button" @click="prev()"
                                                class="text-xs font-black text-gray-600 hover:text-black transition">←
                                                @lang('message.Back')</button>
                                        </div>

                                        <div class="border rounded-2xl p-4 border-gray-200 bg-white shadow-sm">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="text-sm font-black brand">@lang('message.Choose store')</div>
                                                <button type="button" @click="detectMyLocation()"
                                                    class="text-xs font-black brand hover:underline">
                                                    @lang('message.Use my location')
                                                </button>
                                            </div>

                                            <div class="grid md:grid-cols-2 gap-4">
                                                <div>
                                                    <input type="text" x-model="storeSearch"
                                                        placeholder="{{ __('message.Search store (name / city / country)') }}"
                                                        class="w-full rounded-xl px-4 py-2.5 text-sm border border-gray-200 bg-gray-50 focus-brand">

                                                    <div class="mt-3 space-y-3 max-h-64 overflow-auto pr-1 no_scrollbar">
                                                        <template x-for="s in filteredStores()" :key="s.id">
                                                            <button type="button" @click="selectStore(s)"
                                                                :class="selected.store?.id === s.id ?
                                                                    'brand-border ring-2 brand-ring bg-white' :
                                                                    'border-gray-200 bg-white'"
                                                                class="w-full text-left border rounded-2xl p-4 hover:border-[color:var(--brand)] transition shadow-sm hover:shadow-md">
                                                                <div class="flex items-center justify-between">
                                                                    <div class="font-black text-gray-900" x-text="s.name">
                                                                    </div>
                                                                    <div class="text-xs text-gray-500"
                                                                        x-text="s.distance_text"></div>
                                                                </div>
                                                                <div class="text-xs text-gray-500 mt-1"
                                                                    x-text="s.address"></div>
                                                                <div class="text-xs text-gray-500 mt-1"
                                                                    x-text="s.city + ' - ' + s.country"></div>
                                                                <div class="text-xs text-gray-500 mt-1" x-show="s.phone">
                                                                    <span class="font-black brand">☎</span> <span
                                                                        x-text="s.phone"></span>
                                                                </div>
                                                            </button>
                                                        </template>

                                                        <div x-show="storesLoading" class="text-xs text-gray-500">
                                                            @lang('message.Loading stores...')</div>
                                                        <div x-show="!storesLoading && filteredStores().length === 0"
                                                            class="text-xs text-gray-500">
                                                            @lang('message.No stores match your search.')
                                                        </div>
                                                    </div>
                                                </div>

                                                <div
                                                    class="border border-gray-200 rounded-2xl overflow-hidden bg-white shadow-sm">
                                                    <div
                                                        class="px-3 py-2 text-xs font-black brand border-b border-gray-200 bg-gray-50/60">
                                                        @lang('message.Map (click to set location)')
                                                    </div>

                                                    <div id="storesMap" class="h-64 w-full"></div>

                                                    <div
                                                        class="p-3 text-xs text-gray-500 border-t border-gray-200 bg-gray-50/40">
                                                        @lang('message.Stores will be sorted by nearest after selecting a location (my location or map click).')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- schedule --}}
                                        <div class="mt-4 border rounded-2xl p-4 border-gray-200 bg-white shadow-sm"
                                            x-show="selected.store">
                                            <div class="flex items-center justify-between">
                                                <div class="text-sm font-black brand">@lang('message.Schedule')</div>
                                                <div class="text-xs text-gray-500" x-text="selected.store?.name"></div>
                                            </div>

                                            <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-3">
                                                <template x-for="d in scheduleDays()" :key="d.date">
                                                    <button type="button" @click="selectDay(d.date)"
                                                        class="border rounded-2xl p-3 text-center transition bg-white shadow-sm hover:shadow-md"
                                                        :class="selected.day === d.date ? 'brand-border ring-2 brand-ring' :
                                                            'border-gray-200 hover:border-[color:var(--brand)]'">
                                                        <div class="text-xs font-black brand" x-text="d.label"></div>
                                                        <div class="text-[11px] text-gray-500 mt-1">
                                                            <span x-text="d.available"></span> @lang('message.available')
                                                        </div>
                                                    </button>
                                                </template>
                                            </div>

                                            <div class="mt-4">
                                                <div class="text-sm font-black brand mb-2">@lang('message.Time')</div>
                                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                                    <template x-for="t in scheduleSlotsForSelectedDay()"
                                                        :key="t.time">
                                                        <button type="button" @click="selectSlot(t.time)"
                                                            class="border rounded-2xl p-3 text-center transition bg-white shadow-sm hover:shadow-md"
                                                            :class="[
                                                                t.available ? '' : 'slot-disabled',
                                                                selected.slot === t.time ?
                                                                'brand-border ring-2 brand-ring' :
                                                                'border-gray-200 hover:border-[color:var(--brand)]'
                                                            ]">
                                                            <div class="text-sm font-black text-gray-900" x-text="t.time">
                                                            </div>
                                                            <div class="text-[11px] text-gray-500 mt-1"
                                                                x-text="t.available ? @json(__('message.Available')) : @json(__('message.Full'))">
                                                            </div>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Contact details required to confirm the booking --}}
                                        <div class="mt-4 border rounded-2xl p-4 border-gray-200 bg-white shadow-sm"
                                            x-show="selected.store && selected.day && selected.slot">
                                            <div class="text-sm font-black brand mb-1">@lang('message.Your contact details')</div>
                                            <div class="text-xs text-gray-500 mb-3">
                                                @lang('message.We will call you to confirm your free eye test appointment.')
                                            </div>

                                            <div class="grid sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs font-black text-gray-600 mb-1">
                                                        @lang('message.Full name') <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" x-model="booking.name"
                                                        placeholder="{{ __('message.Full name') }}"
                                                        class="w-full rounded-xl px-4 py-2.5 text-sm border bg-gray-50 focus-brand"
                                                        :class="booking.name.trim().length >= 3 ? 'border-gray-200' :
                                                            'border-red-300'">
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-black text-gray-600 mb-1">
                                                        @lang('message.Phone') <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="tel" x-model="booking.phone" dir="ltr"
                                                        inputmode="tel" placeholder="05XXXXXXXX"
                                                        class="w-full rounded-xl px-4 py-2.5 text-sm border bg-gray-50 focus-brand"
                                                        :class="isValidBookingPhone() ? 'border-gray-200' : 'border-red-300'">
                                                    <div class="text-[11px] text-gray-500 mt-1"
                                                        x-show="booking.phone.length > 0 && !isValidBookingPhone()">
                                                        @lang('message.Please enter a valid phone number.')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 bg-white border rounded-2xl p-4 border-gray-200 shadow-sm">
                                            <div class="flex items-center justify-between">
                                                <div class="text-sm font-black text-gray-900">@lang('message.Total')</div>
                                                <div class="text-lg font-black brand">
                                                    <span x-text="finalTotal()"></span> SAR
                                                </div>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-2" x-text="storeBookingHint()"></div>
                                        </div>

                                        <div class="mt-4 flex items-center justify-end gap-3">
                                            <button type="button" @click="close()"
                                                class="px-6 py-2.5 rounded-xl text-sm font-black border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                                                @lang('message.Cancel')
                                            </button>

                                            <button type="button" @click="confirmStoreBooking()"
                                                :disabled="!canConfirmStoreBooking() || booking.saving"
                                                class="px-6 py-2.5 rounded-xl text-white text-sm font-black brand-bg disabled:opacity-40 disabled:cursor-not-allowed shadow-sm hover:shadow-md transition">
                                                <span x-show="!booking.saving" x-text="storeBookingButtonText()"></span>
                                                <span x-show="booking.saving">@lang('message.Please wait...')</span>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- single_vision (Add prescription only UI) --}}
                                    <div x-show="selected.type === 'single_vision' && !needsStoreBookingUI()">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="text-sm font-black brand">@lang('message.Prescription')</h4>
                                            <button type="button" @click="prev()"
                                                class="text-xs font-black text-gray-600 hover:text-black transition">←
                                                @lang('message.Back')</button>
                                        </div>

                                        <div class="grid sm:grid-cols-2 gap-4">
                                            <button type="button" @click="prescriptionMode='add'; resetStoreSelection()"
                                                :class="prescriptionMode === 'add' ?
                                                    'brand-border ring-2 brand-ring bg-white' :
                                                    'border-gray-200 bg-white'"
                                                class="border rounded-2xl p-4 text-left hover:border-[color:var(--brand)] transition shadow-sm hover:shadow-md">
                                                <div class="font-black text-gray-900">@lang('message.Add prescription')</div>
                                                <div class="text-xs text-gray-500 mt-1">@lang('message.Enter manually or upload prescription image/PDF.')</div>
                                            </button>

                                            <button type="button" @click="prescriptionMode='book'"
                                                :class="prescriptionMode === 'book' ?
                                                    'brand-border ring-2 brand-ring bg-white' :
                                                    'border-gray-200 bg-white'"
                                                class="border rounded-2xl p-4 text-left hover:border-[color:var(--brand)] transition shadow-sm hover:shadow-md">
                                                <div class="font-black text-gray-900">@lang('message.Book free eyetest')</div>
                                                <div class="text-xs text-gray-500 mt-1">@lang('message.Not sure about your prescription? Book an eye test!')</div>
                                            </button>
                                        </div>

                                        {{-- add prescription --}}
                                        <div x-show="prescriptionMode==='add'" class="mt-4">
                                            <div class="flex gap-2">
                                                <button type="button" @click="prescriptionInput='manual'"
                                                    :class="prescriptionInput === 'manual' ? 'brand-bg text-white' :
                                                        'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                    class="px-4 py-2 rounded-xl text-xs font-black transition">
                                                    @lang('message.Manual')
                                                </button>
                                                <button type="button" @click="prescriptionInput='upload'"
                                                    :class="prescriptionInput === 'upload' ? 'brand-bg text-white' :
                                                        'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                    class="px-4 py-2 rounded-xl text-xs font-black transition">
                                                    @lang('message.Upload')
                                                </button>
                                            </div>

                                            {{-- manual --}}
                                            <div x-show="prescriptionInput==='manual'"
                                                class="mt-4 border rounded-2xl p-4 border-gray-200 bg-white shadow-sm">
                                                <div class="text-xs font-black text-gray-600 mb-3">
                                                    @lang('message.Enter your name as shown on the prescription')
                                                </div>

                                                <input type="text" x-model="prescription.patient_name"
                                                    placeholder="{{ __('message.Name') }}"
                                                    class="w-full rounded-xl px-4 py-2.5 text-sm border border-gray-200 bg-gray-50 focus-brand mb-4">

                                                <div class="grid sm:grid-cols-2 gap-4">
                                                    <div class="border rounded-2xl p-4 border-gray-200 bg-white">
                                                        <h5 class="font-black text-gray-900 text-sm mb-2">
                                                            @lang('message.Right (OD)')</h5>
                                                        <div class="grid grid-cols-3 gap-2">
                                                            <input x-model="prescription.od.sphere" type="text"
                                                                placeholder="{{ __('message.Sphere') }}"
                                                                class="rounded-xl px-3 py-2 text-sm border border-gray-200 bg-gray-50 focus-brand">
                                                            <input x-model="prescription.od.cyl" type="text"
                                                                placeholder="{{ __('message.CYL') }}"
                                                                class="rounded-xl px-3 py-2 text-sm border border-gray-200 bg-gray-50 focus-brand">
                                                            <input x-model="prescription.od.axis" type="text"
                                                                placeholder="{{ __('message.Axis') }}"
                                                                class="rounded-xl px-3 py-2 text-sm border border-gray-200 bg-gray-50 focus-brand">
                                                        </div>
                                                    </div>

                                                    <div class="border rounded-2xl p-4 border-gray-200 bg-white">
                                                        <h5 class="font-black text-gray-900 text-sm mb-2">
                                                            @lang('message.Left (OS)')</h5>
                                                        <div class="grid grid-cols-3 gap-2">
                                                            <input x-model="prescription.os.sphere" type="text"
                                                                placeholder="{{ __('message.Sphere') }}"
                                                                class="rounded-xl px-3 py-2 text-sm border border-gray-200 bg-gray-50 focus-brand">
                                                            <input x-model="prescription.os.cyl" type="text"
                                                                placeholder="{{ __('message.CYL') }}"
                                                                class="rounded-xl px-3 py-2 text-sm border border-gray-200 bg-gray-50 focus-brand">
                                                            <input x-model="prescription.os.axis" type="text"
                                                                placeholder="{{ __('message.Axis') }}"
                                                                class="rounded-xl px-3 py-2 text-sm border border-gray-200 bg-gray-50 focus-brand">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-4 border rounded-2xl p-4 border-gray-200 bg-white">
                                                    <h5 class="font-black text-gray-900 text-sm mb-2">@lang('message.Pupillary Distance (PD)')
                                                    </h5>
                                                    <input x-model="prescription.pd" type="text" placeholder="PD"
                                                        class="rounded-xl px-4 py-2.5 text-sm border border-gray-200 bg-gray-50 focus-brand w-full sm:w-64">
                                                    <div class="mt-3">
                                                        <label
                                                            class="inline-flex items-center gap-2 text-xs text-gray-600">
                                                            <input type="checkbox" x-model="prescription.pd_unknown"
                                                                class="h-4 w-4 rounded border-gray-300 text-[color:var(--brand)] focus:ring-2 brand-ring">
                                                            @lang('message.I don’t know my PD')
                                                        </label>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-2">@lang('message.You can add it later.')</p>
                                                </div>
                                            </div>

                                            <div x-show="prescriptionInput==='upload'"
                                                class="mt-4 border rounded-2xl p-4 border-gray-200 bg-white shadow-sm">
                                                <div class="text-xs font-black text-gray-600 mb-3">
                                                    @lang('message.Upload prescription (image or PDF)')
                                                </div>

                                                <label class="block">
                                                    <input type="file" accept="image/*,application/pdf"
                                                        @change="handleRxUpload($event)" class="hidden">
                                                    <div
                                                        class="rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 p-6 text-center cursor-pointer hover:border-[color:var(--brand)] transition">
                                                        <div
                                                            class="mx-auto h-12 w-12 rounded-2xl bg-[rgb(27_30_108_/_.08)] grid place-items-center mb-3">
                                                            <svg class="h-6 w-6 brand" viewBox="0 0 24 24"
                                                                fill="none">
                                                                <path d="M12 16V4m0 0l-4 4m4-4l4 4" stroke="currentColor"
                                                                    stroke-width="2" stroke-linecap="round"
                                                                    stroke-linejoin="round" />
                                                                <path d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" />
                                                            </svg>
                                                        </div>
                                                        <div class="text-sm font-black text-gray-900">@lang('message.Click to upload')
                                                        </div>
                                                        <div class="text-xs text-gray-500 mt-1">@lang('message.PNG / JPG / PDF — please upload a clear file')</div>
                                                    </div>
                                                </label>

                                                <template x-if="prescription.upload_file_name">
                                                    <div class="mt-4">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <div class="text-xs font-black brand">@lang('message.Preview')</div>
                                                            <button type="button"
                                                                class="text-xs font-black text-gray-600 hover:text-black"
                                                                @click="clearRxUpload()">
                                                                @lang('message.Remove')
                                                            </button>
                                                        </div>

                                                        {{-- Image preview --}}
                                                        <template
                                                            x-if="prescription.upload_file_type && prescription.upload_file_type.startsWith('image/')">
                                                            <img :src="prescription.upload_preview"
                                                                class="max-h-56 rounded-2xl border border-gray-200 shadow-sm">
                                                        </template>

                                                        {{-- PDF preview --}}
                                                        <template
                                                            x-if="prescription.upload_file_type === 'application/pdf'">
                                                            <div
                                                                class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                                                                <div
                                                                    class="px-3 py-2 text-xs font-black brand border-b border-gray-200 bg-gray-50/60">
                                                                    <span x-text="prescription.upload_file_name"></span>
                                                                </div>
                                                                <div class="h-56">
                                                                    <iframe :src="prescription.upload_preview"
                                                                        class="w-full h-full"
                                                                        title="Prescription PDF"></iframe>
                                                                </div>
                                                            </div>
                                                        </template>

                                                        <div class="text-[11px] text-gray-500 mt-2">
                                                            @lang('message.Uploaded file:') <span class="font-semibold"
                                                                x-text="prescription.upload_file_name"></span>
                                                        </div>
                                                    </div>
                                                </template>

                                            </div>
                                        </div>

                                        <div class="mt-4 bg-white border rounded-2xl p-4 border-gray-200 shadow-sm">
                                            <div class="flex items-center justify-between">
                                                <div class="text-sm font-black text-gray-900">@lang('message.Total')</div>
                                                <div class="text-lg font-black brand">
                                                    <span x-text="finalTotal()"></span> SAR
                                                </div>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-2">
                                                @lang('message.Total includes: frame price + lens category + add-ons (× quantity)')
                                            </div>
                                        </div>

                                        <div class="mt-4 flex items-center justify-end gap-3">
                                            <button type="button" @click="close()"
                                                class="px-6 py-2.5 rounded-xl text-sm font-black border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                                                @lang('message.Cancel')
                                            </button>

                                            <button type="button" @click="confirmAddToCartSingleVision()"
                                                class="px-6 py-2.5 rounded-xl text-white text-sm font-black brand-bg disabled:opacity-40 shadow-sm hover:shadow-md transition">
                                                @lang('message.Confirm & Continue')
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="px-6 py-4 border-t bg-white">
                                <div class="flex items-center justify-between text-[11px] text-gray-500">
                                    <span>@lang('message.Use the primary color selections to confirm your choice.')</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- Images scripts --}}
    <script>
        const images = [
            "{{ $product->getPrimaryImagePath() ? asset($product->getPrimaryImagePath()) : asset('images/default-product.jpg') }}",
            @foreach ($product->images as $image)
                "{{ asset($image->image) }}",
            @endforeach
        ];

        let currentIndex = 0;
        const mainImage = document.getElementById('mainProductImage');
        const thumbs = document.querySelectorAll('.thumb');

        function updateThumbs() {
            thumbs.forEach((thumb, index) => {
                thumb.classList.toggle('border-[color:var(--brand)]', index === currentIndex);
                thumb.classList.toggle('border-2', index === currentIndex);
            });
        }

        function showImage(index) {
            mainImage.classList.add('opacity-0');
            setTimeout(() => {
                mainImage.src = images[index];
                mainImage.classList.remove('opacity-0');
                currentIndex = index;
                updateThumbs();
            }, 200);
        }

        function setImage(index) {
            showImage(index);
        }
        updateThumbs();

        function updateQuantity(change) {
            const input = document.getElementById('quantity');
            let quantity = parseInt(input.value) || 1;
            quantity += change;
            if (quantity < 1) quantity = 1;
            input.value = quantity;
        }
    </script>

    {{-- Toast --}}
    <script>
        function showMessage(type, message) {
            const toast = document.createElement('div');
            toast.textContent = message;
            toast.className = `fixed bottom-5 right-5 px-4 py-2 rounded shadow-md text-white text-sm z-50 transition-all`;

            if (type === 'error') toast.style.backgroundColor = '#dc2626';
            else toast.style.backgroundColor = '#1b1e6c';

            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('opacity-0');
                setTimeout(() => toast.remove(), 500);
            }, 2000);
        }
    </script>

    <script>
        function lensWizardV2(basePrice, productId) {
            return {
                isOpen: false,
                step: 1,

                basePrice,
                productId,

                storeSearch: '',
                stores: [],
                storesLoading: false,
                userLat: null,
                userLng: null,
                map: null,
                mapMarkers: [],
                mapReady: false,

                prescriptionMode: 'add',
                prescriptionInput: 'manual',

                // Coordonnées demandées avant de confirmer un RDV
                booking: {
                    name: @json(auth()->check() ? auth()->user()->name : ''),
                    phone: @json(auth()->check() ? auth()->user()->num ?? '' : ''),
                    saving: false,
                    id: null,
                    savedSignature: null,
                },

                selected: {
                    type: null,
                    category: null,
                    addons: [],
                    addon_options: {},
                    store: null,
                    day: null,
                    slot: null
                },

                prescription: {
                    patient_name: '',
                    od: {
                        sphere: '',
                        cyl: '',
                        axis: ''
                    },
                    os: {
                        sphere: '',
                        cyl: '',
                        axis: ''
                    },
                    pd: '',
                    pd_unknown: false,

                    upload_preview: null,
                    upload_file_type: null,
                },

                data: {
                    types: {
                        single_vision: {
                            id: 'single_vision',
                            categories: [{
                                    id: 'basic',
                                    name: @json(__('message.lens.basic')),
                                    price: 550,
                                    badge: null,
                                    features: [
                                        @json(__('message.feat.reduce_glare')),
                                        @json(__('message.feat.scratch_resistant')),
                                        @json(__('message.feat.uv_protection')),
                                    ],
                                    addons: [{
                                            id: 'clear',
                                            name: @json(__('message.addon.clear')),
                                            desc: '',
                                            price: 0
                                        },
                                        {
                                            id: 'transition',
                                            name: @json(__('message.addon.transition')),
                                            desc: @json(__('message.addon.transition_desc')),
                                            price: 800,
                                            options: [{
                                                    value: 'brown',
                                                    label: @json(__('message.color.brown'))
                                                },
                                                {
                                                    value: 'gray',
                                                    label: @json(__('message.color.gray'))
                                                },
                                            ]
                                        },
                                        {
                                            id: 'photochromic',
                                            name: @json(__('message.addon.photochromic')),
                                            desc: @json(__('message.addon.photochromic_desc')),
                                            price: 400,
                                            options: [{
                                                    value: 'brown',
                                                    label: @json(__('message.color.brown'))
                                                },
                                                {
                                                    value: 'gray',
                                                    label: @json(__('message.color.gray'))
                                                },
                                                {
                                                    value: 'green',
                                                    label: @json(__('message.color.green'))
                                                },
                                            ]
                                        },
                                    ],
                                },
                                {
                                    id: 'premium',
                                    name: @json(__('message.lens.premium')),
                                    price: 1250,
                                    badge: @json(__('message.lens.badge_most_popular')),
                                    features: [
                                        @json(__('message.feat.spanish_tech')),
                                        @json(__('message.feat.reduce_glare')),
                                        @json(__('message.feat.scratch_resistant')),
                                        @json(__('message.feat.uv_protection')),
                                        @json(__('message.feat.reduce_fog')),
                                        @json(__('message.feat.blue_light')),
                                    ],
                                    addons: [{
                                            id: 'clear',
                                            name: @json(__('message.addon.clear')),
                                            desc: '',
                                            price: 0
                                        },
                                        {
                                            id: 'transition',
                                            name: @json(__('message.addon.transition')),
                                            desc: @json(__('message.addon.transition_desc')),
                                            price: 800,
                                            options: [{
                                                    value: 'brown',
                                                    label: @json(__('message.color.brown'))
                                                },
                                                {
                                                    value: 'gray',
                                                    label: @json(__('message.color.gray'))
                                                },
                                            ]
                                        },
                                        {
                                            id: 'photochromic',
                                            name: @json(__('message.addon.photochromic')),
                                            desc: @json(__('message.addon.photochromic_desc')),
                                            price: 400,
                                            options: [{
                                                    value: 'brown',
                                                    label: @json(__('message.color.brown'))
                                                },
                                                {
                                                    value: 'gray',
                                                    label: @json(__('message.color.gray'))
                                                },
                                                {
                                                    value: 'green',
                                                    label: @json(__('message.color.green'))
                                                },
                                            ]
                                        },
                                    ],
                                },
                                {
                                    id: 'elite',
                                    name: @json(__('message.lens.elite')),
                                    price: 1550,
                                    badge: null,
                                    features: [
                                        @json(__('message.feat.german_tech')),
                                        @json(__('message.feat.reduce_glare')),
                                        @json(__('message.feat.scratch_resistant')),
                                        @json(__('message.feat.uv_protection')),
                                        @json(__('message.feat.reduce_fog')),
                                        @json(__('message.feat.blue_light')),
                                        @json(__('message.feat.stain_dust_resistant')),
                                        @json(__('message.feat.dust_resistant')),
                                        @json(__('message.feat.thin_lightweight')),
                                    ],
                                    addons: [{
                                            id: 'clear',
                                            name: @json(__('message.addon.clear')),
                                            desc: '',
                                            price: 0
                                        },
                                        {
                                            id: 'transition',
                                            name: @json(__('message.addon.transition')),
                                            desc: @json(__('message.addon.transition_desc')),
                                            price: 800,
                                            options: [{
                                                    value: 'brown',
                                                    label: @json(__('message.color.brown'))
                                                },
                                                {
                                                    value: 'gray',
                                                    label: @json(__('message.color.gray'))
                                                },
                                            ]
                                        },
                                        {
                                            id: 'photochromic',
                                            name: @json(__('message.addon.photochromic')),
                                            desc: @json(__('message.addon.photochromic_desc')),
                                            price: 400,
                                            options: [{
                                                    value: 'brown',
                                                    label: @json(__('message.color.brown'))
                                                },
                                                {
                                                    value: 'gray',
                                                    label: @json(__('message.color.gray'))
                                                },
                                                {
                                                    value: 'green',
                                                    label: @json(__('message.color.green'))
                                                },
                                            ]
                                        },
                                    ],
                                },
                                {
                                    id: 'ultra',
                                    name: @json(__('message.lens.ultra')),
                                    price: 2250,
                                    badge: null,
                                    features: [
                                        @json(__('message.feat.german_tech')),
                                        @json(__('message.feat.reduce_glare')),
                                        @json(__('message.feat.scratch_resistant')),
                                        @json(__('message.feat.uv_protection')),
                                        @json(__('message.feat.reduce_fog')),
                                        @json(__('message.feat.blue_light')),
                                        @json(__('message.feat.stain_dust_resistant')),
                                        @json(__('message.feat.dust_resistant')),
                                        @json(__('message.feat.thin_lightweight')),
                                        @json(__('message.feat.more_precise_vision')),
                                        @json(__('message.feat.clear_viewing_angle')),
                                        @json(__('message.feat.very_thin_lenses')),
                                    ],
                                    addons: [{
                                            id: 'clear',
                                            name: @json(__('message.addon.clear')),
                                            desc: '',
                                            price: 0
                                        },
                                        {
                                            id: 'transition',
                                            name: @json(__('message.addon.transition')),
                                            desc: @json(__('message.addon.transition_desc')),
                                            price: 800,
                                            options: [{
                                                    value: 'brown',
                                                    label: @json(__('message.color.brown'))
                                                },
                                                {
                                                    value: 'gray',
                                                    label: @json(__('message.color.gray'))
                                                },
                                            ]
                                        },
                                        {
                                            id: 'photochromic',
                                            name: @json(__('message.addon.photochromic')),
                                            desc: @json(__('message.addon.photochromic_desc')),
                                            price: 400,
                                            options: [{
                                                    value: 'brown',
                                                    label: @json(__('message.color.brown'))
                                                },
                                                {
                                                    value: 'gray',
                                                    label: @json(__('message.color.gray'))
                                                },
                                                {
                                                    value: 'green',
                                                    label: @json(__('message.color.green'))
                                                },
                                            ]
                                        },
                                    ],
                                },
                            ],
                        },

                        progressive_store: {
                            id: 'progressive_store',
                            categories: []
                        }
                    }
                },

                async init() {
                    if (!this.data.types.progressive_store.categories.length) {
                        this.data.types.progressive_store.categories =
                            JSON.parse(JSON.stringify(this.data.types.single_vision.categories));
                    }

                    await this.loadStores();

                    this.$watch('step', () => this.ensureMapVisibleReady());
                    this.$watch('selected.type', () => this.ensureMapVisibleReady());
                    this.$watch('isOpen', () => this.ensureMapVisibleReady());
                    this.$watch('prescriptionMode', () => this.ensureMapVisibleReady());
                },

                headerTitle() {
                    if (this.step === 1) return @json(__('message.Choose lens type'));
                    if (this.step === 2) return @json(__('message.Choose lens category'));
                    if (this.step === 3) return @json(__('message.Additional options'));
                    if (this.step === 4) {
                        if (this.selected.type === 'progressive_store') return @json(__('message.Book free eyetest'));
                        return @json(__('message.Add prescription'));
                    }
                    return @json(__('message.Lenses'));
                },

                open() {
                    this.isOpen = true;
                    this.step = 1;

                    this.storeSearch = '';
                    this.prescriptionMode = 'add';
                    this.prescriptionInput = 'manual';

                    this.selected = {
                        type: null,
                        category: null,
                        addons: [],
                        addon_options: {},
                        store: null,
                        day: null,
                        slot: null
                    };

                    this.clearRxUpload();

                    this.prescription.patient_name = '';
                    this.prescription.od = {
                        sphere: '',
                        cyl: '',
                        axis: ''
                    };
                    this.prescription.os = {
                        sphere: '',
                        cyl: '',
                        axis: ''
                    };
                    this.prescription.pd = '';
                    this.prescription.pd_unknown = false;

                    this.updateFinalPriceUI();
                    this.$nextTick(() => this.ensureMapVisibleReady());
                },

                close() {
                    this.isOpen = false;
                    this.updateFinalPriceUI(true);
                },

                next() {
                    if (this.step < 4) this.step++;
                    this.$nextTick(() => this.ensureMapVisibleReady());
                },

                prev() {
                    if (this.step > 1) this.step--;
                },

                resetStoreSelection() {
                    this.selected.store = null;
                    this.selected.day = null;
                    this.selected.slot = null;
                },

                selectType(typeId) {
                    this.selected.type = typeId;
                    this.selected.category = null;
                    this.selected.addons = [];
                    this.selected.addon_options = {};
                    this.resetStoreSelection();

                    this.prescriptionMode = 'add';
                    this.prescriptionInput = 'manual';
                },

                categoriesForSelectedType() {
                    if (!this.selected.type) return [];
                    return this.data.types[this.selected.type]?.categories || [];
                },

                selectCategory(cat) {
                    this.selected.category = cat;
                    this.selected.addons = [];
                    this.selected.addon_options = {};
                    this.resetStoreSelection();
                    this.updateFinalPriceUI();
                },

                addonsForSelectedCategory() {
                    return this.selected.category?.addons || [];
                },

                isAddonSelected(addonId) {
                    return this.selected.addons.some(a => a.id === addonId);
                },

                toggleAddon(addon) {
                    if (this.isAddonSelected(addon.id)) {
                        this.selected.addons = [];
                        this.selected.addon_options = {};
                        this.updateFinalPriceUI();
                        return;
                    }

                    // Une option par défaut est pré-sélectionnée : le client peut la changer,
                    // mais il ne peut jamais rester bloqué sur « choisissez une couleur ».
                    const defaultOption = (addon.options && addon.options.length) ? addon.options[0].value : '';

                    this.selected.addons = [{
                        id: addon.id,
                        name: addon.name,
                        price: addon.price,
                        option: defaultOption
                    }];

                    this.selected.addon_options = {};
                    if (defaultOption) this.selected.addon_options[addon.id] = defaultOption;

                    this.updateFinalPriceUI();
                },

                setAddonOption(addonId, optionVal) {
                    const idx = this.selected.addons.findIndex(a => a.id === addonId);
                    if (idx !== -1) this.selected.addons[idx].option = optionVal;
                    this.selected.addon_options[addonId] = optionVal;
                },

                handleRxUpload(e) {
                    const file = e.target.files?.[0];
                    if (!file) return;

                    // clear old object URL if any
                    if (this.prescription.upload_file_type === 'application/pdf' && this.prescription.upload_preview) {
                        try {
                            URL.revokeObjectURL(this.prescription.upload_preview);
                        } catch {}
                    }

                    this.prescription.upload_file_name = file.name;
                    this.prescription.upload_file_type = file.type;

                    // PDF -> object URL preview
                    if (file.type === 'application/pdf') {
                        this.prescription.upload_preview = URL.createObjectURL(file);
                        return;
                    }

                    // Image -> dataURL preview
                    if (file.type && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (ev) => {
                            this.prescription.upload_preview = ev.target.result;
                        };
                        reader.readAsDataURL(file);
                        return;
                    }

                    // unsupported
                    this.clearRxUpload();
                    if (typeof showMessage === 'function') showMessage('error', @json(__('message.Unsupported file type.')));
                },

                clearRxUpload() {
                    if (this.prescription.upload_file_type === 'application/pdf' && this.prescription.upload_preview) {
                        try {
                            URL.revokeObjectURL(this.prescription.upload_preview);
                        } catch {}
                    }
                    this.prescription.upload_preview = null;
                    this.prescription.upload_file_name = null;
                    this.prescription.upload_file_type = null;
                },

                needsStoreBookingUI() {
                    return (this.step === 4) && (
                        this.selected.type === 'progressive_store' ||
                        (this.selected.type === 'single_vision' && this.prescriptionMode === 'book')
                    );
                },

                storeBookingTitle() {
                    return @json(__('message.Book free eyetest'));
                },

                storeBookingButtonText() {
                    return @json(__('message.Confirm | Book free eyetest'));
                },

                isValidBookingPhone() {
                    const digits = (this.booking.phone || '').replace(/[^0-9]/g, '');
                    return digits.length >= 8 && digits.length <= 15;
                },

                isValidBookingName() {
                    return (this.booking.name || '').trim().length >= 3;
                },

                canConfirmStoreBooking() {
                    return !!(this.selected.store && this.selected.day && this.selected.slot) &&
                        this.isValidBookingName() && this.isValidBookingPhone();
                },

                storeBookingHint() {
                    if (!this.selected.store || !this.selected.day || !this.selected.slot) {
                        return @json(__('message.Select store, then day, then time to complete booking.'));
                    }
                    if (!this.isValidBookingName() || !this.isValidBookingPhone()) {
                        return @json(__('message.Enter your name and phone number to confirm the booking.'));
                    }
                    return @json(__('message.Total includes: frame price + lens category + add-ons (× quantity)'));
                },

                bookingFlowKey() {
                    return this.selected.type === 'progressive_store' ? 'store_measurement' : 'book_eye_test';
                },

                /**
                 * Enregistre le RDV côté serveur dès la confirmation,
                 * pour qu'il remonte dans l'admin même sans commande finalisée.
                 */
                bookingSignature() {
                    return [
                        this.selected.store?.id,
                        this.selected.day,
                        this.selected.slot,
                        this.booking.name.trim(),
                        this.booking.phone.trim(),
                        this.bookingFlowKey(),
                    ].join('|');
                },

                async saveBookingToServer() {
                    // Évite de recréer une ligne si le client reconfirme les mêmes infos
                    const signature = this.bookingSignature();
                    if (this.booking.id && this.booking.savedSignature === signature) {
                        return this.booking.id;
                    }

                    try {
                        const res = await fetch("{{ route('eye-exam-bookings.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            },
                            body: JSON.stringify({
                                customer_name: this.booking.name.trim(),
                                customer_phone: this.booking.phone.trim(),
                                store_id: this.selected.store.id,
                                booking_date: this.selected.day,
                                booking_time: this.selected.slot,
                                flow: this.bookingFlowKey(),
                                product_id: this.productId,
                            }),
                        });

                        if (!res.ok) return null;

                        const data = await res.json();
                        if (data?.booking_id) {
                            this.booking.savedSignature = signature;
                        }
                        return data?.booking_id ?? null;
                    } catch (e) {
                        console.error('booking save failed', e);
                        return null;
                    }
                },

                async confirmStoreBooking() {
                    if (this.booking.saving) return;

                    if (!this.selected.store || !this.selected.day || !this.selected.slot) {
                        if (typeof showMessage === 'function') showMessage('error', @json(__('message.Please select store, day and time')));
                        return;
                    }

                    if (!this.isValidBookingName()) {
                        if (typeof showMessage === 'function') showMessage('error', @json(__('message.Please enter your full name.')));
                        return;
                    }

                    if (!this.isValidBookingPhone()) {
                        if (typeof showMessage === 'function') showMessage('error', @json(__('message.Please enter a valid phone number.')));
                        return;
                    }

                    this.booking.saving = true;
                    try {
                        this.booking.id = await this.saveBookingToServer();

                        if (this.selected.type === 'progressive_store') return this.confirmAddToCartForStore();
                        return this.confirmAddToCartSingleVision();
                    } finally {
                        this.booking.saving = false;
                    }
                },

                async loadStores() {
                    this.storesLoading = true;
                    try {
                        const res = await fetch("{{ route('stores.json') }}", {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (!res.ok) throw new Error(`HTTP ${res.status}`);

                        const data = await res.json();

                        this.stores = (Array.isArray(data) ? data : []).map(s => ({
                            ...s,
                            lat: (s.lat !== null && s.lat !== undefined) ? Number(s.lat) : null,
                            lng: (s.lng !== null && s.lng !== undefined) ? Number(s.lng) : null,
                            distance_km: null,
                            distance_text: ''
                        }));

                        this.recalcDistances();
                    } catch (e) {
                        console.error('loadStores error:', e);
                        this.stores = [];
                    } finally {
                        this.storesLoading = false;
                    }
                },

                filteredStores() {
                    const q = (this.storeSearch || '').toLowerCase().trim();
                    let list = this.stores.slice();

                    if (q) {
                        list = list.filter(s =>
                            (s.name || '').toLowerCase().includes(q) ||
                            (s.address || '').toLowerCase().includes(q) ||
                            (s.city || '').toLowerCase().includes(q) ||
                            (s.country || '').toLowerCase().includes(q) ||
                            (s.phone || '').toLowerCase().includes(q)
                        );
                    }

                    if (this.userLat !== null && this.userLng !== null) {
                        list.sort((a, b) => (a.distance_km ?? 999999) - (b.distance_km ?? 999999));
                    }
                    return list;
                },

                distanceKm(lat1, lon1, lat2, lon2) {
                    const toRad = (v) => v * Math.PI / 180;
                    const R = 6371;
                    const dLat = toRad(lat2 - lat1);
                    const dLon = toRad(lon2 - lon1);
                    const a = Math.sin(dLat / 2) ** 2 + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon /
                        2) ** 2;
                    return 2 * R * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                },

                recalcDistances() {
                    if (this.userLat === null || this.userLng === null) {
                        this.stores = this.stores.map(s => ({
                            ...s,
                            distance_km: null,
                            distance_text: ''
                        }));
                        return;
                    }
                    this.stores = this.stores.map(s => {
                        if (s.lat === null || s.lng === null) return {
                            ...s,
                            distance_km: null,
                            distance_text: ''
                        };
                        const km = this.distanceKm(this.userLat, this.userLng, s.lat, s.lng);
                        return {
                            ...s,
                            distance_km: km,
                            distance_text: `${km.toFixed(1)} km`
                        };
                    });
                },

                detectMyLocation() {
                    if (!navigator.geolocation) return;
                    navigator.geolocation.getCurrentPosition((pos) => {
                        this.setUserLocation(pos.coords.latitude, pos.coords.longitude, true);
                    }, (err) => console.warn(err), {
                        enableHighAccuracy: true,
                        timeout: 8000
                    });
                },

                setUserLocation(lat, lng, panMap = false) {
                    this.userLat = Number(lat);
                    this.userLng = Number(lng);
                    this.recalcDistances();

                    if (this.map && panMap) this.map.setView([this.userLat, this.userLng], 12);
                    this.renderMapMarkers();
                },

                selectStore(store) {
                    this.selected.store = store;
                    this.selected.day = null;
                    this.selected.slot = null;

                    if (this.map && store.lat !== null && store.lng !== null) {
                        this.map.setView([store.lat, store.lng], 14);
                    }
                    this.renderMapMarkers();
                },

                ensureMapVisibleReady() {
                    if (!this.isOpen) return;
                    if (!this.needsStoreBookingUI()) return;

                    this.$nextTick(() => {
                        const el = document.getElementById('storesMap');
                        if (!el) return;

                        if (!this.map && window.L) {
                            this.initMap();
                            this.mapReady = true;
                        }

                        if (this.map) {
                            setTimeout(() => {
                                this.map.invalidateSize();
                                this.renderMapMarkers();
                            }, 100);
                        }
                    });
                },

                initMap() {
                    const center = [25.3, 55.4];
                    this.map = L.map('storesMap', {
                        zoomControl: true
                    }).setView(center, 10);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(this.map);

                    this.map.on('click', (e) => {
                        this.setUserLocation(e.latlng.lat, e.latlng.lng, false);
                    });
                },

                clearMarkers() {
                    this.mapMarkers.forEach(m => m.remove());
                    this.mapMarkers = [];
                },

                escapeHtml(str) {
                    return String(str).replace(/[&<>"']/g, (m) => ({
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;'
                    } [m]));
                },

                renderMapMarkers() {
                    if (!this.map) return;
                    this.clearMarkers();

                    if (this.userLat !== null && this.userLng !== null) {
                        const me = L.circleMarker([this.userLat, this.userLng], {
                            radius: 7,
                            color: '#1b1e6c',
                            fillColor: '#1b1e6c',
                            fillOpacity: .25,
                            weight: 2
                        }).addTo(this.map);
                        me.bindPopup(@json(__('message.My location / Selected location')));
                        this.mapMarkers.push(me);
                    }

                    const selectedId = this.selected.store?.id ?? null;

                    this.filteredStores().forEach(s => {
                        if (s.lat === null || s.lng === null) return;

                        const isSelected = selectedId === s.id;

                        const marker = L.marker([s.lat, s.lng]).addTo(this.map);
                        marker.on('click', () => this.selectStore(s));
                        marker.bindPopup(
                            `<b style="color:#1b1e6c">${this.escapeHtml(s.name||'')}</b><br>${this.escapeHtml(s.address||'')}`
                        );

                        if (isSelected) marker.openPopup();
                        this.mapMarkers.push(marker);
                    });
                },

                scheduleDays() {
                    const sch = this.selected.store?.schedule;
                    const daysObj = sch?.days || {};
                    const dates = Object.keys(daysObj).sort();
                    return dates.map(date => {
                        const slots = daysObj[date]?.slots || {};
                        const avail = Object.values(slots).filter(x => (Number(x.cap || 0) - Number(x.booked ||
                            0)) > 0).length;
                        return {
                            date,
                            label: this.formatArabicDate(date),
                            available: avail
                        };
                    });
                },

                selectDay(date) {
                    this.selected.day = date;
                    this.selected.slot = null;
                },

                scheduleSlotsForSelectedDay() {
                    const sch = this.selected.store?.schedule;
                    const day = this.selected.day;
                    if (!sch || !day) return [];
                    const slotsObj = sch.days?.[day]?.slots || {};
                    return Object.keys(slotsObj).sort().map(t => {
                        const cap = Number(slotsObj[t]?.cap ?? 0);
                        const booked = Number(slotsObj[t]?.booked ?? 0);
                        return {
                            time: t,
                            cap,
                            booked,
                            available: (cap - booked) > 0
                        };
                    });
                },

                selectSlot(time) {
                    this.selected.slot = time;
                },

                formatArabicDate(dateStr) {
                    const d = new Date(dateStr + 'T00:00:00');
                    const locale = document.documentElement.lang || 'en';
                    try {
                        const useLocale = (locale.startsWith('ar')) ? 'ar' : 'en';
                        return new Intl.DateTimeFormat(useLocale, {
                            weekday: 'short',
                            month: 'short',
                            day: 'numeric'
                        }).format(d);
                    } catch {
                        return dateStr;
                    }
                },

                lensExtraPrice() {
                    const catPrice = this.selected.category?.price || 0;
                    const addonPrice = this.selected.addons.reduce((sum, a) => sum + (parseFloat(a.price) || 0), 0);
                    return catPrice + addonPrice;
                },

                qty() {
                    return parseInt(document.getElementById('quantity')?.value || '1');
                },

                finalTotal() {
                    const total = (this.basePrice + this.lensExtraPrice()) * this.qty();
                    return total.toFixed(2);
                },

                updateFinalPriceUI(hide = false) {
                    const el = document.getElementById('finalPriceDisplay');
                    const val = document.getElementById('finalPriceValue');
                    if (!el || !val) return;

                    if (hide) {
                        el.classList.add('hidden');
                        return;
                    }

                    if (this.selected.category) {
                        el.classList.remove('hidden');
                        val.textContent = this.finalTotal();
                    } else {
                        el.classList.add('hidden');
                    }
                },

                /**
                 * Le sélecteur de couleur n'existe qu'à l'étape 3 : bloquer la validation
                 * à l'étape 4 laissait le client devant une erreur sans rien à corriger.
                 * On retombe donc sur la première couleur au lieu d'échouer.
                 */
                validateAddonColorIfNeeded() {
                    const chosen = this.selected.addons[0];
                    if (!chosen) return true;

                    const addonDef = (this.addonsForSelectedCategory() || []).find(a => a.id === chosen.id);
                    if (!addonDef || !addonDef.options || !addonDef.options.length) return true;

                    if (!chosen.option) {
                        this.setAddonOption(addonDef.id, addonDef.options[0].value);
                    }

                    return true;
                },

                baseValidateBeforeSubmit() {
                    if (!this.selected.type || !this.selected.category) {
                        if (typeof showMessage === 'function') showMessage('error', @json(__('message.Please select lens type and category')));
                        return false;
                    }
                    this.validateAddonColorIfNeeded();
                    return true;
                },

                confirmAddToCartForStore() {
                    if (!this.baseValidateBeforeSubmit()) return;
                    if (!this.selected.store || !this.selected.day || !this.selected.slot) {
                        if (typeof showMessage === 'function') showMessage('error', @json(__('message.Please select store, day and time')));
                        return;
                    }

                    const payload = {
                        type: this.selected.type,
                        category: this.selected.category.id,
                        category_price: this.selected.category.price,
                        addon: this.selected.addons[0] || null,
                        flow: 'store_measurement',
                        store_id: this.selected.store.id,
                        store: this.selected.store,
                        schedule: {
                            day: this.selected.day,
                            slot: this.selected.slot
                        },
                        booking_id: this.booking.id,
                        customer_name: this.booking.name.trim(),
                        customer_phone: this.booking.phone.trim(),
                        total: this.finalTotal(),
                    };

                    const qty = this.qty();
                    const extra = this.lensExtraPrice();

                    try {
                        addToCart(this.productId, 'product', qty, JSON.stringify(payload), extra);
                    } catch (e) {
                        console.error(e);
                        alert('addToCart is not defined or failed.');
                        return;
                    }

                    this.updateFinalPriceUI();
                    this.close();
                    if (typeof showMessage === 'function') showMessage('success', @json(__('message.Added to cart (store measurement)')));
                },

                confirmAddToCartSingleVision() {
                    if (!this.baseValidateBeforeSubmit()) return;

                    if (this.prescriptionMode === 'book') {
                        if (!this.selected.store || !this.selected.day || !this.selected.slot) {
                            if (typeof showMessage === 'function') showMessage('error', @json(__('message.Please select store, day and time')));
                            return;
                        }

                        const payload = {
                            type: this.selected.type,
                            category: this.selected.category.id,
                            category_price: this.selected.category.price,
                            addon: this.selected.addons[0] || null,
                            flow: 'book_eye_test',
                            store_id: this.selected.store.id,
                            store: this.selected.store,
                            schedule: {
                                day: this.selected.day,
                                slot: this.selected.slot
                            },
                            booking_id: this.booking.id,
                            customer_name: this.booking.name.trim(),
                            customer_phone: this.booking.phone.trim(),
                            total: this.finalTotal(),
                        };

                        const qty = this.qty();
                        const extra = this.lensExtraPrice();

                        try {
                            addToCart(this.productId, 'product', qty, JSON.stringify(payload), extra);
                        } catch (e) {
                            console.error(e);
                            alert('addToCart is not defined or failed.');
                            return;
                        }

                        this.updateFinalPriceUI();
                        this.close();
                        if (typeof showMessage === 'function') showMessage('success', @json(__('message.Added to cart (book eye test)')));
                        return;
                    }

                    const payload = {
                        type: this.selected.type,
                        category: this.selected.category.id,
                        category_price: this.selected.category.price,
                        addon: this.selected.addons[0] || null,
                        flow: 'add_prescription',
                        prescription_input: this.prescriptionInput,

                        prescription: this.prescription,
                        total: this.finalTotal(),
                    };

                    const qty = this.qty();
                    const extra = this.lensExtraPrice();

                    try {
                        addToCart(this.productId, 'product', qty, JSON.stringify(payload), extra);
                    } catch (e) {
                        console.error(e);
                        alert('addToCart is not defined or failed.');
                        return;
                    }

                    this.updateFinalPriceUI();
                    this.close();
                    if (typeof showMessage === 'function') showMessage('success', @json(__('message.Added to cart with prescription')));
                },
            }
        }
    </script>

@endsection
