@extends('layouts.app')


@section('content')
    {{-- Script nécessaire pour le Modal --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="bg-white min-h-screen" x-data="{ showReviewModal: false }">
        <!-- SUCCESS / ERROR MESSAGE -->
        <div x-data="{ show: false, type: 'success', text: '' }" x-show="show" x-transition x-cloak
            @message.window="
        type = $event.detail.type;
        text = $event.detail.text;
        show = true;
        setTimeout(() => show = false, 3000); "
            class="fixed top-6 right-6 z-[200]">
            <div :class="type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'"
                class="px-5 py-3 rounded-lg shadow-lg text-sm font-semibold">
                <span x-text="text"></span>
            </div>
        </div>

        {{-- BREADCRUMB --}}
        <div class="bg-gray-50 border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
                <nav class="flex items-center space-x-1.5 text-xs text-gray-600">
                    <a href="{{ route('home') }}" class="hover:text-indigo-600 transition">@lang('message.Home')</a>
                    <svg class="w-2.5 h-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-gray-900 font-medium">
                        {{ $contactLens->getTranslation('name', app()->getLocale()) ?: $contactLens->getTranslation('name', 'fr') }}
                    </span>
                </nav>
            </div>
        </div>

        {{-- PRODUIT --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
            <div class="flex flex-col lg:flex-row gap-6 px-4 lg:px-10 py-6 bg-white">
                <div class="lg:basis-[50%]">
                    <div class="relative bg-[#F9F9F9] rounded-sm border border-gray-100 h-[420px] overflow-hidden">

                        {{-- MAIN IMAGE --}}

                        <img id="mainProductImage"
                            src="{{ $contactLens->getPrimaryImagePath()
                                ? asset($contactLens->getPrimaryImagePath())
                                : asset('images/default-product.jpg') }}"
                            class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300">


                        @if ($contactLens->images->count() > 1)
                            {{-- LEFT ARROW --}}
                            <button onclick="prevImage()"
                                class="absolute left-3 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-white/80 hover:bg-white shadow flex items-center justify-center">
                                <i class="fas fa-chevron-left text-gray-700 text-sm"></i>
                            </button>

                            {{-- RIGHT ARROW --}}
                            <button onclick="nextImage()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-white/80 hover:bg-white shadow flex items-center justify-center">
                                <i class="fas fa-chevron-right text-gray-700 text-sm"></i>
                            </button>
                        @endif
                    </div>

                    {{-- THUMBNAILS (ONLY IF MULTIPLE IMAGES) --}}
                    @if ($contactLens->images->count() > 1)
                        <div class="flex justify-center gap-2 mt-3">
                            @foreach ($contactLens->images as $index => $image)
                                <button onclick="setImage({{ $index + 1 }})"
                                    class="thumb border border-gray-200 rounded-sm overflow-hidden w-16 h-16 hover:border-[#212482] transition">
                                    <img src="{{ asset($image->image) }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Infos Produit --}}
                <div class="lg:basis-[50%]">
                    <div class="pb-2 border-b border-gray-200 space-y-1">
                        <p class="text-[11px] text-indigo-600 font-semibold uppercase tracking-wider">
                            {{ $contactLens->brand->name ?? '-' }}
                        </p>

                        <h1 class="text-xl font-bold text-gray-900 leading-snug">
                            {{ $contactLens->getTranslation('name', app()->getLocale()) ?: $contactLens->getTranslation('name', 'fr') }}
                        </h1>

                        <div class="flex items-center justify-between mt-1">
                            <div class="flex items-center gap-1 text-[11px]">
                                <div class="flex items-center text-yellow-500">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i
                                            class="fas fa-star{{ $i <= floor($contactLens->rating ?? 0) ? '' : ($i - ($contactLens->rating ?? 0) < 1 ? '-half-alt' : ' text-gray-300') }} w-3 h-3"></i>
                                    @endfor
                                </div>
                                <span class="text-gray-500">({{ $contactLens->reviews->count() }} @lang('message.Reviews'))</span>
                            </div>

                            <div
                                class="text-[9px] font-bold uppercase {{ $contactLens->isInStock() ? 'text-green-600' : 'text-red-500' }}">
                                {{ $contactLens->isInStock() ? '● In Stock' : '● Out of Stock' }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xl font-bold text-[#2E3192]">
                                    {{ number_format($contactLens->discount_price ?? $contactLens->price, 2) }} <span
                                        class="text-xs font-normal">SAR</span>
                                </span>
                                @if ($contactLens->discount_price)
                                    <span
                                        class="text-sm text-gray-400 line-through">{{ number_format($contactLens->price, 2) }}</span>
                                    <span
                                        class="text-[10px] font-bold text-red-600 bg-red-50 px-1.5 py-0.5 rounded">-{{ $contactLens->discount_percentage }}%</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- detaills -->
                    <div class="mt-4">
                        <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">@lang('message.Frame Specifications')
                        </h3>
                        <div class="grid grid-cols-2 gap-x-6 gap-y-1.5 text-[12px]">
                            <div class="flex justify-between border-b border-gray-100 py-1">
                                <span class="text-gray-400">@lang('message.Color:')</span>
                                <span class="font-medium text-gray-800 capitalize">{{ $contactLens->color }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-100 py-1">
                                <span class="text-gray-400">@lang('message.Duration:')</span>
                                <span
                                    class="font-medium text-gray-800 capitalize">{{ $contactLens->duration_of_use }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-100 py-1">
                                <span class="text-gray-400">@lang('message.Diameter:')</span>
                                <span class="font-medium text-gray-800 capitalize">{{ $contactLens->diameter }}mm</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-100 py-1">
                                <span class="text-gray-400">@lang('message.Water Content:')</span>
                                <span class="font-medium text-gray-800">{{ $contactLens->water_content }}%</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-100 py-1">
                                <span class="text-gray-400">@lang('message.Type'):</span>
                                <span class="font-medium text-gray-800">{{ $contactLens->type }} Colored Lenses</span>
                            </div>
                        </div>
                    </div>
                    {{-- Description --}}
                    <div class="mt-4">
                        <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">@lang('message.Description')
                        </h3>
                        <div class="text-gray-600 leading-relaxed text-xs">
                            {!! $contactLens->getTranslation('description', app()->getLocale()) ?:
                                $contactLens->getTranslation('description', 'fr') !!}
                        </div>
                    </div>

                    {{-- BOUTONS D'ACTION --}}
                    <div class="mt-auto pt-6">
                        <div class="flex items-center gap-3">
                            <!-- Quantity -->
                            <div class="flex items-center border border-gray-200 rounded-sm bg-white h-11">
                                <button onclick="updateQuantity(-1)"
                                    class="px-3 text-gray-400 hover:text-black hover:bg-gray-50 h-full transition">-</button>
                                <input type="text" id="quantity" value="1" readonly
                                    class="w-10 text-center text-sm border-none focus:ring-0 font-semibold">
                                <button onclick="updateQuantity(1)"
                                    class="px-3 text-gray-400 hover:text-black hover:bg-gray-50 h-full transition">+</button>
                            </div>

                            <!-- Add to Cart -->
                            <button
                                onclick="addToCart( {{ $contactLens->id }},  'contact_lens',  parseInt(document.getElementById('quantity').value))"
                                class="flex-1 bg-[#212482] text-white h-11 rounded-sm text-xs font-bold hover:bg-black transition-all flex items-center justify-center gap-2 {{ !$contactLens->isInStock() ? 'opacity-40 cursor-not-allowed' : '' }}"
                                {{ !$contactLens->isInStock() ? 'disabled' : '' }}>
                                <i class="fas fa-shopping-bag text-[14px]"></i> @lang('message.ADD TO CART')
                            </button>


                            <!-- Wishlist -->
                            @auth
                                <button onclick="toggleWishlist({{ $contactLens->id }}, 'contact_lens', this)"
                                    class="h-11 w-11 flex items-center justify-center border border-gray-200 rounded-sm text-gray-300 hover:text-red-500 transition-colors">
                                    <i class="fas fa-heart"></i>
                                </button>

                            @endauth

                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- PRODUCT REVIEWS SECTION --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-t border-gray-100 mt-12">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-6">
                <div>
                    <h2 class="text-3xl  text-gray-900 tracking-tight">@lang('message.Customer Reviews')</h2>
                    <p class="text-sm text-gray-400 mt-1 uppercase tracking-widest">@lang('message.Feedback from our community')</p>
                </div>

                @auth
                    <button @click="showReviewModal = true"
                        class="inline-flex items-center px-8 py-3 bg-gray-900 hover:bg-black text-white text-xs font-bold uppercase tracking-widest transition-all rounded-full">
                        @lang('message.Write a Review')
                    </button>
                @else
                    <div class="text-sm text-gray-500 border-l-2 border-gray-200 pl-4">
                        @lang('message.Please') <a href="{{ route('login') }}"
                            class="text-gray-900 font-bold underline underline-offset-4">@lang('message.login')</a>
                        @lang('message.to write a review.')
                    </div>
                @endauth
            </div>

            @if ($contactLens->reviews->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($contactLens->reviews as $review)
                        <div class="group rounded-lg p-4 transition-opacity max-w-[280px]"
                            style="background-color: rgb(246, 246, 246)">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-gray-100 border border-gray-300 flex items-center justify-center text-[10px] font-bold text-gray-400 uppercase">
                                        {{ substr($review->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-900">{{ $review->user->name }}</h4>
                                        <span
                                            class="text-[10px] text-gray-400">{{ $review->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center text-[9px] text-yellow-500 gap-0.5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-gray-200' }}"></i>
                                    @endfor
                                </div>
                            </div>

                            @if ($review->title)
                                <h5 class="text-sm font-bold text-gray-800 mb-2">{{ $review->title }}</h5>
                            @endif
                            <p class="text-sm text-gray-500 leading-relaxed ">{{ $review->comment }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-20 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                    <p class="text-gray-400 text-sm  tracking-wide">@lang('message.No reviews yet. Be the first to share your experience!')</p>
                </div>
            @endif
        </div>

        {{-- MODAL --}}
        {{-- Modal de Review --}}
        <div x-show="showReviewModal" x-transition.opacity x-cloak
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/30 backdrop-blur-sm">
            <div class="bg-white w-full max-w-md overflow-hidden rounded-2xl shadow-xl"
                @click.away="showReviewModal = false">
                <div class="px-8 pt-8 pb-4 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">@lang('message.Share your opinion')</h3>
                    <button @click="showReviewModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                <form action="{{ route('reviews.store') }}" method="POST" class="p-8 pt-4 space-y-6">
                    @csrf
                    <input type="hidden" name="reviewable_id" value="{{ $contactLens->id }}">
                    <input type="hidden" name="reviewable_type" value="contact_lens">


                    <div>
                        <label for="title" class="block text-xs font-bold text-gray-600">@lang('message.Title')</label>
                        <input type="text" name="title" id="title"
                            class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label for="rating" class="block text-xs font-bold text-gray-600">@lang('message.Rating')</label>
                        <select name="rating" id="rating"
                            class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            <option value="5">5 - Excellent</option>
                            <option value="4">4 - Very Good</option>
                            <option value="3">3 - Good</option>
                            <option value="2">2 - Fair</option>
                            <option value="1">1 - Poor</option>
                        </select>
                    </div>

                    <div>
                        <label for="comment" class="block text-xs font-bold text-gray-600">@lang('message.Comment')</label>
                        <textarea name="comment" id="comment" rows="4"
                            class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 text-sm"></textarea>
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-gray-900 text-white text-xs font-bold uppercase tracking-widest hover:bg-black transition-colors rounded-lg">
                        @lang('message.Post Review')
                    </button>
                </form>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded bg-red-50 border border-red-300 p-3 text-sm text-red-700">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded bg-red-100 border border-red-300 p-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 rounded bg-green-100 border border-green-300 p-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- PRODUCTS YOU MAY LIKE --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-t border-gray-100 mt-16">
            {{-- En-tête élégant --}}
            <div class="flex items-center justify-between mb-10">
                <h2 class="text-xl md:text-2xl tracking-[0.15em] text-gray-900 uppercase">
                    @lang('message.Products you may like')
                </h2>
                <div class="h-[1px] flex-grow bg-gray-200 ml-8 hidden md:block"></div>
            </div>

            {{-- Grille de produits --}}
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-x-4 gap-y-10 md:gap-x-8">
                @foreach ($relatedProducts as $lens)
                    <div class="transition-transform duration-500 hover:-translate-y-1">
                        @include('components.contact-lens-card', ['lens' => $lens])
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- SCRIPTS --}}
    <script>
        function changeImage(src) {
            const mainImage = document.querySelector('#mainLensImage');
            if (mainImage) {
                mainImage.classList.add('opacity-0');
                setTimeout(() => {
                    mainImage.src = src;
                    mainImage.classList.remove('opacity-0');
                }, 150);
            }
        }

        function updateQuantity(change) {
            const quantityInput = document.getElementById('quantity');
            let quantity = parseInt(quantityInput.value);
            quantity += change;
            if (quantity < 1) quantity = 1;
            quantityInput.value = quantity;
        }

        // async function addToCart(itemId) {
        //     const quantity = parseInt(document.getElementById('quantity').value);

        //     try {
        //         const response = await fetch('{{ route('cart.add') }}', {
        //             method: 'POST',
        //             headers: {
        //                 'Content-Type': 'application/json',
        //                 'X-CSRF-TOKEN': '{{ csrf_token() }}',
        //                 'Accept': 'application/json'
        //             },
        //             body: JSON.stringify({
        //                 item_id: itemId,
        //                 item_type: 'contact_lens',
        //                 quantity: quantity
        //             })
        //         });
        //         const data = await response.json();
        //         if (data.success) {
        //             showMessage('success', '{{ __('Product added to cart') }}');
        //             const cartCountElement = document.getElementById('cart-count');
        //             if (cartCountElement) cartCountElement.textContent = data.count;
        //         } else if (data.message) {
        //             showMessage('error', data.message);
        //         }
        //     } catch (error) {
        //         console.error("Cart Error:", error);
        //         showMessage('error', '{{ __('Error adding product to cart') }}');
        //     }

        // }

        function showMessage(type, text) {
            window.dispatchEvent(new CustomEvent('message', {
                detail: {
                    type,
                    text
                }
            }));
        }
    </script>

@endsection
