<div class="bg-white overflow-hidden group transition duration-300 relative border border-gray-300 x-data="{ current: 0 }">

    {{-- IMAGE --}}
    <div class="relative aspect-[4/4] overflow-hidden bg-[#f9f9f9]">

        {{-- BRAND --}}
        <span class="absolute top-3 left bg-white text-black text-[10px] uppercase px-2 py-0.5 shadow-sm z-40">
            {{ $product->brand->name ?? '--' }}
        </span>

        <div
            class="absolute top-3 right-3 flex flex-col gap-2 z-20 opacity-0
                   group-hover:opacity-100 transition-all duration-300
                   transform translate-x-2 group-hover:translate-x-0">

            {{-- TRY ON --}}
            @if ($product->tryon_available && $product->tryonFile)
                <a href="{{ route('tryon.show', $product->id) }}"
                    class="bg-white p-2 rounded-full shadow-md hover:bg-[#212482] hover:text-white transition" title="@lang('message.virtual_try_on')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
                        <path
                            d="M3 5a2 2 0 0 0-2 2v.5H.5a.5.5 0 0 0 0 1H1V9a2 2 0 0 0 2 2h1a3 3 0 0 0 3-3 1 1 0 1 1 2 0 3 3 0 0 0 3 3h1a2 2 0 0 0 2-2v-.5h.5a.5.5 0 0 0 0-1H15V7a2 2 0 0 0-2-2h-2z" />
                    </svg>
                </a>
            @endif

            @auth
                <button onclick="toggleWishlist({{ $product->id }}, 'product', this)"
                    class="bg-white p-2 rounded-full shadow-md hover:bg-red-500 hover:text-white transition-colors duration-300 flex items-center justify-center">
                    <i class="far fa-heart"></i>
                </button>
            @endauth <a href="{{ route('products.show', $product->seo_url) }}"
                class="bg-white p-2 rounded-full shadow-md hover:bg-black hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </a>
        </div>

        {{-- IMAGES --}}
        <a href="{{ route('products.show', $product->seo_url) }}" class="block w-full h-full">
            @php
                $allImages = collect([$product->primaryImage])->merge($product->images ?? collect());
            @endphp

            @foreach ($allImages as $index => $image)
                <img src="{{ asset($image->image) }}"
                    class="absolute inset-0 w-full h-full object-cover transition-opacity duration-700"
                    :class="current === {{ $index }} ? 'opacity-100' : 'opacity-0'">
            @endforeach
        </a>

        {{-- ADD TO CART --}}
        <button type="button" onclick="event.stopPropagation(); addToCart({{ $product->id }}, 'product', 1, null, 0)"
            class="absolute bottom-0 left-0 right-0
           bg-[#1a1a1a] text-white py-4 text-xs font-bold uppercase tracking-widest
           flex items-center justify-center gap-2
           transform translate-y-full group-hover:translate-y-0
           transition-transform duration-300 z-30">

            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>

            @lang('message.ADD TO CART')
        </button>


        {{-- DISCOUNT --}}
        @if ($product->discount_percentage > 0)
            <span class="absolute bottom-3 left-3 bg-red-600 text-white text-[10px] px-2 py-1 rounded-full z-40">
                -{{ $product->discount_percentage }}%
            </span>
        @endif
    </div>

    {{-- INFOS PRODUIT --}}
    <div class="py-4 text-center">
        <h3 class="font-medium text-gray-900 text-[13px] leading-tight max-w-[70%] mx-auto">
            <a href="{{ route('products.show', $product->seo_url) }}">
                {{ $product->name }}
            </a>
        </h3>

        <div class="flex flex-col items-center mt-1">
            @if ($product->discount_price)
                <span class="text-[14px] font-semibold text-gray-900">
                    {{ number_format($product->discount_price, 2) }} SAR
                </span>
                <span class="text-[12px] text-gray-400 line-through">
                    {{ number_format($product->price, 2) }} SAR
                </span>
            @else
                <span class="text-[14px] font-semibold text-gray-600">
                    {{ number_format($product->price, 2) }} SAR
                </span>
            @endif
        </div>
    </div>
</div>
