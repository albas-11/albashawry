<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AL Bashawri')</title>
    <link rel="icon"
        href="{{ $globalSettings && $globalSettings->favicon
            ? asset('images/settings/' . $globalSettings->favicon)
            : asset('images/default-favicon.ico') }}"
        type="image/x-icon">
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&family=Dancing+Script:wght@400..700&family=Roboto:ital,wght@0,100..900;1,100..900&family=Winky+Sans:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if (app()->getLocale() === 'ar')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @else
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @endif
    @yield('styles')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('admin.pixels.head')

    <style>
        /* تحسينات إضافية للموبايل */
        @media (max-width: 768px) {
            .mobile-header-shadow {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            }
        }
    </style>
    <style>
    /* 1. استدعاء خط Cairo بأوزان متعددة */
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap');

    /* 2. تطبيق الخط إجبارياً على كافة النصوص */
    body, h1, h2, h3, h4, h5, h6, p, span, a, li, button, input, select, textarea, div {
        font-family: 'Cairo', sans-serif !important;
    }

    /* 3. تكبير حجم الخط الأساسي للموقع */
    html, body {
        font-size: 16px !important; /* الحجم الطبيعي، يمكنك زيادته لـ 18px إذا أردت خطاً ضخماً */
        line-height: 1.6 !important; /* زيادة المسافة بين الأسطر للقراءة المريحة */
    }

    /* 4. تنسيقات خاصة للعناوين لتكون بارزة */
    h1, h2, h3, h4, h5, h6 {
        font-weight: 700 !important;
        margin-bottom: 10px !important;
    }

    /* 5. تحسين ظهور الروابط والأزرار */
    a, button, .btn {
        font-weight: 600 !important;
        letter-spacing: 0px !important; /* إلغاء التباعد الزائد في الأحرف العربية */
    }
    
    /* 6. تحسين شكل القوائم والنصوص الصغيرة */
    .text-xs {
        font-size: 12px !important; /* تكبير النصوص الصغيرة جداً قليلاً */
    }
    
    .text-sm {
        font-size: 14px !important;
    }
</style>
</head>

<body class="font-sans antialiased bg-gray-50" style="background-color: #f5f5f5;">
    
    <header class="bg-white shadow-sm z-50 sticky top-0" x-data="{ mobileMenuOpen: false }">
        <div class="w-full px-0">
            
            <div class="hidden md:block bg-[#920a69] text-white py-2.5 w-full border-b border-white/10">
                <div class="max-w-7xl mx-auto px-4">
                    <div x-data="{
                        messages: [
                            '@lang('message.Free Shipping for first 30 days')',
                            '@lang('message.Use code for 10% off your first order')',
                            '@lang('message.Shop online, click and collect from our stores')'
                        ],
                        index: 0,
                        next() { this.index = (this.index + 1) % this.messages.length; },
                        prev() { this.index = (this.index - 1 + this.messages.length) % this.messages.length; }
                    }" x-init="setInterval(() => next(), 5000)"
                        class="flex items-center justify-between relative select-none">

                        <button @click="prev()" class="text-white/40 hover:text-white transition-colors duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        <div class="flex-1 text-center overflow-hidden relative h-5">
                            <template x-for="(msg, i) in messages" :key="i">
                                <div x-show="index === i" x-transition:enter="transition ease-out duration-500"
                                    x-transition:enter-start="opacity-0 transform translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    x-transition:leave="transition ease-in duration-500"
                                    x-transition:leave-start="opacity-100 transform translate-y-0"
                                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                                    class="absolute inset-0 flex justify-center items-center">
                                    <span class="text-[10px] font-bold uppercase tracking-[0.25em] whitespace-nowrap">
                                        <span x-text="msg"></span>
                                    </span>
                                </div>
                            </template>
                        </div>

                        <button @click="next()" class="text-white/40 hover:text-white transition-colors duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                    </div>
                </div>
            </div>

            <div class="md:hidden flex items-center justify-between px-4 py-3 bg-white mobile-header-shadow w-full">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-800 p-1 focus:outline-none">
                    <svg x-show="!mobileMenuOpen" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <a href="{{ route('home') }}" class="flex-shrink-0">
                    @php
                        $defaultLogo = asset('images/logo.png');
                        $savedLogo = $globalSettings && $globalSettings->logo
                            ? public_path('images/settings/' . $globalSettings->logo)
                            : null;
                        $logoSrc = $savedLogo && file_exists($savedLogo)
                            ? asset('images/settings/' . $globalSettings->logo)
                            : $defaultLogo;
                    @endphp
                    <img src="{{ $logoSrc }}" alt="Logo" class="h-8 w-auto">
                </a>

                <div class="flex items-center gap-3">
                    <a href="{{ route('cart.index') }}" class="relative text-gray-800 p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                        </svg>
                        <span class="absolute -top-1 -right-1 bg-[#2E3192] text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center border-2 border-white">
                            {{ app(\App\Services\CartService::class)->getCount() }}
                        </span>
                    </a>
                </div>
            </div>
            <div class="hidden md:flex flex-col sm:flex-row items-center justify-between w-full px-4 sm:px-6 py-2">
                <div class="flex items-center mb-2 sm:mb-0">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-900 flex items-center">
                        <img src="{{ $logoSrc }}" alt="AL Bashawri Logo" class="h-10 w-auto mr-2">
                    </a>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-2 sm:gap-4 w-full sm:w-auto">
                    <a href="{{ route('stores.index') }}"
                        class="group flex items-center gap-2 px-6 py-2 sm:py-3 rounded-full bg-[#2E3192] text-white font-medium transition-all duration-300 hover:bg-[#3a3eab] hover:shadow-lg hover:shadow-indigo-200 active:scale-95 w-full sm:w-auto justify-center">

                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-geo-alt transition-transform duration-300 group-hover:-translate-y-1"
                            viewBox="0 0 16 16">
                            <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10" />
                            <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                        </svg>

                        <span class="relative">
                            @lang('message.Our Stores')
                        </span>
                    </a>

                    <div class="relative inline-block text-left" id="lang-dropdown-container">
                        <button onclick="toggleLangMenu()"
                            class="flex items-center gap-2 px-4 py-2 border-none rounded-2xl bg-gray-50 text-gray-700 font-bold text-sm hover:bg-gray-100 transition-all active:scale-95 shadow-sm">

                            @if (app()->getLocale() == 'en')
                                <img src="https://flagcdn.com/w40/gb.png"
                                    class="w-5 h-5 rounded-full object-cover shadow-sm" alt="EN">
                                <span class="ml-1 uppercase">English</span>
                            @else
                                <img src="https://flagcdn.com/w40/sa.png"
                                    class="w-5 h-5 rounded-full object-cover shadow-sm" alt="AR">
                                <span class="ml-1">العربية</span>
                            @endif

                            <svg id="lang-arrow" class="w-4 h-4 ml-1 text-gray-400 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div id="lang-switch"
                            class="hidden absolute right-0 mt-3 w-40 bg-white rounded-2xl shadow-2xl border border-gray-50 z-[100] overflow-hidden animate-in fade-in zoom-in duration-200">

                            <div class="py-2">
                                <a href="{{ route('language.switch', 'en') }}"
                                    class="flex items-center px-4 py-3 text-sm font-semibold transition-colors hover:bg-blue-50 {{ app()->getLocale() == 'en' ? 'text-[#2E3192] bg-blue-50/50' : 'text-gray-600' }}">
                                    <img src="https://flagcdn.com/w40/gb.png"
                                        class="w-5 h-5 rounded-full object-cover mr-3" alt="EN">
                                    <span class="flex-1">English</span>
                                    @if (app()->getLocale() == 'en')
                                        <i class="fa-solid fa-check text-xs"></i>
                                    @endif
                                </a>

                                <a href="{{ route('language.switch', 'ar') }}"
                                    class="flex items-center px-4 py-3 text-sm font-semibold transition-colors hover:bg-blue-50 {{ app()->getLocale() == 'ar' ? 'text-[#2E3192] bg-blue-50/50' : 'text-gray-600' }}">
                                    <img src="https://flagcdn.com/w40/sa.png"
                                        class="w-5 h-5 rounded-full object-cover mr-3" alt="AR">
                                    <span class="flex-1 text-right">العربية</span>
                                    @if (app()->getLocale() == 'ar')
                                        <i class="fa-solid fa-check text-xs"></i>
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-t border-gray-200 my-2 hidden md:block">

        <div class="w-full bg-white shadow-sm">
            <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                class="md:hidden border-t border-gray-100 px-4 py-2 space-y-2 bg-white w-full absolute z-50 shadow-lg left-0 top-full max-h-[85vh] overflow-y-auto">

                <a href="{{ route('home') }}" class="block py-2 font-medium text-gray-700">@lang('message.Home')</a>
                <div x-data="{ expanded: false }" class="border-b border-gray-100">
                    <button @click="expanded = !expanded"
                        class="flex justify-between items-center w-full py-2 font-medium text-gray-700">
                        @lang('message.Sunglasses')
                        <svg :class="{ 'rotate-180': expanded }" class="w-4 h-4 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="expanded" class="pl-4 pb-2 space-y-2 text-sm text-gray-600">
                        <a href="{{ route('products.sunglasses') }}" class="block py-1">@lang('message.All Sunglasses')</a>
                        <a href="{{ route('products.sunglasses', ['gender' => 'men']) }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50 hover:text-[#9E0168] transition">
                            <img src="{{ asset('images/gender/sunglasses_men.jpg') }}" alt="Men"
                                class="w-6 h-6 object-contain">@lang('message.Men')</a>
                        <a href="{{ route('products.sunglasses', ['gender' => 'women']) }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50 hover:text-[#9E0168] transition">
                            <img src="{{ asset('images/gender/sunglasses_women.jpg') }}" alt="Men"
                                class="w-6 h-6 object-contain">@lang('message.Women')</a>
                        <a href="{{ route('products.sunglasses', ['gender' => 'boys']) }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50 hover:text-[#9E0168] transition">
                            <img src="{{ asset('images/gender/sunglasses_boy.jpg') }}" alt="Men"
                                class="w-6 h-6 object-contain">@lang('message.Boys')</a>
                        <a href="{{ route('products.sunglasses', ['gender' => 'girls']) }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50 hover:text-[#9E0168] transition">
                            <img src="{{ asset('images/gender/sunglasses_girl.jpg') }}" alt="Men"
                                class="w-6 h-6 object-contain">@lang('message.Girls')</a>
                    </div>
                </div>

                <div x-data="{ expanded: false }" class="border-b border-gray-100">
                    <button @click="expanded = !expanded"
                        class="flex justify-between items-center w-full py-2 font-medium text-gray-700">
                        @lang('message.eyeglasses')
                        <svg :class="{ 'rotate-180': expanded }" class="w-4 h-4 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="expanded" class="pl-4 pb-2 space-y-2 text-sm text-gray-600">
                        <a href="{{ route('products.eyeglasses') }}" class="block py-1">@lang('message.All Eyeglasses')</a>
                        <a href="{{ route('products.eyeglasses', ['gender' => 'men']) }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50 hover:text-[#9E0168] transition">
                            <img src="{{ asset('images/gender/eyeglasses_men.jpg') }}" alt="Men"
                                class="w-6 h-6 object-contain">@lang('message.Men')</a>
                        <a href="{{ route('products.eyeglasses', ['gender' => 'women']) }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50 hover:text-[#9E0168] transition">
                            <img src="{{ asset('images/gender/eyeglasses_women.jpg') }}" alt="Men"
                                class="w-6 h-6 object-contain">@lang('message.Women')</a>
                        <a href="{{ route('products.eyeglasses', ['gender' => 'boys']) }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50 hover:text-[#9E0168] transition">
                            <img src="{{ asset('images/gender/eyeglasses_boy.jpg') }}" alt="Men"
                                class="w-6 h-6 object-contain">@lang('message.Boys')</a>
                        <a href="{{ route('products.eyeglasses', ['gender' => 'girls']) }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50 hover:text-[#9E0168] transition">
                            <img src="{{ asset('images/gender/eyeglasses_girl.jpg') }}" alt="Men"
                                class="w-6 h-6 object-contain">@lang('message.Girls')</a>
                    </div>
                </div>

                <div x-data="{ expanded: false }" class="border-b border-gray-100">
                    <button @click="expanded = !expanded"
                        class="flex justify-between items-center w-full py-2 font-medium text-gray-700">
                        @lang('message.Clip Ons')
                        <svg :class="{ 'rotate-180': expanded }" class="w-4 h-4 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="expanded" class="pl-4 pb-2 space-y-2 text-sm text-gray-600">
                        <a href="{{ route('products.clipon') }}" class="block py-1">@lang('message.All Clip Ons')</a>
                        <a href="{{ route('products.clipon', ['gender' => 'men']) }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50 hover:text-[#9E0168] transition">
                            <img src="{{ asset('images/gender/clipon_men.jpg') }}" alt="Men"
                                class="w-6 h-6 object-contain">@lang('message.Men')</a>
                        <a href="{{ route('products.clipon', ['gender' => 'women']) }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50 hover:text-[#9E0168] transition">
                            <img src="{{ asset('images/gender/clipon_women.jpg') }}" alt="Men"
                                class="w-6 h-6 object-contain">@lang('message.Women')</a>
                        <a href="{{ route('products.clipon', ['gender' => 'boys']) }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50 hover:text-[#9E0168] transition">
                            <img src="{{ asset('images/gender/clipon_boy.jpg') }}" alt="Men"
                                class="w-6 h-6 object-contain">@lang('message.Boys')</a>
                        <a href="{{ route('products.clipon', ['gender' => 'girls']) }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50 hover:text-[#9E0168] transition">
                            <img src="{{ asset('images/gender/clipon_girl.jpg') }}" alt="Men"
                                class="w-6 h-6 object-contain">@lang('message.Girls')</a>
                    </div>
                </div>

                <a href="{{ route('products.contact-lenses') }}"
                    class="block py-2 font-medium text-gray-700 border-b border-gray-100">@lang('message.Contact Lenses')</a>
                <a href="{{ route('products.accessories') }}"
                    class="block py-2 font-medium text-gray-700 border-b border-gray-100">@lang('message.Accessories')</a>
                <a href="{{ route('brands.index') }}"
                    class="block py-2 font-medium text-gray-700 border-b border-gray-100">@lang('message.Brands')</a>
                
                <div class="py-3 border-b border-gray-100">
                    <p class="text-xs text-gray-400 uppercase tracking-widest mb-2">@lang('message.Language')</p>
                    <div class="flex gap-4">
                        <a href="{{ route('language.switch', 'ar') }}" class="{{ app()->getLocale() == 'ar' ? 'text-[#2E3192] font-bold' : 'text-gray-500' }}">العربية</a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('language.switch', 'en') }}" class="{{ app()->getLocale() == 'en' ? 'text-[#2E3192] font-bold' : 'text-gray-500' }}">English</a>
                    </div>
                </div>

                <div class="pt-4 pb-4">
                    @auth
                        <a href="{{ route('profile.edit') }}"
                            class="block py-2 text-[#2E3192] font-semibold">@lang('message.Profile')</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block py-2 text-red-600">@lang('message.Logout')</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block py-2 text-[#2E3192] font-bold">@lang('message.Sign in')</a>
                    @endauth
                </div>
            </div>

            <nav class="hidden md:flex items-center justify-center space-x-8 text-base md:text-sm"
                x-data="{ activeMenu: null }">
               <div class="relative group" @mouseenter="activeMenu = 'sunglasses'" @mouseleave="activeMenu = null">

                    <a href="{{ route('products.sunglasses') }}"
                        class="relative px-5 py-5 text-gray-700 hover:text-[#9E0168] transition-all duration-300 font-semibold flex items-center rtl:flex-row-reverse group">
                        @lang('message.Sunglasses')
                        <svg class="w-3 h-3 ltr:ml-2 rtl:mr-2 transform transition-transform duration-300 group-hover:rotate-180 opacity-50 group-hover:opacity-100"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                        <span
                            class="absolute bottom-3 ltr:left-1/2 rtl:right-1/2 ltr:-translate-x-1/2 rtl:translate-x-1/2 w-0 h-0.5 bg-[#9E0168] transition-all duration-300 group-hover:w-3/4">
                        </span>
                    </a>
                    <div x-show="activeMenu === 'sunglasses'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        class="absolute ltr:left-0 rtl:right-0 mt-0 w-[550px] bg-white/95 backdrop-blur-md shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-gray-100 z-50 p-6 overflow-hidden">
                        <div class="flex gap-8 rtl:flex-row-reverse">
                            <div class="w-1/2 text-left rtl:text-right">
                                <h4 class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-4 px-2">
                                    @lang('message.Shop by Gender')
                                </h4>

                                <div class="space-y-1">
                                    @foreach (['men', 'women', 'girls', 'boys'] as $gender)
                                        <a href="{{ route('products.sunglasses', ['gender' => $gender]) }}"
                                            class="group/item flex items-center rtl:flex-row-reverse gap-4 px-3 py-3 rounded-xl hover:bg-[#9E0168]/5 transition-all duration-300">
                                            <div
                                                class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center overflow-hidden border border-gray-50 group-hover/item:border-[#9E0168]/20 transition-colors">
                                                <img src="{{ asset('images/gender/sunglasses_' . ($gender == 'girls' ? 'girl' : ($gender == 'boys' ? 'boy' : $gender)) . '.jpg') }}"
                                                    class="w-full h-full object-cover group-hover/item:scale-110 transition-transform duration-500"
                                                    alt="{{ $gender }}">
                                            </div>
                                            <span
                                                class="text-sm font-medium text-gray-700 group-hover/item:text-[#9E0168] transition-colors">
                                                @lang('message.' . ucfirst($gender))
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            <div
                                class="w-1/2
                        ltr:border-l rtl:border-r border-gray-50
                        ltr:pl-8 rtl:pr-8
                        text-left rtl:text-right">

                                <h4
                                    class="text-[10px] uppercase font-black tracking-widest
                           text-gray-400 mb-4">
                                    @lang('message.Shop by Brand')
                                </h4>

                                <div
                                    class="flex flex-col space-y-1 max-h-64
                            overflow-y-auto
                            ltr:pr-2 rtl:pl-2
                            custom-scrollbar">

                                    @foreach ($brands as $brand)
                                        <a href="{{ route('products.sunglasses', ['brand' => $brand->slug]) }}"
                                            class="flex items-center rtl:flex-row-reverse gap-3
                                  px-3 py-2 rounded-lg text-sm
                                  text-gray-600 hover:text-black
                                  hover:bg-gray-50 transition-all duration-200">

                                            <img src="{{ asset($brand->logo) }}" alt="{{ $brand->name }}"
                                                class="w-5 h-5 object-contain grayscale
                                     hover:grayscale-0 transition-all">

                                            <span class="truncate font-medium">
                                                {{ $brand->name }}
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="relative group" @mouseenter="activeMenu = 'glasses'" @mouseleave="activeMenu = null">

                    <a href="{{ route('products.eyeglasses') }}"
                        class="relative px-5 py-5 text-gray-700 hover:text-[#9E0168]
             transition-all duration-300 font-semibold
             flex items-center rtl:flex-row-reverse group">

                        @lang('message.eyeglasses')

                        <svg class="w-3 h-3 ltr:ml-2 rtl:mr-2
                   transform transition-transform duration-300
                   group-hover:rotate-180 opacity-50 group-hover:opacity-100"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M19 9l-7 7-7-7" />
                        </svg>

                        <span
                            class="absolute bottom-3
                      ltr:left-1/2 rtl:right-1/2
                      ltr:-translate-x-1/2 rtl:translate-x-1/2
                      w-0 h-0.5 bg-[#9E0168]
                      transition-all duration-300 group-hover:w-3/4">
                        </span>
                    </a>

                    <div x-show="activeMenu === 'glasses'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        class="absolute ltr:left-0 rtl:right-0 mt-0 w-[550px]
                bg-white/95 backdrop-blur-md
                shadow-[0_20px_50px_rgba(0,0,0,0.1)]
                border border-gray-100 z-50 p-6">

                        <div class="flex gap-8 rtl:flex-row-reverse">

                            {{-- Gender --}}
                            <div class="w-1/2 text-left rtl:text-right">
                                <h4 class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-4 px-2">
                                    @lang('message.Shop by Gender')
                                </h4>

                                @php
                                    $genders = [
                                        'men' => 'eyeglasses_men.jpg',
                                        'women' => 'eyeglasses_women.jpg',
                                        'girls' => 'eyeglasses_girl.jpg',
                                        'boys' => 'eyeglasses_boy.jpg',
                                    ];
                                @endphp

                                <div class="space-y-1">
                                    @foreach ($genders as $key => $image)
                                        <a href="{{ route('products.eyeglasses', ['gender' => $key]) }}"
                                            class="group/item flex items-center rtl:flex-row-reverse gap-4
                                  px-3 py-3 rounded-xl hover:bg-[#9E0168]/5 transition-all">

                                            <div
                                                class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center
                                     overflow-hidden border border-gray-100
                                     group-hover/item:border-[#9E0168]/20">
                                                <img src="{{ asset('images/gender/' . $image) }}"
                                                    class="w-full h-full object-cover
                                           group-hover/item:scale-110 transition-transform duration-500">
                                            </div>

                                            <span
                                                class="text-sm font-medium text-gray-700
                                      group-hover/item:text-[#9E0168]">
                                                @lang('message.' . ucfirst($key))
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Brand --}}
                            <div
                                class="w-1/2 ltr:border-l rtl:border-r border-gray-50
                        ltr:pl-8 rtl:pr-8 text-left rtl:text-right">

                                <h4 class="text-[10px] uppercase font-black tracking-widest
                           text-gray-400 mb-4">
                                    @lang('message.Shop by Brand')
                                </h4>

                                <div
                                    class="flex flex-col space-y-1 max-h-64
                            overflow-y-auto
                            ltr:pr-2 rtl:pl-2
                            custom-scrollbar">

                                    @foreach ($brands as $brand)
                                        <a href="{{ route('products.eyeglasses', ['brand' => $brand->slug]) }}"
                                            class="flex items-center rtl:flex-row-reverse gap-3
                                  px-3 py-2 rounded-lg text-sm
                                  text-gray-600 hover:text-black
                                  hover:bg-gray-50 transition-all duration-200">

                                            <img src="{{ asset($brand->logo) }}" alt="{{ $brand->name }}"
                                                class="w-5 h-5 object-contain grayscale
                                     hover:grayscale-0 transition-all">

                                            <span class="truncate font-medium">
                                                {{ $brand->name }}
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="relative group" @mouseenter="activeMenu = 'clipon'" @mouseleave="activeMenu = null">

                    <a href="{{ route('products.clipon') }}"
                        class="relative px-5 py-5 text-gray-700 hover:text-[#9E0168]
             transition-all duration-300 font-semibold
             flex items-center rtl:flex-row-reverse group">

                        @lang('message.Clip Ons')

                        <svg class="w-3 h-3 ltr:ml-2 rtl:mr-2
                   transform transition-transform duration-300
                   group-hover:rotate-180 opacity-50 group-hover:opacity-100"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M19 9l-7 7-7-7" />
                        </svg>

                        <span
                            class="absolute bottom-3
                      ltr:left-1/2 rtl:right-1/2
                      ltr:-translate-x-1/2 rtl:translate-x-1/2
                      w-0 h-0.5 bg-[#9E0168]
                      transition-all duration-300 group-hover:w-3/4">
                        </span>
                    </a>

                    <div x-show="activeMenu === 'clipon'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        class="absolute ltr:left-0 rtl:right-0 mt-0 w-[550px]
                bg-white/95 backdrop-blur-md
                shadow-[0_20px_50px_rgba(0,0,0,0.1)]
                border border-gray-100 z-50 p-6">

                        <div class="flex gap-8 rtl:flex-row-reverse">

                            {{-- Gender --}}
                            <div class="w-1/2 text-left rtl:text-right">
                                <h4 class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-4 px-2">
                                    @lang('message.Shop by Gender')
                                </h4>

                                @php
                                    $cliponGenders = [
                                        'men' => 'clipon_men.jpg',
                                        'women' => 'clipon_women.jpg',
                                        'girls' => 'clipon_girl.jpg',
                                        'boys' => 'clipon_boy.jpg',
                                    ];
                                @endphp

                                <div class="space-y-1">
                                    @foreach ($cliponGenders as $key => $image)
                                        <a href="{{ route('products.clipon', ['gender' => $key]) }}"
                                            class="group/item flex items-center rtl:flex-row-reverse gap-4
                                  px-3 py-3 rounded-xl hover:bg-[#9E0168]/5">

                                            <div
                                                class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center
                                     overflow-hidden border border-gray-100
                                     group-hover/item:border-[#9E0168]/20">
                                                <img src="{{ asset('images/gender/' . $image) }}"
                                                    class="w-full h-full object-cover
                                           group-hover/item:scale-110 transition-transform duration-500">
                                            </div>

                                            <span
                                                class="text-sm font-medium text-gray-700
                                      group-hover/item:text-[#9E0168]">
                                                @lang('message.' . ucfirst($key))
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Brand --}}
                            <div
                                class="w-1/2 ltr:border-l rtl:border-r border-gray-50
                        ltr:pl-8 rtl:pr-8 text-left rtl:text-right">

                                <h4 class="text-[10px] uppercase font-black tracking-widest
                           text-gray-400 mb-4">
                                    @lang('message.Shop by Brand')
                                </h4>

                                <div
                                    class="flex flex-col space-y-1 max-h-64
                            overflow-y-auto
                            ltr:pr-2 rtl:pl-2
                            custom-scrollbar">

                                    @foreach ($brands as $brand)
                                        <a href="{{ route('products.clipon', ['brand' => $brand->slug]) }}"
                                            class="group/brand flex items-center rtl:flex-row-reverse gap-3
                                  px-3 py-2 rounded-lg text-sm
                                  text-gray-600 hover:text-black hover:bg-gray-50">

                                            <img src="{{ asset($brand->logo) }}"
                                                class="w-5 h-5 object-contain grayscale opacity-60
                                     group-hover/brand:grayscale-0
                                     group-hover/brand:opacity-100 transition-all">

                                            <span class="truncate font-medium">{{ $brand->name }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <a href="{{ route('products.contact-lenses') }}"
                    class="group relative px-5 py-5 text-gray-700 hover:text-[#9E0168] transition-all duration-300 font-semibold flex items-center">
                    @lang('message.Contact Lenses')
                    <span
                        class="absolute bottom-3 left-1/2 -translate-x-1/2 w-0 h-0.5 bg-[#9E0168] transition-all duration-300 group-hover:w-3/4"></span>
                </a>

                <a href="{{ route('products.accessories') }}"
                    class="group relative px-5 py-5 text-gray-700 hover:text-[#9E0168] transition-all duration-300 font-semibold flex items-center">
                    @lang('message.Accessories')
                    <span
                        class="absolute bottom-3 left-1/2 -translate-x-1/2 w-0 h-0.5 bg-[#9E0168] transition-all duration-300 group-hover:w-3/4"></span>
                </a>

                <div class="relative group" @mouseenter="activeMenu = 'brands'" @mouseleave="activeMenu = null">

                    <a href="{{ route('brands.index') }}"
                        class="relative px-5 py-5 text-gray-700 hover:text-[#9E0168]
             transition-all duration-300 font-semibold
             flex items-center rtl:flex-row-reverse group">

                        @lang('message.Brands')

                        {{-- Arrow --}}
                        <svg class="w-3 h-3 ltr:ml-2 rtl:mr-2
                   transform transition-transform duration-300
                   group-hover:rotate-180 opacity-50 group-hover:opacity-100"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M19 9l-7 7-7-7" />
                        </svg>

                        {{-- Underline --}}
                        <span
                            class="absolute bottom-3 ltr:left-1/2 rtl:right-1/2 ltr:-translate-x-1/2 rtl:translate-x-1/2 w-0 h-0.5 bg-[#9E0168] transition-all duration-300 group-hover:w-3/4">
                        </span>
                    </a>

                    {{-- Dropdown --}}
                    <div x-show="activeMenu === 'brands'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        class="absolute ltr:left-0 rtl:right-0 mt-0 w-[450px] bg-white/95 backdrop-blur-md shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-gray-100 z-50 p-6">

                        <h4
                            class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-4 px-2 text-left rtl:text-right">
                            @lang('message.Our Luxury Brands')
                        </h4>
                        <div
                            class="grid grid-cols-2 gap-x-4 gap-y-1 max-h-[400px] overflow-y-auto ltr:pr-2 rtl:pl-2 custom-scrollbar">
                            @foreach ($brands as $brand)
                                <a href="{{ route('brand', $brand->slug) }}"
                                    class="group/brand flex items-center rtl:flex-row-reverse gap-3 p-2 rounded-xl hover:bg-gray-50 transition-all duration-200">
                                    <div
                                        class="w-10 h-10 flex-shrink-0 rounded-full border border-gray-100 bg-white p-1.5 shadow-sm group-hover/brand:border-[#9E0168]/30 transition-colors">
                                        <img src="{{ asset($brand->logo) }}" alt="{{ $brand->name }}"
                                            class="w-full h-full object-contain grayscale group-hover/brand:grayscale-0 transition-all">
                                    </div>

                                    <div class="flex flex-col text-left rtl:text-right">
                                        <span
                                            class="text-sm font-semibold text-gray-700 group-hover/brand:text-[#9E0168] transition-colors">
                                            {{ $brand->name }}
                                        </span>
                                        <span class="text-[10px] text-gray-400 uppercase tracking-tighter">
                                            Collection
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                            class="flex items-center gap-3 px-1 py-1 rounded-full transition-all group">
                            <div
                                class="w-9 h-9 rounded-full bg-gray-50 flex items-center justify-center border border-gray-100 group-hover:border-[#9E0168]/20 transition-all">
                                <svg class="w-5 h-5 text-gray-500 group-hover:text-[#9E0168]" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>


                            <svg class="w-3 h-3 text-gray-300 transition-transform duration-300"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50">

                            <div class="p-2">
                                @if (auth()->user()->hasAnyRole(['Admin', 'User']))
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="flex items-center px-4 py-3 text-xs font-bold uppercase tracking-widest text-[#920a69] hover:bg-gray-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        @lang('message.Dashboard')
                                    </a>
                                @endif

                                <a href="{{ route('account.orders.index') }}"
                                    class="flex items-center px-4 py-3 text-xs font-bold uppercase tracking-widest text-gray-600 hover:text-[#9E0168] hover:bg-gray-50 rounded-lg transition-all">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    @lang('message.My Orders')
                                </a>

                                <a href="{{ route('profile.edit') }}"
                                    class="flex items-center px-4 py-3 text-xs font-bold uppercase tracking-widest text-gray-600 hover:text-[#9E0168] hover:bg-gray-50 rounded-lg transition-all">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    @lang('message.Profile')
                                </a>
                            </div>

                            <div class="border-t border-gray-50 p-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="flex items-center w-full px-4 py-3 text-xs font-bold uppercase tracking-widest text-red-500 hover:bg-red-50 rounded-lg transition-all">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        @lang('message.Logout')
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="flex items-center gap-2 px-4 py-2 border border-[#2E3192] rounded-full text-[#2E3192] font-medium hover:bg-[#2E3192] hover:text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            class="bi bi-person-circle" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                            <path fill-rule="evenodd"
                                d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                        </svg>
                        <span>@lang('message.Sign in')</span>
                    </a>
                @endauth

                <div class="flex items-center space-x-8 rtl:space-x-reverse">
                    @auth
                        <a href="{{ route('wishlist.index') }}"
                            class="group relative flex items-center justify-center p-2">
                            <svg class="w-5 h-5 text-[#383cb3] group-hover:text-[#b1b4ff] transition-colors duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>

                            <span id="wishlist-count"
                                class="absolute top-1 right-1 bg-[#2E3192] text-[8px] font-bold text-white h-4 w-4 rounded-full flex items-center justify-center scale-90 group-hover:scale-110 transition-transform duration-300">
                                {{ auth()->user()->wishlists->count() }}
                            </span>
                        </a>
                    @endauth
                    <a href="{{ route('cart.index') }}" class="group relative flex items-center justify-center p-2">
                        <svg class="w-5 h-5 text-[#383cb3] group-hover:text-[#b1b4ff] transition-colors duration-300"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>

                        <span id="cart-count"
                            class="absolute top-1 right-1 bg-[#2E3192] text-[8px] font-bold text-white h-4 w-4 rounded-full flex items-center justify-center scale-90 group-hover:scale-110 transition-transform duration-300">
                            {{ app(\App\Services\CartService::class)->getCount() }}
                        </span>
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="bg-[#1b1e6c] text-white mt-24">
       <div class="max-w-7xl mx-auto px-6 py-20">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-16 lg:gap-12">

                <div class="lg:col-span-2 space-y-8">
                    <div>
                        <h3 class="text-2xl  tracking-[0.3em] text-white mb-6">
                            Al Bashawri البشاوري
                        </h3>
                        <p class="text-gray-400 text-[14px] leading-relaxed uppercase tracking-widest max-w-xs">
                            @lang('message.Your destination')
                        </p>
                    </div>

                    <div class="flex space-x-6 rtl:space-x-reverse">
                        <a href="https://www.instagram.com/albashawriksa/"
                            class="text-gray-500 hover:text-white transition-colors duration-300">
                            <i class="fab fa-instagram text-lg"></i>
                        </a>
                        <a href="https://www.snapchat.com/add/theaddress2021"
                            class="text-gray-500 hover:text-white transition-colors duration-300">
                            <i class="fab fa-snapchat text-lg"></i>
                        </a>
                        <a href="https://www.tiktok.com/@albashawrisa"
                            class="text-gray-500 hover:text-white transition-colors duration-300">
                            <i class="fab fa-tiktok text-lg"></i>
                        </a>
                        <a href="https://wa.me/966534473335"
                            class="text-gray-500 hover:text-white transition-colors duration-300">
                            <i class="fab fa-whatsapp text-lg"></i>
                        </a>
                    </div>


                </div>

                <div>
                    <h4 class="text-[14px] font-bold uppercase tracking-[0.3em] text-white mb-8">@lang('message.Shop')
                    </h4>
                    <ul class="space-y-4 text-[14px] font-medium uppercase tracking-widest text-gray-400">
                        <li><a href="{{ route('products.sunglasses') }}"
                                class="hover:text-white transition-colors">@lang('message.Sunglasses')</a></li>
                        <li><a href="{{ route('products.eyeglasses') }}"
                                class="hover:text-white transition-colors">@lang('message.Glasses')</a></li>
                        <li><a href="{{ route('products.clipon') }}"
                                class="hover:text-white transition-colors">@lang('message.Clip Ons')</a></li>
                        <li><a href="{{ route('products.contact-lenses') }}"
                                class="hover:text-white transition-colors">@lang('message.Contact Lenses')</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-[14px] font-bold uppercase tracking-[0.3em] text-white mb-8">@lang('message.Service')
                    </h4>
                    <ul class="space-y-4 text-[14px] font-medium uppercase tracking-widest text-gray-400">
                        <li><a href="{{ route('about') }}"
                                class="hover:text-white transition-colors ">@lang('message.About Us')</a></li>
                        <li><a href="{{ route('policy') }}"
                                class="hover:text-white transition-colors">@lang('message.Policy of Refund and Exchange')</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">@lang('message.Privacy Policy')</a></li>
                        <li><a href="{{ route('stores.index') }}"
                                class="hover:text-white transition-colors underline decoration-gray-700 underline-offset-4">
                                @lang('message.Our Stores') </a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-[14px] font-bold uppercase tracking-[0.3em] text-white mb-8">@lang('message.Contact')
                    </h4>
                    <ul class="space-y-4 text-[14px] font-medium tracking-widest text-gray-400">
                        <li class="flex items-center gap-2">
                            <a href="mailto:Albashawriksa@gmail.com"
                                class="block text-xs hover:text-gray-400 transition-colors tracking-wide">
                                <i class="fas fa-envelope text-gray-400"></i>
                                {{ settings('admin_email') }}
                            </a>
                        </li>
                        <li class="flex items-center gap-2">
                            <a href="https://wa.me/966534473335"
                                class="block text-xs hover:text-gray-400 transition-colors tracking-wide">
                                <i class="fab fa-whatsapp text-green-500"></i>
                                {{ settings('phone') }}
                            </a>
                        </li>
                    </ul>

                </div>
            </div>
        </div>

        <div class="border-t border-gray-900 bg-[#0e0f2d]">
            <div class="max-w-7xl mx-auto px-6 py-10 flex flex-col md:flex-row items-center justify-between">

                <div class="flex items-center space-x-6 order-1 md:order-2 mb-8 md:mb-0 ">
                    <i class="fab fa-cc-visa text-2xl"></i>
                    <i class="fab fa-cc-mastercard text-2xl"></i>
                    <i class="fab fa-cc-paypal text-2xl"></i>
                    <i class="fas fa-money-check-alt text-xl"></i>
                </div>

                <div
                    class="text-[9px] font-bold uppercase tracking-[0.3em]  order-2 md:order-1 text-center md:text-left">
                    &copy; {{ date('Y') }} Al Bashawri.


                </div>
            </div>
        </div>
    </footer>

    @yield('scripts')
    <script>
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('language-menu');
            const button = document.getElementById('menu-button');
            if (button && menu && !button.contains(event.target) && !menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
    <script>
        // CSRF Token for AJAX
        window.axios = require('axios');
        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
    </script>

    <script>
        function toggleLangMenu() {
            const menu = document.getElementById('lang-switch');
            const arrow = document.getElementById('lang-arrow');
            menu.classList.toggle('hidden');
            arrow.classList.toggle('rotate-180');
        }

        // Fermer le menu si on clique ailleurs sur la page
        window.addEventListener('click', function(e) {
            const container = document.getElementById('lang-dropdown-container');
            const menu = document.getElementById('lang-switch');
            const arrow = document.getElementById('lang-arrow');

            if (container && !container.contains(e.target)) {
                menu.classList.add('hidden');
                arrow.classList.remove('rotate-180');
            }
        });
    </script>

    <script>
        async function toggleWishlist(itemId, itemType, btn) {
            if (!btn) return;

            try {
                const response = await fetch("{{ route('wishlist.toggle') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        item_id: itemId,
                        item_type: itemType
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showMessage('success', data.message);

                    btn.innerHTML = data.action === 'added' ?
                        '<i class="fas fa-heart text-red-500"></i>' :
                        '<i class="far fa-heart"></i>';
                } else {
                    showMessage('error', data.message || 'Erreur wishlist');
                }

            } catch (error) {
                console.error('Wishlist Error:', error);
                showMessage('error', 'Erreur wishlist');
            }
        }

        // Fonction pour afficher message
        function showMessage(type, text) {
            window.dispatchEvent(new CustomEvent('message', {
                detail: {
                    type,
                    text
                }
            }));
        }
    </script>

    <script>
        function showMessage(type, message) {
            const toast = document.createElement('div');
            toast.textContent = message;
            toast.className =
                'fixed bottom-5 right-5 px-4 py-2 rounded shadow-md text-white text-sm z-50';

            toast.style.backgroundColor =
                type === 'error' ? '#9f0d73' : '#2E3192';

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 2000);
        }
    </script>

    <script>
        window.addToCart = async function(itemId, itemType, quantity, lensType = null, extraPrice = 0, rxFile = null) {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const url = "{{ url('/cart/add') }}";

            const fd = new FormData();
            fd.append('item_id', Number(itemId));
            fd.append('item_type', itemType);
            fd.append('quantity', Number(quantity));
            fd.append('extra_price', Number(extraPrice || 0));

            if (lensType) fd.append('lens_type', lensType);

            if (rxFile instanceof File) fd.append('rx_file', rxFile);

            try {
                const res = await fetch(url, {
                    method: "POST",
                    headers: {
                        "Accept": "application/json",
                        ...(token ? {
                            "X-CSRF-TOKEN": token
                        } : {})
                    },
                    body: fd,
                });

                const data = await res.json().catch(() => ({}));

                if (!res.ok || !data.success) {
                    console.error("ADD TO CART ERROR =>", data);
                    if (typeof showMessage === "function") showMessage("error", data.message ||
                        "Add to cart failed");
                    return false;
                }

                if (typeof showMessage === "function") showMessage("success", data.message || "Added to cart");
                return true;

            } catch (e) {
                console.error("ADD TO CART EXCEPTION =>", e);
                if (typeof showMessage === "function") showMessage("error", "Network error");
                return false;
            }
        }
    </script>

    @vite(['resources/js/virtual-tryon.js'])

    @include('admin.pixels.events')
</body>

</html>