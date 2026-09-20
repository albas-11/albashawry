<div class="bg-white overflow-hidden group transition duration-300 relative border border-gray-300">

    {{-- IMAGE --}}
    <div class="relative aspect-square overflow-hidden bg-[#f9f9f9]">

        {{-- Boutons overlay --}}
        <div class="absolute top-3 right-3 flex flex-col gap-2 z-20 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0">
            @auth
                <button onclick="toggleWishlist({{ $accessory->id }}, 'accessory', this)"
                    class="bg-white p-2 rounded-full shadow-md hover:bg-red-500 hover:text-white transition-colors duration-300 flex items-center justify-center">
                    <i class="far fa-heart"></i>
                </button>
            @endauth
            <a href="{{ route('products.accessories_show', $accessory->id) }}"
                class="bg-white p-2 rounded-full shadow-md hover:bg-black hover:text-white transition-colors duration-300 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </a>
        </div>

        {{-- Slider d'images --}}
        <a href="{{ route('products.accessories_show', $accessory->id) }}" class="w-full h-full block">
            @php
                $allImages = collect();
                if ($accessory->primaryImage) {
                    $allImages->push($accessory->primaryImage);
                }
                if ($accessory->images) {
                    $allImages = $allImages->merge($accessory->images);
                }
            @endphp

            @if ($allImages->count() > 0)
                @foreach ($allImages as $index => $image)
                    <img src="{{ asset($image->image) }}" alt="Accessory Image"
                        class="w-full h-full object-cover absolute inset-0 transition-opacity duration-700"
                        :class="current === {{ $index }} ? 'opacity-100' : 'opacity-0'">
                @endforeach
            @else
                <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400">
                    <i class="fas fa-camera text-4xl"></i>
                </div>
            @endif
        </a>

        {{-- Bouton add-to-cart --}}
        <button onclick="addToCart({{ $accessory->id }}, 'accessory', 1)"
            class="absolute bottom-0 left-0 right-0
            bg-[#1a1a1a] text-white py-4 text-xs font-bold uppercase tracking-widest
            flex items-center justify-center gap-2
            transform translate-y-full group-hover:translate-y-0
            transition-transform duration-300 z-30
            {{ !$accessory->isInStock() ? 'opacity-40 cursor-not-allowed' : '' }}"
            {{ !$accessory->isInStock() ? 'disabled' : '' }}>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            @lang('message.ADD TO CART')
        </button>
    </div>

    {{-- Infos produit --}}
    <div class="py-4 text-center">
        <h3 class="font-medium text-gray-900 text-[13px] leading-tight max-w-[70%] mx-auto">
            <a href="{{ route('products.accessories_show', $accessory->id) }}">
                {{ $accessory->getTranslation('name', app()->getLocale()) ?: $accessory->getTranslation('name', 'fr') }}
            </a>
        </h3>

        <div class="flex flex-col items-center mt-1">
            @if (isset($accessory->discount_price) && $accessory->discount_price > 0)
                <span class="text-[14px] font-semibold text-gray-900">
                    {{ number_format($accessory->discount_price, 2) }} SAR
                </span>
                <span class="text-[12px] text-gray-400 line-through">
                    {{ number_format($accessory->price, 2) }} SAR
                </span>
            @else
                <span class="text-[14px] font-semibold text-gray-600">
                    {{ number_format($accessory->price, 2) }} SAR
                </span>
            @endif
        </div>
    </div>

</div>
