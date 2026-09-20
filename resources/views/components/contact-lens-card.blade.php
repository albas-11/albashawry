<div class="bg-white overflow-hidden group transition duration-300 relative border border-gray-300">

    <div class="relative aspect-[4/4] overflow-hidden bg-[#f9f9f9]">

        <span class="absolute top-3 left bg-white text-black text-[10px] uppercase px-2 py-0.5  shadow-sm z-40">
            {{ $lens->brand->name ?? '--' }}

        </span>
        <div
            class="absolute top-3 right-3 flex flex-col gap-2 z-20 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0">
            @auth
                <button onclick="toggleWishlist({{ $lens->id }}, 'contact_lens', this)"
                    class="bg-white p-2 rounded-full shadow-md hover:bg-red-500 hover:text-white transition-colors duration-300 flex items-center justify-center"
                    >
                    <i class="far fa-heart"></i>
                </button>
            @endauth
            <a href="{{ route('products.show_lenses', $lens) }}"
                class="bg-white p-2 rounded-full shadow-md hover:bg-black hover:text-white transition-colors duration-300 flex items-center justify-center"
               >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </a>

        </div>

        <a href="{{ route('products.show_lenses', $lens) }}" class="w-full h-full block">
            @php
                $allImages = collect();
                if ($lens->primaryImage) {
                    $allImages->push($lens->primaryImage);
                }
                if ($lens->images) {
                    $allImages = $allImages->merge($lens->images);
                }
            @endphp

            @if ($allImages->count() > 0)
                @foreach ($allImages as $index => $image)
                    <img src="{{ asset($image->image) }}" alt="Product Image"
                        class="w-full h-full object-cover absolute inset-0 transition-opacity duration-700"
                        :class="current === {{ $index }} ? 'opacity-100' : 'opacity-0'">
                @endforeach
            @else
                <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif
        </a>

        <button onclick="addToCart({{ $lens->id }}, 'contact_lens', 1)"
            class="absolute bottom-0 left-0 right-0
           bg-[#1a1a1a] text-white py-4 text-xs font-bold uppercase tracking-widest
           flex items-center justify-center gap-2
           transform translate-y-full group-hover:translate-y-0
           transition-transform duration-300 z-30
           {{ !$lens->isInStock() ? 'opacity-40 cursor-not-allowed' : '' }}"
            {{ !$lens->isInStock() ? 'disabled' : '' }}>

            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>

            @lang('message.ADD TO CART')
        </button>
    </div>

    {{-- INFOS PRODUIT (Centrées) --}}
    <div class="py-4 text-center">
        <h3 class="font-medium text-gray-900 text-[13px] leading-tight max-w-[70%] mx-auto text-center">
            <a href="{{ route('products.show_lenses', $lens) }}">
                {{ $lens->getTranslation('name', app()->getLocale()) ?: $lens->getTranslation('name', 'fr') }}
            </a>
        </h3>


        <div class="flex flex-col items-center">
            @if (isset($lens->discount_price) && $lens->discount_price > 0)
                <span class="text-[14px] font-semibold text-gray-900">
                    ${{ number_format($lens->discount_price, 2) }}
                </span>
                <span class="text-[12px] text-gray-400 line-through">
                    ${{ number_format($lens->price, 2) }}
                </span>
            @else
                <span class="text-[14px] font-semibold text-gray-600">
                    ${{ number_format($lens->price, 2) }}
                </span>
            @endif
        </div>
    </div>
</div>
