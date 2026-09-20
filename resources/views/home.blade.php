@extends('layouts.app')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@600&family=Playfair+Display:wght@400&display=swap');

        .accordion-card h3,
        .accordion-card p {
            text-shadow: 0 2px 13px rgba(0, 0, 0, 0.3);
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .swiper-button-next,
        .swiper-button-prev {
            color: white;
            width: 45px;
            height: 45px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .swiper-pagination-bullet {
            background: rgb(0, 0, 0) !important;
            opacity: 0.6;
        }

        .swiper-pagination-bullet-active {
            opacity: 1;
        }

        .category-pill-btn {
            @apply bg-white hover:bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3 transition-all duration-300 shadow-sm hover:shadow-md cursor-pointer;
        }

        .category-pill-btn.active {
            @apply bg-gradient-to-r from-blue-600 to-purple-600 text-white border-transparent shadow-lg transform scale-105;
        }

        .category-pill-btn:hover:not(.active) {
            @apply border-blue-300 transform scale-105;
        }

        /* Product Card Animations */
        @keyframes product-fade-in {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .product-card-animated {
            animation: product-fade-in 0.6s ease-out forwards;
            opacity: 0;
        }

        /* Notification Animations */
        @keyframes slide-in {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .animate-slide-in {
            animation: slide-in 0.3s ease-out;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .nav-categories {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 20px;
        }

        .nav-links-container {
            display: flex;
            flex-wrap: wrap;
            gap: 13px;
            align-items: center;
        }

        .nav-title {
            font-weight: 600;
            color: #6b7280;
        }

        .category-link {
            padding: 6px 14px;
            border-radius: 9999px;
            background-color: #f3f4f6;
            color: #374151;
            font-size: 14px;
            transition: all .2s ease-in-out;
        }

        .category-link:hover {
            background-color: #2563eb;
            color: white;
        }

        .category-link.active {
            background-color: #2563eb;
            color: white;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.5s ease-out forwards;
        }

        .store-visit-section {
            display: flex;
            justify-content: center;
            align-items: center;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            font-family: Arial, sans-serif;
        }

        .store-visit-content {
            flex: 1;
            max-width: 450px;
            padding-right: 40px;
        }

        .greeting {
            color: #A81A7D;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .title {
            font-size: 3rem;
            font-weight: 500;
            margin-top: 0;
            margin-bottom: 20px;
            line-height: 1.1;
            font-family: Georgia, serif;
        }

        .description {
            font-size: 1.1rem;
            line-height: 1.5;
            color: #333;
            margin-bottom: 30px;
        }

        .actions {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 14px 25px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: #005ce6;
            color: white;
        }

        .btn-primary:hover {
            background-color: #004ac9;
        }

        .btn-secondary {
            background-color: #2a3a55;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #1c2738;
        }

        .store-visit-image-container {
            flex-shrink: 0;
            width: 450px;
            position: relative;
            border-radius: 13px;
            overflow: hidden;
            box-shadow: 0 13px 20px rgba(0, 0, 0, 0.1);
        }

        .store-image {
            width: 100%;
            height: auto;
            display: block;
        }

        @media (max-width: 800px) {
            .store-visit-section {
                flex-direction: column;
                padding: 13px;
            }

            .store-visit-content {
                padding-right: 0;
                margin-bottom: 30px;
                text-align: center;
                max-width: 100%;
            }

            .actions {
                justify-content: center;
            }

            .store-visit-image-container {
                width: 100%;
                max-width: 450px;
            }
        }

        .accordion-container {
            display: flex;
            width: 80%;
            height: 600px;
            /* Ajustez la hauteur selon vos besoins */
            gap: 15px;
            padding: 20px;
        }

        .card {
            position: relative;
            flex: 1;
            border-radius: 20px;
            overflow: hidden;
            cursor: pointer;
        }

        .card-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* La vidéo est "gelée" tant qu'on hover pas */
            opacity: 0.8;
            transition: opacity 0.3s;
        }

        .card:hover .card-video {
            opacity: 1;
        }

        /* L'effet d'élargissement au survol */
        .card:hover {
            flex: 4;
        }

        /* Style pour le texte à l'intérieur */
        .content {
            position: absolute;
            bottom: 20px;
            left: 20px;
            color: white;
            white-space: nowrap;
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        .text1 {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .card:hover .content .text1 {
            opacity: 1;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Optionnel : rend le saut vers l'ancre plus fluide */
        html {
            scroll-behavior: smooth;
        }
        
        /* حاوية الشريط القابلة للتمرير */
.category-scroll-wrapper {
    display: flex;
    overflow-x: auto; /* يسمح بالتمرير الأفقي */
    gap: 20px; /* المسافة بين كل عنصر */
    padding: 10px 5px;
    scrollbar-width: none; /* إخفاء شريط التمرير لمتصفحات Firefox */
    -ms-overflow-style: none;  /* إخفاء شريط التمرير لمتصفحات IE */
}

/* إخفاء شريط التمرير لمتصفحات Chrome و Safari */
.category-scroll-wrapper::-webkit-scrollbar {
    display: none;
}

/* عنصر التصنيف الواحد */
.category-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    min-width: 80px; /* عرض أدنى للعنصر لضمان عدم انكماشه */
    cursor: pointer;
    transition: transform 0.2s;
}

.category-item:hover {
    transform: translateY(-5px); /* حركة بسيطة عند التمرير */
}

/* دائرة الصورة */
.image-circle {
 /* width: 80px;  حجم الدائرة */
    /* height: 80px; */
    /* border-radius: 50%; جعلها دائرية */
    overflow: hidden;
    /* border: 2px solid #fff; /* حدود بيضاء */
    /* box-shadow: 0 4px 10px rgba(0,0,0,0.1); ظل خفيف */ */
    margin-bottom: 8px;
    /* background-color: #f8f9fa; */
    display: flex;
    justify-content: center;
    align-items: center;
    transition: box-shadow 0.3s ease;
}

/* تأثير عند الوقوف على الصورة (مثل إنستغرام) */
.category-item:hover .image-circle {
    box-shadow: 0 4px 15px rgba(46, 49, 146, 0.3); /* ظل ملون بلون البراند */
    border-color: #2E3192; /* لون حدود عند الهوفر */
}

/* الصورة داخل الدائرة */
.image-circle img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* لملء الدائرة دون تشويه الصورة */
}

/* النص أسفل الصورة */
.category-name {
    color: #333;
    font-size: 14px;
    font-weight: 600;
    text-align: center;
    white-space: nowrap; /* منع النص من النزول لسطر ثاني */
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis; /* وضع ... إذا كان الاسم طويلاً */
}

/* تحسينات للجوال */
@media (max-width: 768px) {
    /* .image-circle {
        width: 65px;
        height: 65px;
    } */
    
    .category-name {
        font-size: 12px;
    }
    
    .category-scroll-wrapper {
        gap: 15px;
    }
}
/*جديد*/
/* =========================================
   CATEGORY SLIDER
========================================= */

.category-scroll-wrapper {
    width: 100%;
    display: flex;
    overflow-x: auto;
    gap: 20px;

    padding: 10px 0;

    scroll-behavior: smooth;

    scrollbar-width: none;
    -ms-overflow-style: none;

    /* توسيط العناصر */
    justify-content: center;
}

.category-scroll-wrapper::-webkit-scrollbar {
    display: none;
}


/* =========================================
   CATEGORY ITEM
========================================= */

.category-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;

    text-align: center;
    text-decoration: none;

    cursor: pointer;

    /* 6 عناصر في الديسكتوب */
    flex: 0 0 calc((100% - 100px) / 6);
    min-width: calc((100% - 100px) / 6);

    transition: transform 0.25s ease;
}


/* =========================================
   CATEGORY IMAGE
========================================= */

.image-circle {
    width: 100%;

    display: flex;
    align-items: center;
    justify-content: center;

    overflow: hidden;

    margin-bottom: 8px;

    transition:
        transform 0.25s ease,
        box-shadow 0.25s ease;
}

.image-circle img {
    display: block;

    width: 100%;
    height: auto;

    max-width: 100%;

    object-fit: contain;
}


/* =========================================
   CATEGORY NAME
========================================= */

.category-name {
    display: block;

    width: 100%;

    color: #333;

    font-size: 14px;
    font-weight: 600;

    line-height: 1.4;

    text-align: center;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}


/* =========================================
   HOVER
========================================= */

.category-item:hover {
    transform: translateY(-4px);
}

.category-item:hover .image-circle {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
}


/* =========================================
   TABLET
   3 ELEMENTS VISIBLE
========================================= */

@media (max-width: 991px) {

    .category-scroll-wrapper {
        gap: 20px;

        justify-content: center;
    }

    .category-item {
        flex: 0 0 calc((100% - 40px) / 3);
        min-width: calc((100% - 40px) / 3);
    }

    .category-name {
        font-size: 14px;
    }
}


/* =========================================
   MOBILE
   2 ELEMENTS VISIBLE
========================================= */

@media (max-width: 767px) {

    .category-scroll-wrapper {
        gap: 15px;

        padding: 10px 0;

        justify-content: flex-start;
    }

    .category-item {
        flex: 0 0 calc((100% - 15px) / 3);
        min-width: calc((100% - 15px) / 3);
    }

    .category-name {
        font-size: 13px;
    }
}


/* =========================================
   SMALL MOBILE
========================================= */

@media (max-width: 400px) {

    .category-scroll-wrapper {
        gap: 12px;
    }

    .category-item {
        flex: 0 0 calc((100% - 12px) / 2);
        min-width: calc((100% - 12px) / 2);
    }

    .category-name {
        font-size: 12px;
    }
}

section.category-slider-section.py-4 .container {
    margin: auto;
}
/* =========================================
   HOVER - WHOLE CATEGORY CARD
========================================= */

.category-item {
    padding: 10px;
    border-radius: 12px;
    transition: all 0.25s ease;
}

.category-item:hover {
    background: #fff;
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.10);
}

.category-item:hover .image-circle {
    transform: none;
    box-shadow: none;
}

.category-name {
    transition: color 0.25s ease;
}

.category-item:hover .category-name {
    color: #000;
}
.category-name {

    font-weight: 700;
}
    </style>
    

    

    <!-- Hero Slider Top  last-->
     
    <!-- <div class="relative w-full overflow-hidden" >
        <div class="swiper mySwiperHero h-[160px] xs:h-[180px] sm:h-[220px] md:h-[300px] lg:h-[420px]">
            <div class="swiper-wrapper">
                @php
                    $images =
                        app()->getLocale() == 'en'
                            ? ['/images/banner1.jpg', '/images/banner2.jpg', '/images/banner3.jpg']
                            : [
                                '/images/banner1.jpg',
                                '/images/banner2.jpg',
                                '/images/banner3.jpg',
                            ];
                @endphp

                @foreach ($images as $img)
                    <div class="swiper-slide">
                        <img src="{{ $img }}" class="w-full h-full object-cover object-center" loading="lazy" />
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination !bottom-2"></div>
            <div class="swiper-button-next hidden md:flex text-[#212482]"></div>
            <div class="swiper-button-prev hidden md:flex text-[#212482]"></div>
        </div>
    </div> -->

        <!-- Hero Slider Top  new-->
         
<!-- ==========================================
     HOME HERO SLIDER
     ========================================== -->

<section class="home-hero">

    <div class="swiper mySwiperHero">

        <div class="swiper-wrapper">

            <!-- ==================================
                 SLIDE 1
                 ================================== -->
            <div class="swiper-slide">

                <a
                    href="#latest-products"
                    class="hero-slide-link"
                    aria-label="Latest Products"
                >

                    <picture class="hero-picture">

                        <!-- Mobile -->
                        <source
                            media="(max-width: 767px)"
                            srcset="{{ app()->getLocale() === 'en'
                                ? asset('images/mob11.webp')
                                : asset('images/1mob.webp') }}"
                        >

                        <!-- Desktop / Tablet -->
                        <img
                            src="{{ app()->getLocale() === 'en'
                                ? asset('images/11.webp')
                                : asset('images/1.webp') }}"
                            alt="Latest Products"
                            class="hero-image"
                            width="1920"
                            height="700"
                            fetchpriority="high"
                            loading="eager"
                        >

                    </picture>

                </a>

            </div>


            <!-- ==================================
                 SLIDE 2
                 ================================== -->
            <div class="swiper-slide">

                <a
                    href="{{ route('products.sunglasses') }}"
                    class="hero-slide-link"
                    aria-label="Sunglasses"
                >

                    <picture class="hero-picture">

                        <!-- Mobile -->
                        <source
                            media="(max-width: 767px)"
                            srcset="{{ app()->getLocale() === 'en'
                                ? asset('images/mob22.webp')
                                : asset('images/2mob.webp') }}"
                        >

                        <!-- Desktop / Tablet -->
                        <img
                            src="{{ app()->getLocale() === 'en'
                                ? asset('images/22.webp')
                                : asset('images/2.webp') }}"
                            alt="Sunglasses"
                            class="hero-image"
                            loading="lazy"
                        >

                    </picture>

                </a>

            </div>


            <!-- ==================================
                 SLIDE 3
                 ================================== -->
            <div class="swiper-slide">

                <a
                    href="{{ route('products.eyeglasses') }}"
                    class="hero-slide-link"
                    aria-label="Prescription Eyeglasses"
                >

                    <picture class="hero-picture">

                        <!-- Mobile -->
                        <source
                            media="(max-width: 767px)"
                            srcset="{{ app()->getLocale() === 'en'
                                ? asset('images/mob33.webp')
                                : asset('images/3mob.webp') }}"
                        >

                        <!-- Desktop / Tablet -->
                        <img
                            src="{{ app()->getLocale() === 'en'
                                ? asset('images/33.webp')
                                : asset('images/3.webp') }}"
                            alt="Prescription Eyeglasses"
                            class="hero-image"
                            loading="lazy"
                        >

                    </picture>

                </a>

            </div>

        </div>


        <!-- ==================================
             PAGINATION
             ================================== -->
        <div class="swiper-pagination"></div>


        <!-- ==================================
             NAVIGATION
             ================================== -->
        <div class="swiper-button-next hidden md:flex"></div>
        <div class="swiper-button-prev hidden md:flex"></div>

    </div>

</section>

<style>

/* ==========================================
   HERO CONTAINER
   ========================================== */

.home-hero {
    width: 100%;
    max-width: 100%;
    margin: 0;
    padding: 0;
    overflow: hidden;
}


/* ==========================================
   SWIPER
   ========================================== */

.home-hero .mySwiperHero {
    width: 100%;
    max-width: 100%;
    margin: 0;
    padding: 0;
}


/* ==========================================
   SWIPER WRAPPER
   ========================================== */

.home-hero .swiper-wrapper {
    width: 100%;
    height: auto;
}


/* ==========================================
   SLIDE
   ========================================== */

.home-hero .swiper-slide {
    width: 100%;
    height: auto !important;
    min-height: 0 !important;
    display: block;
    flex-shrink: 0;
}


/* ==========================================
   CLICKABLE AREA
   ========================================== */

.home-hero .hero-slide-link {
    display: block;
    width: 100%;
    height: auto;
    margin: 0;
    padding: 0;
    line-height: 0;
}


/* ==========================================
   PICTURE
   ========================================== */

.home-hero .hero-picture {
    display: block;
    width: 100%;
    height: auto;
    margin: 0;
    padding: 0;
}


/* ==========================================
   HERO IMAGE
   ========================================== */

.home-hero .hero-image {
    display: block;
    width: 100%;
    max-width: 100%;
    height: auto !important;
    min-height: 0 !important;

    margin: 0;
    padding: 0;

    /*
     * IMPORTANT:
     * No object-fit: cover
     * No fixed height
     * No cropping
     */
    object-fit: initial;
}


/* ==========================================
   PAGINATION
   ========================================== */

.home-hero .swiper-pagination {
    bottom: 12px !important;
}

.home-hero .swiper-pagination-bullet {
    background: #ffffff !important;
    opacity: 0.65;
}

.home-hero .swiper-pagination-bullet-active {
    opacity: 1;
}


/* ==========================================
   ARROWS
   ========================================== */

.home-hero .swiper-button-next,
.home-hero .swiper-button-prev {
    color: #ffffff !important;
}


/* ==========================================
   MOBILE
   ========================================== */

@media (max-width: 767px) {

    .home-hero .swiper-pagination {
        bottom: 8px !important;
    }

    .home-hero .swiper-pagination-bullet {
        width: 7px;
        height: 7px;
    }

}


/* ==========================================
   EXTRA SMALL MOBILE
   ========================================== */

@media (max-width: 480px) {

    .home-hero .swiper-pagination-bullet {
        width: 6px;
        height: 6px;
    }

}

</style>

   <!-- catogey  -->

        <section class="category-slider-section py-4">
    <div class="container">
        {{-- عنوان القسم (اختياري) --}}
        {{-- <h5 class="fw-bold mb-3">تسوق حسب الفئة</h5> --}}

        <div class="category-scroll-wrapper">
            @foreach($categories as $category)
                <a href="{{ route('products.category', $category->slug ?? '#') }}" class="category-item">
                    
                    {{-- دائرة الصورة --}}
                    <div class="image-circle">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
                        @else
                            {{-- صورة افتراضية في حال عدم وجود صورة --}}
                            <img src="https://via.placeholder.com/150?text={{ substr($category->name, 0, 1) }}" alt="{{ $category->name }}">
                        @endif
                    </div>

                    {{-- اسم التصنيف --}}
<span class="category-name">
    @switch($category->id)
        @case(2)
            @lang('home.Category Kids')
            @break

        @case(3)
            @lang('home.Category Clip-On')
            @break

        @case(4)
            @lang('home.Category Unisex')
            @break

        @case(5)
            @lang('home.Category Men')
            @break

        @case(6)
            @lang('home.Category Contact Lenses')
            @break

        @case(7)
            @lang('home.Category Women')
            @break

        @default
            {{ $category->name }}
    @endswitch
</span>                </a>
            @endforeach
        </div>
    </div>
</section>






    <!-- <section class="py-12 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 md:mb-16 flex flex-col items-center text-center">
                <h2 class="text-2xl md:text-5xl text-gray-900 uppercase tracking-tight md:tracking-tighter">
                    @lang('home.Our Services')
                </h2>
                <div class="mt-3 md:mt-6 w-12 md:w-20 h-[1px] md:h-[2px] bg-black"></div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 md:gap-4">

                @php
                    $services = [
                        [
                            'route' => 'products.eyeglasses',
                            'label' => 'home.Prescription eyeglasses',
                            'img' => 'image1.jpg',
                        ],
                        [
                            'route' => 'products.sunglasses',
                            'label' => 'home.Prescription sunglasses',
                            'img' => 'image2.jpg',
                        ],
                        ['route' => 'products.contact-lenses', 'label' => 'home.Contact Lenses', 'img' => 'image3.jpg'],
                        ['route' => '#', 'label' => 'home.Eye exams', 'img' => 'image4.jpg'],
                    ];
                @endphp

                @foreach ($services as $service)
                    <a href="{{ $service['route'] != '#' ? route($service['route']) : '#' }}"
                        class="group relative h-[180px] md:h-[300px] overflow-hidden bg-gray-100 block">

                        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-[2s] scale-100 group-hover:scale-110"
                            style="background-image: url('{{ asset('images/' . $service['img']) }}');">
                        </div>

                        <div class="absolute inset-0 bg-black/10 group-hover:bg-black/40 transition-colors duration-500">
                        </div>
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent opacity-80 md:opacity-60">
                        </div>

                        <div
                            class="absolute inset-0 flex flex-col justify-end p-4 md:p-10 transition-transform duration-500">
                            <h3
                                class="text-white font-bold uppercase tracking-[0.1em] md:tracking-[0.4em] mb-2 md:mb-6 leading-tight">
                                @lang($service['label'])
                            </h3>
                            <div class="flex items-center gap-2 text-white">
                                <span
                                    class="w-6 md:w-10 h-[1px] bg-white transition-all duration-500 group-hover:w-full"></span>
                                <span
                                    class="hidden md:block whitespace-nowrap text-[13px] uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity">
                                    @lang('home.Discover')
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach

            </div>
        </div>
    </section> -->
 <!-- عنصر لماذا بشاوري  -->
{{-- WHY US SECTION --}}
<section class="why-us-section">
    <div class="why-us-container">

        {{-- CONTENT --}}
        <div class="why-us-content" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

            <span class="why-us-label">
                @lang('home.Store Benefits')
            </span>

            <h2>
                @lang('home.Experience You Can Trust')
            </h2>

            <div class="why-us-items">

                {{-- 1 --}}
                <div class="why-us-item">
                    <div class="why-us-number">1</div>

                    <div class="why-us-text">
                        <h3>@lang('home.Extensive Optical Experience')</h3>
                        <p>
                            @lang('home.Specialized knowledge in eyewear and optics, built through years of experience')
                        </p>
                    </div>
                </div>

                {{-- 2 --}}
                <div class="why-us-item">
                    <div class="why-us-number">2</div>

                    <div class="why-us-text">
                        <h3>@lang('home.Wide Range of Brands')</h3>
                        <p>
                            @lang('home.A wide selection of eyewear and designs to suit different styles and needs')
                        </p>
                    </div>
                </div>

                {{-- 3 --}}
                <div class="why-us-item">
                    <div class="why-us-number">3</div>

                    <div class="why-us-text">
                        <h3>@lang('home.Original Products')</h3>
                        <p>
                            @lang('home.We are committed to offering authentic products from the brands we carry')
                        </p>
                    </div>
                </div>

                {{-- 4 --}}
                <div class="why-us-item">
                    <div class="why-us-number">4</div>

                    <div class="why-us-text">
                        <h3>@lang('home.Shop with Ease')</h3>
                        <p>
                            @lang('home.Browse our collection and order your products easily from anywhere in Saudi Arabia')
                        </p>
                    </div>
                </div>

            </div>

        </div>

        {{-- IMAGE --}}
        <div class="why-us-image">
            <img
                src="{{ asset('images/whyus.webp') }}"
                alt="@lang('home.Store Benefits')"
            >
        </div>

    </div>
</section>


<style>
/* =========================================
   WHY US SECTION
========================================= */

.why-us-section {
    width: 100%;
    background: #fff;
    padding: 90px 20px;
}

.why-us-container {
    width: 100%;
    max-width: 1280px;
    margin: 0 auto;

    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    align-items: center;

    gap: 80px;
}


/* =========================================
   IMAGE
========================================= */

.why-us-image {
    width: 100%;
    overflow: hidden;
    border-radius: 20px;
}

.why-us-image img {
    display: block;
    width: 100%;
    height: auto;
    max-width: 100%;

    /* الصورة الأصلية تحتوي على التصميم كامل */
    object-fit: contain;
}


/* =========================================
   CONTENT
========================================= */

.why-us-content {
    width: 100%;
}

.why-us-label {
    display: block;

    color: #4F46E5;
    font-size: 14px;
    font-weight: 500;

    margin-bottom: 18px;
}

.why-us-content h2 {
    margin: 0 0 42px;

    color: #000000;

    font-size: clamp(38px, 4vw, 56px);
    line-height: 1.2;
    font-weight: 800;
}


/* =========================================
   ITEMS
========================================= */

.why-us-items {
    display: flex;
    flex-direction: column;
    gap: 27px;
}

.why-us-item {
    display: flex;
    flex-direction: row;
    align-items: flex-start;

    gap: 18px;

    transition: transform 0.3s ease;
}

.why-us-number {
    width: 48px;
    height: 48px;
    min-width: 48px;

    border: 1px solid #4F46E5;
    border-radius: 50%;

    display: flex;
    align-items: center;
    justify-content: center;

    color: #000;
    background: #fff;

    font-size: 22px;
    font-weight: 600;

    transition: all 0.3s ease;
}

.why-us-text {
    flex: 1;
    min-width: 0;
}

.why-us-text h3 {
    margin: 0 0 7px;

    color: #000;

    font-size: 17px;
    font-weight: 700;
    line-height: 1.5;
}

.why-us-text p {
    margin: 0;

    color: #222;

    font-size: 15px;
    line-height: 1.8;
}


/* =========================================
   HOVER
========================================= */

.why-us-item:hover {
    transform: translateX(-5px);
}

.why-us-item:hover .why-us-number {
    background: #4F46E5;
    color: #fff;
}


/* =========================================
   TABLET
========================================= */

@media (max-width: 991px) {

    .why-us-section {
        padding: 70px 20px;
    }

    .why-us-container {
        grid-template-columns: 1fr;
        gap: 55px;
        max-width: 760px;
    }

    .why-us-image {
        order: 1;
    }

    .why-us-content {
        order: 2;
    }

    .why-us-content h2 {
        font-size: 42px;
        margin-bottom: 35px;
    }
}


/* =========================================
   MOBILE
========================================= */

@media (max-width: 767px) {

    .why-us-section {
        padding: 55px 16px;
    }

    .why-us-container {
        width: 100%;
        gap: 40px;
    }

    .why-us-image {
        border-radius: 14px;
    }

    .why-us-content {
        width: 100%;
    }

    .why-us-label {
        font-size: 13px;
        margin-bottom: 12px;
    }

    .why-us-content h2 {
        font-size: 32px;
        line-height: 1.25;
        margin-bottom: 30px;
    }

    .why-us-items {
        gap: 23px;
    }

    .why-us-item {
        gap: 12px;
    }

    .why-us-number {
        width: 42px;
        height: 42px;
        min-width: 42px;

        font-size: 18px;
    }

    .why-us-text h3 {
        font-size: 15px;
        line-height: 1.5;
        margin-bottom: 5px;
    }

    .why-us-text p {
        font-size: 13px;
        line-height: 1.7;
    }

    /* منع خروج النص خارج الشاشة */
    .why-us-text {
        overflow-wrap: break-word;
    }
}


/* =========================================
   SMALL MOBILE
========================================= */

@media (max-width: 400px) {

    .why-us-section {
        padding: 45px 14px;
    }

    .why-us-content h2 {
        font-size: 28px;
    }

    .why-us-number {
        width: 38px;
        height: 38px;
        min-width: 38px;
        font-size: 17px;
    }

    .why-us-text h3 {
        font-size: 14px;
    }

    .why-us-text p {
        font-size: 12px;
    }
}
</style>



    <section id="brands-filter" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px- mb-20 mt-24 ">
        <div class="flex flex-col border-b border-gray-100 pb-8 mb-10">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
                <div class="flex-shrink-0">
                    <h2 class="text-3xl text-gray-900 uppercase">
                        @lang('home.Shop by Brand')
                    </h2>
                    <p class="mt-2 text-[13px] font-bold text-gray-400 uppercase tracking-[0.2em]">
                        @lang('home.Select your preferred designer')
                    </p>
                </div>

                <nav id="brand-nav" class="flex gap-4 overflow-x-auto pb-2 no-scrollbar scroll-smooth">
                    @foreach ($brands as $brand)
                        <a href="{{ route('home', ['brand' => $brand->id, 'category' => request('category'), 'search' => request('search')]) }}#brands-filter"
                            class="flex items-center gap-3 px-4 py-2 rounded-full border transition-all whitespace-nowrap flex-shrink-0
                        {{ $selectedBrandId == $brand->id
                            ? 'border-black bg-gray-50'
                            : 'border-gray-200 hover:border-gray-400 bg-white' }}">
                            <div
                                class="w-8 h-8 rounded-full overflow-hidden border border-gray-100 bg-gray-50 flex-shrink-0">
                                @if ($brand->logo)
                                    <img src="{{ asset($brand->logo) }}" class="w-full h-full object-cover"
                                        alt="{{ $brand->name }}">
                                @else
                                    <div
                                        class="w-full h-full flex items-center justify-center bg-gray-100 text-[13px] text-gray-400">
                                        {{ substr($brand->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <span
                                class="text-[14px] font-bold uppercase tracking-widest {{ $selectedBrandId == $brand->id ? 'text-black' : 'text-gray-500 hover:text-black' }} pr-2">
                                {{ $brand->name }}
                            </span>
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>
        {{-- PRODUITS --}}
        <div class="mt-4">
            @if ($filteredProducts->count())
                {{-- LISTE DÉROULANTE HORIZONTALE --}}
                <div class="flex gap-6 overflow-x-auto pb-10 no-scrollbar snap-x snap-mandatory scroll-smooth">
                    @foreach ($filteredProducts as $product)
                        {{-- Conteneur individuel avec largeur fixe --}}
                        <div class="flex-shrink-0 w-[240px] md:w-[280px] snap-start">
                            @include('components.product-card-enhanced', ['product' => $product])
                        </div>
                    @endforeach
                </div>
            @else
                {{-- MESSAGE SI VIDE --}}
                <div class="text-center py-20 bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                    <p class="text-[14px] font-bold uppercase tracking-widest text-gray-400">
                        @lang('home.Aucun produit trouvé')
                    </p>
                </div>
            @endif
        </div>
    </section>

    <section class="bg-white py-10 sm:py-16">
        <div class="max-w-7xl mx-auto px-6 sm:px-10 lg:px-16 grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">

            <!-- LEFT SIDE (PLUS GRAND) -->
            <div class="lg:col-span-7 space-y-8">
                <div class="space-y-4 max-w-md text-center lg:text-left rtl:text-right">
                    <h3 class="text-xl sm:text-2xl text-gray-900">
                        @lang("home.Don't know how to wear contact lenses?")
                    </h3>
                    <p class="text-gray-600">
                        @lang('home.Learn the Basics of Contact Lens Wear')
                    </p>
                    <a href="{{ route('products.contact-lenses') }}"
                        class="inline-flex items-center gap-2 bg-[#f4f5ff] text-[#2E3192] px-6 py-2 rounded-full font-semibold hover:bg-[#cfd3ff] transition rtl:flex-row-reverse">
                        @lang('home.Shop now') →
                    </a>
                </div>
                <img src="{{ asset('images/Contact.webp') }}" alt="Contact lenses"
                    class="hidden lg:block w-full h-[420px] rounded-2xl object-cover">
            </div>
            <!-- RIGHT SIDE (VIDEO TOUJOURS VISIBLE) -->
            <div class="lg:col-span-5 w-full h-64 sm:h-80 lg:h-[500px] overflow-hidden rounded-2xl">
                <video src="{{ asset('images/lensesnew.mp4') }}" autoplay muted loop playsinline
                    class="w-full h-full object-cover">
                </video>
            </div>

        </div>
    </section>


    

  <!-- <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
            <span class="text-xs font-bold uppercase tracking-[0.2em] text-gray-400 mb-2 block">
                Collection
            </span>
            <h2 class="text-3xl md:text-4xl font-light text-gray-900 leading-tight">
                @lang('message.Contact Lenses')
            </h2>
        </div>




        {{-- GRILLE DE PRODUITS --}}
         <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-12 md:gap-x-8">
            @foreach ($contactLenses as $lens)
                @include('components.contact-lens-card', ['lens' => $lens])
            @endforeach
        </div>
    </section>   -->

    <!-- بنر جديد -->


    <!-- ==================================
     BRAND BANNER
     ================================== -->
<section class="brand-banner">

    <a
        href="{{ route('brands.index') }}"
        class="brand-banner-link"
        aria-label="Brands"
    >

        <picture class="brand-banner-picture">

            <!-- Mobile -->
            <source
                media="(max-width: 767px)"
                srcset="{{ app()->getLocale() === 'en'
                    ? asset('images/44eng-mob.webp')
                    : asset('images/44-1.webp') }}"
            >

            <!-- Desktop / Tablet -->
            <img
                src="{{ app()->getLocale() === 'en'
                    ? asset('images/44eng.webp')
                    : asset('images/44.webp') }}"
                alt="Brands"
                class="brand-banner-image"
            >

        </picture>


    </a>

</section>

<style>
    /* ==================================
       BRAND BANNER
       ================================== */

    .brand-banner {
        width: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden;
    }

    .brand-banner-link {
        display: block;
        width: 100%;
        margin: 0;
        padding: 0;
        line-height: 0;
    }

    .brand-banner-picture {
        display: block;
        width: 100%;
        margin: 0;
        padding: 0;
    }

    .brand-banner-image {
        display: block;
        width: 100%;
        height: auto;
        max-width: 100%;
        margin: 0;
        padding: 0;
        object-fit: contain;
    }
</style>
<!-- 
    <section class="relative w-full h-[500px] overflow-hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat transition-transform duration-[10s] scale-110"
            style="background-image: url('{{ asset('images/image section.jpg') }}');">
        </div>

        <div class="absolute inset-0 bg-black/30 backdrop-blur-[1px]"></div>

        <div class="relative max-w-4xl mx-auto px-6 text-center text-white">
            <span
                class="inline-block px-4 py-1 border border-white/30 rounded-full text-[13px] font-bold uppercase tracking-[0.3em] mb-8 backdrop-blur-md">
                @lang('home.Limited Offer')
            </span>

            <h2 class="text-3xl md:text-5xl  tracking-tight leading-tight mb-6">
                @lang('home.Free Shipping for first 30 days')
                <span class="block font-serif italic mt-2">@lang('home.Use code for 10% off')</span>
            </h2>

            <p class="text-sm md:text-base text-white/80 font-medium tracking-wide mb-12 max-w-lg mx-auto uppercase">
                @lang('home.Shop our latest')
            </p>

            <div class="flex flex-col sm:flex-row justify-center items-center gap-6">
                <a href="{{ route('products.eyeglasses') }}"
                    class="min-w-[200px] bg-white text-[#2E3192] px-8 py-4 text-sm md:text-base font-medium tracking-wide uppercase hover:bg-gray-100 transition-all duration-300 shadow-xl">
                    @lang('home.Shop eyeglasses')
                </a>
                <a href="{{ route('products.sunglasses') }}"
                    class="min-w-[200px] bg-[#2E3192] text-white border px-8 py-4 text-sm md:text-base font-medium tracking-wide uppercase hover:bg-transparent hover:text-white transition-all duration-300 backdrop-blur-sm">
                    @lang('home.Shop sunglasses')
                </a>

            </div>
        </div>
    </section> -->

    <!-- Hero Slider with Banners -->
    {{-- <section class="relative">
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper">

                @foreach ($banners as $banner)
                    <div class="swiper-slide">
                        <div class="relative h-[500px] bg-cover bg-center"
                            style="background-image: url('{{ asset('images/products/' . $banner->image) }}')">
                            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center">
                                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white">
                                    <h1 class="text-5xl font-bold mb-4">{{ $banner->title }}</h1>
                                    @if ($banner->subtitle)
                                        <p class="text-xl mb-6">{{ $banner->subtitle }}</p>
                                    @endif
                                    @if ($banner->link)
                                        <a href="{{ $banner->link }}"
                                            class="bg-blue-600 hover:bg-blue-700 px-8 py-3 rounded-lg text-lg font-semibold inline-block transition">
                                            {{ $banner->button_text ?? __('Shop Now') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </section> --}}

    <!-- Latest Products -->
    <section     id="latest-products"
 class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="flex flex-col items-center mb-16 text-center">
            <span class="text-[13px] font-bold uppercase tracking-[0.4em] text-gray-400 mb-4">
                @lang('home.Just Landed')
            </span>
            <h2 class="text-3xl md:text-4xl text-gray-900 uppercase">
                @lang('home.New Arrivals')
            </h2>
            <div class="mt-6 w-10 h-[1px] bg-black"></div>
        </div>

        {{-- LISTE PRODUITS HORIZONTALE --}}
        <div class="flex gap-6 overflow-x-auto pb-10 no-scrollbar snap-x snap-mandatory scroll-smooth">
            @foreach ($latestProducts as $product)
                <div class="flex-shrink-0 w-[240px] md:w-[280px] snap-start">
                    @include('components.product-card-enhanced', [
                        'product' => $product,
                        'isNew' => true,
                    ])
                </div>
            @endforeach

            {{-- BOUTON SHOW MORE --}}
            <div class="flex-shrink-0 w-[240px] md:w-[280px] snap-start flex items-center justify-center">
                <a href="{{ route('products.sunglasses') }}"
                    class="px-6 py-3 bg-[#2E3192] text-white font-semibold rounded-full
                      hover:bg-[#1f2270] transition
                      inline-flex items-center gap-2 rtl:flex-row-reverse">
                    @lang('home.Show More Products')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <section class="bg-slate-50 py-10 overflow-hidden ">
        <div class="max-w-7xl mx-auto px-16">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

                {{-- GAUCHE : TEXTE --}}
                <div class="space-y-6 max-w-xl">
                    <span class="inline-block text-sm font-medium tracking-wider uppercase text-indigo-600">
                        @lang('home.innovation_visuelle')
                    </span>
                    <h3 class="text-4xl md:text-5xl font-light text-slate-900 leading-tight">
                        @lang('home.try_before') <span class="font-semibold text-indigo-900">@lang('home.you_buy')</span>
                    </h3>
                    <p class="text-lg text-slate-600 leading-relaxed font-light">
                        @lang('home.virtual_tryon_text')
                    </p>
<a href="{{ route('brands.index') }}"
    class="inline-flex items-center gap-2 bg-[#f4f5ff] text-[#2E3192] px-6 py-2 rounded-full font-semibold hover:bg-[#cfd3ff] transition rtl:flex-row-reverse">
    @lang('home.Shop now') →
</a>
                </div>

                {{-- DROITE : IMAGE --}}
                <div class="relative flex justify-center lg:justify-end">
                    <div
                        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-72 h-72 bg-indigo-100 rounded-full blur-3xl opacity-60">
                    </div>
                    <div class="relative group">
<img src="{{ asset('images/virtual.png') }}"
     alt="@lang('home.virtual_tryon')"
   class="w-[760px] h-auto object-contain virtual" />             </div>

            </div>
        </div>
        <style>
@media (max-width: 767px) {

    .bg-slate-50.py-10.overflow-hidden > div > div {
        display: flex !important;
        flex-direction: column-reverse !important;
    }

    img.virtual {
        width: 400px !important;
        min-width: 0 !important;
        max-width: 100% !important;
    }
}


@media (min-width: 768px) and (max-width: 991px) {

    img.virtual {
        width: 550px !important;
        min-width: 0 !important;
        max-width: 100% !important;
    }
}


@media (min-width: 992px) {

    img.virtual {
        width: 650px !important;
        min-width: 0 !important;
        max-width: none !important;
    }
}

        </style>
    </section>
<!-- banner -->
<section class="about-banner">

    <a
        href="{{ route('about') }}"
        class="about-banner-link"
        aria-label="About Us"
    >

        <picture class="about-banner-picture">

            <!-- Mobile -->
            <source
                media="(max-width: 767px)"
                srcset="{{ app()->getLocale() === 'en'
                    ? asset('images/whomoceng.webp')
                    : asset('images/whomob.webp') }}"
            >

            <!-- Desktop / Tablet -->
            <img
                src="{{ app()->getLocale() === 'en'
                    ? asset('images/whoeng.webp')
                    : asset('images/who.webp') }}"
                alt="About Us"
                class="about-banner-image"
            >

        </picture>

    </a>

</section>

<style>
    .about-banner {
        width: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden;
    }

    .about-banner-link {
        display: block;
        width: 100%;
        margin: 0;
        padding: 0;
        line-height: 0;
    }

    .about-banner-picture {
        display: block;
        width: 100%;
        margin: 0;
        padding: 0;
    }

    .about-banner-image {
        display: block;
        width: 100%;
        height: auto;
        max-width: 100%;
        margin: 0;
        padding: 0;
        object-fit: contain;
    }
</style>






    <div class="min-h-screen flex items-center justify-center bg-white px-4 md:px-8">
        <div class="accordion-container flex flex-col md:flex-row gap-4 w-full max-w-7xl h-[600px] overflow-hidden">

            @php
                $categories = [
                    [
                        'route' => 'products.sunglasses',
                        'title' => 'home.Sunglasses',
                        'desc' => 'home.Discover our trendy sunglasses',
                        'video' => 'sunglasses.mp4',
                    ],
                    [
                        'route' => 'products.eyeglasses',
                        'title' => 'home.Eyeglasses',
                        'desc' => 'home.Our stylish eyeglasses for everyone',
                        'video' => 'glasse.mp4',
                    ],
                    [
                        'route' => 'products.clipon',
                        'title' => 'home.ClipOns',
                        'desc' => 'home.Practical and stylish clip-ons',
                        'video' => 'clipOns.mp4',
                    ],
                ];
            @endphp

            @foreach ($categories as $cat)
                <a href="{{ route($cat['route']) }}"
                    class="accordion-card group relative flex-1 overflow-hidden rounded-3xl transition-all duration-700 ease-in-out hover:flex-[2.5]">

                    <video muted loop playsinline autoplay
                        class="absolute inset-0 w-full h-full object-cover z-0 scale-110 group-hover:scale-100 transition-transform duration-[1.2s]">
                        <source src="{{ asset('images/categories/' . $cat['video']) }}" type="video/mp4">
                    </video>

                    <div class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition-colors duration-500"></div>

                    <div class="content absolute bottom-0 left-0 p-10 z-10 w-full">
                        <h3 class="text-white text-3xl  uppercase tracking-[0.25em] mb-3 transition-all duration-500 group-hover:tracking-[0.4em]"
                            style="font-family: 'Playfair Display', serif;">
                            @lang($cat['title'])
                        </h3>

                        <div class="h-[1px] w-12 bg-white/70 mb-5 group-hover:w-full transition-all duration-700"></div>

                        <p class="text-white/0 group-hover:text-white/100 text-[13px] font-semibold uppercase tracking-[0.2em] transition-all duration-700 delay-100 translate-y-4 group-hover:translate-y-0"
                            style="font-family: 'Montserrat', sans-serif;">
                            @lang($cat['desc'])
                        </p>
                    </div>
                </a>
            @endforeach

        </div>
    </div>


        <!-- <section class="store-visit-section">
        <div class="store-visit-content">
            <p class="greeting"> @lang('home.Nice to see you')</p>
            <h2 class="title"> @lang('home.Visit a nearby store')</h2>
            <p class="description"> @lang('home.Try on our full assortment of frames, get styled by a friendly advisor, and receive an eye exam from an expert optometrist.')
            </p>
            <div class="flex justify-center gap-4">
                <a href="{{ route('stores.index') }}" style="background-color: #2E3192"
                    class=" text-white px-6 py-3 rounded-full font-semibold hover:bg-blue-700 transition">
                    @lang('home.Find your store') →
                </a>
            </div>
        </div>
     <div class="store-visit-image-container">
    <a href="{{ route('stores.index') }}" class="store-image-link">
        <img src="/images/StoreImage.webp"
             alt="Store interior with shelves of glasses, named Old Port"
             class="store-image">
    </a>
</div>

        <style>
.store-visit-section {
    width: 100%;
    max-width: 1280px;
    margin: 0 auto;
    padding: 40px 64px;

    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    align-items: center;
    gap: 64px;

    box-sizing: border-box;
}

.store-visit-content {
    width: 100%;
    max-width: 100%;
    padding-right: 0;
}

.store-visit-image-container {
    width: 100%;
    max-width: 100%;
    height: auto;
    position: relative;
    overflow: hidden;
    border-radius: 20px;
    box-shadow: none;
}

.store-visit-image-container .store-image {
    display: block;
    width: 100%;
    height: auto;
    max-width: 100%;
    object-fit: contain;
}@media (max-width: 991px) {
    .store-visit-section {
        grid-template-columns: 1fr;
        gap: 40px;
        padding: 40px 20px;
    }

    .store-visit-content {
        text-align: center;
    }

    .store-visit-image-container {
        width: 100%;
        max-width: 100%;
    }
}

        </style>
    </section> -->


    <section class="bg-slate-50 py-10 overflow-hidden">
    <div class="max-w-7xl mx-auto px-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            {{-- LEFT : TEXT --}}
            <div class="space-y-6 max-w-xl">
                <span class="inline-block text-sm font-medium tracking-wider uppercase text-indigo-600">
                    @lang('home.Nice to see you')
                </span>

                <h3 class="text-4xl md:text-5xl font-light text-slate-900 leading-tight">
                    @lang('home.Visit a nearby store')
                </h3>

                <p class="text-lg text-slate-600 leading-relaxed font-light">
                    @lang('home.Try on our full assortment of frames, get styled by a friendly advisor, and receive an eye exam from an expert optometrist.')
                </p>

                <a href="{{ route('stores.index') }}"
                    class="inline-flex items-center gap-2 bg-[#f4f5ff] text-[#2E3192] px-6 py-2 rounded-full font-semibold hover:bg-[#cfd3ff] transition rtl:flex-row-reverse">
                    @lang('home.Find your store') →
                </a>
            </div>

            {{-- RIGHT : IMAGE --}}
     {{-- RIGHT : IMAGE --}}
<div class="relative flex justify-center lg:justify-end min-w-0 overflow-hidden">
    <div
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-72 h-72 bg-indigo-100 rounded-full blur-3xl opacity-60">
    </div>

    <div class="relative group w-full flex justify-center lg:justify-end">
        <a href="{{ route('stores.index') }}" class="store-image-link">
            <img src="{{ asset('images/StoreImage.webp') }}"
                alt="Store interior with shelves of glasses"
                class="store-image" />
        </a>
    </div>
</div>

        </div>
    </div>

    <style>
        /* MOBILE */
        @media (max-width: 767px) {

            .bg-slate-50.py-10.overflow-hidden > div > div {
                display: flex !important;
                flex-direction: column-reverse !important;
            }

            img.store-image {
                width: 400px !important;
                min-width: 0 !important;
                max-width: 100% !important;
            }
        }

        /* TABLET */
        @media (min-width: 768px) and (max-width: 991px) {

            img.store-image {
                width: 550px !important;
                min-width: 0 !important;
                max-width: 100% !important;
            }
        }

        /* DESKTOP */
        @media (min-width: 992px) {

            img.store-image {
                width: 650px !important;
                min-width: 0 !important;
                max-width: none !important;
            }
        }

 .store-image-link {
    display: block;
    width: 100%;
    max-width: 650px;
}

.store-image {
    display: block;
    width: 100%;
    height: auto;
    max-width: 100%;
    object-fit: contain;
}

@media (max-width: 767px) {
    .store-image {
        width: 400px;
        max-width: 100%;
    }
}

@media (min-width: 768px) and (max-width: 991px) {
    .store-image {
        width: 550px;
        max-width: 100%;
    }
}

@media (min-width: 992px) {
    .store-image {
        width: 650px;
        max-width: 100%;
    }
}
    </style>
</section>

    <!-- Top Brands -->
    <!-- <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 bg-white ">
        <div class="flex flex-col items-center mb-16 text-center">
            <h2 class="text-3xl tracking-[0.2em] text-gray-900 uppercase">
                @lang('home.Top Brands')
            </h2>
            <div class="mt-4 w-10 h-[1px] bg-black"></div>
        </div>

        <div class="swiper myBrandsSwiper relative px-12">
            <div class="swiper-wrapper flex items-center">
                @foreach ($brands as $brand)
                    <div class="swiper-slide flex items-center justify-center p-4">
                        <a href="{{ route('brand', $brand->slug) }}" class="group">
                            <img src="{{ asset($brand->logo) }}" alt="{{ $brand->name }}"
                                class="h-14 md:h-16 object-contain transition-transform duration-300 group-hover:scale-105">
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="swiper-button-next !text-black !w-8 !h-8 after:!text-lg"></div>
            <div class="swiper-button-prev !text-black !w-8 !h-8 after:!text-lg"></div>
        </div>
    </section> -->

    <!-- Contact Us -->
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-start">

                <div class="space-y-12">
                    <div>
                        <h2 class="text-4xl tracking-tight text-gray-900 uppercase mb-6">
                            @lang('home.Contact Us')
                        </h2>
                        <p class="text-gray-500 leading-relaxed max-w-md">
                            @lang('home.Our team is here to assist you with your style and prescription needs.')
                        </p>
                    </div>

                    <div class="space-y-8">
                        <!-- Phone -->
                        <div class="flex items-center gap-6 group">
                            <div
                                class="w-12 h-12 rounded-full border border-gray-200 flex items-center justify-center group-hover:bg-[#1b1e6c] group-hover:text-white transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-[13px] font-bold uppercase tracking-widest text-gray-400">
                                    @lang('home.Phone')</h4>
                                <p class="text-gray-900 font-medium">{{ settings('phone') }}</p>
                            </div>
                        </div>
                        <!-- Email -->
                        <div class="flex items-center gap-6 group">
                            <div
                                class="w-12 h-12 rounded-full border border-gray-200 flex items-center justify-center group-hover:bg-[#1b1e6c] group-hover:text-white transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                                    <path
                                        d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-[13px] font-bold uppercase tracking-widest text-gray-400">
                                    @lang('home.Email')</h4>
                                <p class="text-gray-900 font-medium">{{ settings('admin_email') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-12 rounded-[2rem] shadow-[0_30px_60px_rgba(0,0,0,0.03)] border border-gray-50">
                    <h3 class="text-xl uppercase tracking-widest text-gray-900 mb-8">@lang('home.Send Message')</h3>

                    @if (session('success'))
                        <div
                            class="mb-6 p-4 bg-gray-50 text-[14px] font-bold uppercase tracking-widest text-green-600 border border-green-100 rounded-xl">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form action="{{ route('contact.send') }}" method="POST" class="space-y-8">
                        @csrf
                        <div class="relative">
                            <input type="text" name="name" required
                                class="w-full border-b border-gray-200 focus:border-black outline-none py-3 text-sm transition-colors placeholder:text-gray-300 bg-transparent"
                                placeholder="@lang('home.Your full name')">
                        </div>
                        <div class="relative">
                            <input type="email" name="email" required
                                class="w-full border-b border-gray-200 focus:border-black outline-none py-3 text-sm transition-colors placeholder:text-gray-300 bg-transparent"
                                placeholder="@lang('home.E-mail')">
                        </div>
                        <div class="relative">
                            <textarea name="message" rows="3" required
                                class="w-full border-b border-gray-200 focus:border-black outline-none py-3 text-sm transition-colors placeholder:text-gray-300 bg-transparent resize-none"
                                placeholder="@lang('home.write your massage')"></textarea>
                        </div>
                        <button type="submit"
                            class="w-full bg-[#1b1e6c] text-white py-5 text-[13px] font-bold uppercase tracking-[0.3em] hover:bg-gray-800 transition-all rounded-2xl shadow-lg shadow-black/10">
                            @lang('home.Send Message')
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto  px-8 sm:px-8 lg:px-12 py-12 d-none" style="display: none;">
        <div class="flex items-center justify-center gap-3 mb-4">
            <span class="h-[1px] w-6 bg-gray-200"></span>
            <span class="text-[13px] font-bold uppercase tracking-[0.4em] text-gray-400">
                @lang('home.Trust & Quality')
            </span>
            <span class="h-[1px] w-6 bg-gray-200"></span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
            <div class="flex items-center space-x-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#00AEEF]" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 3.5l7 2.5v4.5c0 4.55-3.05 8.95-7 10-3.95-1.05-7-5.45-7-10V6l7-2.5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                </svg>
                <p class="text-lg font-semibold text-[#1B2352]">@lang('home.Secure payment')</p>
            </div>
            <div class="flex items-center space-x-4">
                <p class="text-lg font-semibold">
                    <span class="text-[#00AEEF]">1916</span>
                    <span class="text-[#1B2352]"> @lang('home.Since 1916')</span>
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#00AEEF]" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 2l2.39 1.17 2.61-.02 1.4 2.1 2.25 1 .01 2.61L23 12l-1 2.25-.01 2.61-2.25 1-1.4 2.1-2.61-.02L12 22l-2.39-1.17-2.61.02-1.4-2.1-2.25-1L3 14.25 2 12l1-2.25.01-2.61 2.25-1 1.4-2.1 2.61.02z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                </svg>
                <p class="text-lg font-semibold text-[#1B2352] leading-tight">
                    @lang('home.Warranty against manufacturing')<br>
                    @lang('home.for 90 days')
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#00AEEF]" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L20 6" />
                </svg>
                <p class="text-lg font-semibold text-[#1B2352]">@lang('home.100% Original')</p>
            </div>

        </div>
    </section>

    <!-- Swiper CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- Initialize Swipers -->
    <script>
        var swiper = new Swiper(".myBrandsSwiper", {
            slidesPerView: 5,
            spaceBetween: 40,
            loop: true,
            autoplay: {
                delay: 1500,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            breakpoints: {
                320: {
                    slidesPerView: 2
                },
                640: {
                    slidesPerView: 3
                },
                1024: {
                    slidesPerView: 5
                }
            }
        });

        // Hero slider with fade effect
        new Swiper(".mySwiperHero", {
            loop: true,
            effect: "fade",
            autoplay: {
                delay: 3500,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });

        // Banners slider
        new Swiper('.hero-swiper', {
            autoplay: {
                delay: 5000
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true
            },
            loop: true
        });
    </script>

    <!-- JavaScript for Category Filtering -->
    <script>
        let currentCategory = 'all';
        let currentSort = 'latest';

        document.querySelectorAll('.accordion-card').forEach(card => {
            const video = card.querySelector('video');

            card.addEventListener('mouseenter', () => {
                video.play();
            });

            card.addEventListener('mouseleave', () => {
                video.pause();
                video.currentTime = 0;
            });
        });
        // Filter products by category
        async function filterProductsByCategory(categoryId) {
            currentCategory = categoryId;

            // Update active pill
            document.querySelectorAll('.category-pill-btn').forEach(btn => {
                if (btn.dataset.category == categoryId) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            // Show skeleton loader
            const grid = document.getElementById('filtered-products-grid');
            grid.style.opacity = '0';
            document.getElementById('skeleton-loader').classList.remove('hidden');
            document.getElementById('empty-state').classList.add('hidden');

            try {
                // Fetch filtered products
                const response = await fetch(
                    `{{ route('home.filter') }}?category_id=${categoryId}&sort=${currentSort}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                const data = await response.json();

                // Wait for animation
                await new Promise(resolve => setTimeout(resolve, 500));

                // Hide skeleton
                document.getElementById('skeleton-loader').classList.add('hidden');

                if (data.success && data.products.length > 0) {
                    // Update header
                    document.getElementById('current-filter-title').textContent =
                        categoryId === 'all' ? '{{ __('All Products') }}' : data.products[0].category;
                    document.getElementById('products-count').textContent = data.count;

                    // Render products
                    renderFilteredProducts(data.products);
                    grid.style.opacity = '1';
                } else {
                    grid.innerHTML = '';
                    document.getElementById('empty-state').classList.remove('hidden');
                    grid.style.opacity = '1';
                }

                // Smooth scroll
                document.getElementById('products-by-category').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });

            } catch (error) {
                console.error('Error:', error);
                document.getElementById('skeleton-loader').classList.add('hidden');
                grid.style.opacity = '1';
            }
        }

        // Render products
        function renderFilteredProducts(products) {
            const grid = document.getElementById('filtered-products-grid');
            grid.innerHTML = products.map((product, index) => createProductCard(product, index)).join('');
        }

        // Create product card (simplifié pour éviter erreurs)
        function createProductCard(product, index) {
            return `
                <div class="product-card-animated" style="animation-delay: ${index * 0.05}s;">
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition">
                        <a href="${product.url}" class="block">
                            <div class="aspect-square bg-gray-100">
                                ${product.image ?
                                    `<img src="${product.image}" alt="${product.name}" class="w-full h-full object-cover">` :
                                    `<div class="w-full h-full flex items-center justify-center"><i class="fas fa-glasses text-6xl text-gray-300"></i></div>`
                                }
                            </div>
                        </a>
                        <div class="p-4">
                            <p class="text-sm text-gray-500 mb-1">${product.brand}</p>
                            <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">${product.name}</h3>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold">$${product.final_price.toFixed(2)}</span>
                                <button onclick="addToCart(${product.id})" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Apply sorting
        function applySorting(sortBy) {
            currentSort = sortBy;
            filterProductsByCategory(currentCategory);
        }

        // Add to cart
        async function addToCart(productId) {
            try {
                const response = await fetch('{{ route('cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: 1
                    })
                });
                const data = await response.json();
                if (data.success) {
                    alert('{{ __('Product added to cart!') }}');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    </script>
    <script>
        const cards = document.querySelectorAll('.card');

        cards.forEach(card => {
            const video = card.querySelector('video');

            card.addEventListener('mouseenter', () => {
                if (video) video.play();
            });

            card.addEventListener('mouseleave', () => {
                if (video) video.pause();
                if (video) video.currentTime = 0; // remet au début
            });
        });
    </script>
    <script>
        const brandNav = document.getElementById('brand-nav');

        // Sauvegarder la position du scroll horizontal quand on clique
        brandNav.addEventListener('click', () => {
            localStorage.setItem('brandScrollPos', brandNav.scrollLeft);
        });

        // Restaurer la position au chargement
        window.addEventListener('load', () => {
            const scrollPos = localStorage.getItem('brandScrollPos');
            if (scrollPos) {
                brandNav.scrollLeft = scrollPos;
            }
        });
    </script>

@endsection
