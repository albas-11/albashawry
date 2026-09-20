<div class="group p-4 border border-gray-200" x-data="{ current: 0 }" style="background-color: #fffefe">
    <div class="relative aspect-square bg-[#f9f9f9] overflow-hidden mb-4">

        @if ($product->stock <= 0)
            <span
                class="absolute top-3 left-3 z-10 bg-black text-white text-[10px] font-bold px-2 py-1 uppercase tracking-tighter">
                @lang('message.Sold Out')
            </span>
        @endif

        @if ($product->tryon_available && $product->tryonFile)
            <a href="{{ route('tryon.show', $product->id) }}"
                class="absolute top-1 right-1 z-10 text-[13px] font-bold px-3 py-1.5 shadow-sm border border-[#212482] text-[#212482] bg-white/80 backdrop-blur-sm flex items-center gap-1 hover:bg-[#212482] hover:text-white transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                    class="bi bi-sunglasses" viewBox="0 0 16 16">
                    <path
                        d="M3 5a2 2 0 0 0-2 2v.5H.5a.5.5 0 0 0 0 1H1V9a2 2 0 0 0 2 2h1a3 3 0 0 0 3-3 1 1 0 1 1 2 0 3 3 0 0 0 3 3h1a2 2 0 0 0 2-2v-.5h.5a.5.5 0 0 0 0-1H15V7a2 2 0 0 0-2-2h-2a2 2 0 0 0-1.888 1.338A2 2 0 0 0 8 6a2 2 0 0 0-1.112.338A2 2 0 0 0 5 5zm0 1h.941c.264 0 .348.356.112.474l-.457.228a2 2 0 0 0-.894.894l-.228.457C2.356 8.289 2 8.205 2 7.94V7a1 1 0 0 1 1-1" />
                </svg>
                <span> @lang('message.Try-On')</span>
            </a>
        @endif

        <a href="{{ route('products.show', $product->seo_url) }}" class="block h-full w-full">
            @php
                $allImages = collect();

                if ($product->getPrimaryImagePath()) {
                    $allImages->push((object) ['image' => $product->getPrimaryImagePath()]);
                }

                // Ajouter les autres images
                if ($product->images && $product->images->count()) {
                    $allImages = $allImages->merge($product->images);
                }

                // Placeholder si aucune image
                if ($allImages->isEmpty()) {
                    $allImages->push((object) ['image' => 'images/placeholder.png']);
                }
            @endphp

            @foreach ($allImages as $index => $image)
                <img src="{{ asset($image->image) }}"
                    class="w-full h-full object-contain absolute inset-0 transition-opacity duration-500"
                    :class="current === {{ $index }} ? 'opacity-100' : 'opacity-0'">
            @endforeach

        </a>

        <div
            class="absolute inset-0 flex items-end justify-center pb-6 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-y-4 group-hover:translate-y-0 pointer-events-none bg-transparent">
            <div class="flex gap-3 pointer-events-auto">
                <div class="relative group/tooltip">
                    <button type="button"
                        onclick="event.stopPropagation(); addToCart({{ $product->id }}, 'product', 1, null, 0)"
                        class="w-11 h-11 bg-white rounded-full flex items-center justify-center
           shadow-xl hover:bg-red-500 hover:text-white transition-all">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </button>

                    <span
                        class="absolute -top-10 left-1/2 -translate-x-1/2 bg-black text-white
               text-[11px] py-1 px-3 rounded opacity-0
               group-hover/tooltip:opacity-100 transition-opacity
               whitespace-nowrap uppercase tracking-widest">
                        @lang('message.Add to Cart')
                    </span>
                </div>

                <a href="{{ route('products.show', $product->seo_url) }}"
                    class="relative group/tooltip w-11 h-11 bg-white rounded-full flex items-center justify-center shadow-xl hover:bg-red-500 hover:text-white transition-all">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                 -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>

                    <span
                        class="absolute -top-10 left-1/2 -translate-x-1/2 bg-black text-white text-[11px]
               py-1 px-3 rounded opacity-0 group-hover/tooltip:opacity-100
               transition-opacity whitespace-nowrap uppercase tracking-widest">
                        @lang('message.Quick View')
                    </span>
                </a>

                @auth
                    <div class="relative group/tooltip">
                        <button onclick="toggleWishlist({{ $product->id }}, 'product', this)"
                            class="w-11 h-11 bg-white rounded-full flex items-center justify-center
           shadow-xl border
           hover:bg-red-500 hover:text-white transition-all">
                            <i class="fas fa-heart"></i>
                        </button>

                        <span
                            class="absolute -top-10 left-1/2 -translate-x-1/2 bg-black text-white
               text-[11px] py-1 px-3 rounded opacity-0
               group-hover/tooltip:opacity-100 transition-opacity
               whitespace-nowrap uppercase tracking-widest">
                            @lang('message.Add to Wishlist')
                        </span>
                    </div>
                @endauth

            </div>
        </div>
    </div>

    <div class="text-left">
        <div class="flex text-orange-400 gap-0.5 mb-2">
            @for ($i = 0; $i < 5; $i++)
                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                    <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            @endfor
        </div>
        <a href=""
            class="text-[10px] text-gray-500 mb-1 uppercase tracking-wider">{{ $product->brand->name }}</a>
        <h3 class="text-[15px] font-medium text-gray-900 mb-1">
            <a href="{{ route('products.show', $product->seo_url) }}"
                class="block text-[13px] font-medium text-gray-900 truncate">
                {{ $product->name }}
            </a>
        </h3>

        <div class="text-[15px] font-bold text-gray-700">
            @if ($product->discount_price)
                <span class="text-red-400">{{ number_format($product->discount_price, 2) }}SAR</span>
                <span
                    class="text-gray-400 line-through font-normal text-sm ml-2">{{ number_format($product->price, 2) }}SAR</span>
            @else
                <span>{{ number_format($product->price, 2) }}SAR</span>
            @endif
        </div>
    </div>
</div>
