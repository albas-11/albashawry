@extends('layouts.app')

@section('title', $accessory->getTranslation('name', app()->getLocale()) ?: $accessory->getTranslation('name', 'fr'))

@section('content')
    <div class="bg-white min-h-screen">
        <div class="bg-gray-50 border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
                <nav class="flex items-center space-x-1.5 text-xs text-gray-600">
                    <a href="{{ route('home') }}" class="hover:text-indigo-600 transition">@lang('message.Home')</a>
                    <svg class="w-2.5 h-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span
                        class="text-gray-900 font-medium">{{ $accessory->getTranslation('name', app()->getLocale()) ?: $accessory->getTranslation('name', 'fr') }}</span>
                </nav>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
            <div class="flex flex-col lg:flex-row gap-6 px-4 lg:px-10 py-6 bg-white">

                {{-- LEFT : IMAGES --}}
                <div class="lg:basis-[50%]">
                    <div class="relative bg-[#F9F9F9] rounded-sm border border-gray-100 h-[420px] overflow-hidden">
                        <img id="mainAccessoryImage"
                            src="{{ $accessory->getPrimaryImagePath()
                                ? asset($accessory->getPrimaryImagePath())
                                : asset('images/default-product.jpg') }}"
                            class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300">
                    </div>

                    @if ($accessory->images->count() > 1)
                        <div class="flex justify-center gap-2 mt-3">
                            @foreach ($accessory->images as $index => $image)
                                <button onclick="setImage({{ $index + 1 }})"
                                    class="thumb border border-gray-200 rounded-sm overflow-hidden w-16 h-16 hover:border-[#212482] transition">
                                    <img src="{{ asset($image->image) }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- RIGHT : INFOS --}}
                <div class="lg:basis-[50%]">
                    <div class="pb-2 border-b border-gray-200 space-y-1">

                        <h1 class="text-xl font-bold text-gray-900 leading-snug">
                            {{ $accessory->getTranslation('name', app()->getLocale()) ?: $accessory->getTranslation('name', 'fr') }}
                        </h1>

                        <div class="flex items-center justify-between mt-1">
                            <div class="flex items-center gap-1 text-[11px]">
                                <div class="flex items-center text-yellow-500">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i
                                            class="fas fa-star {{ $i <= floor($accessory->rating ?? 0) ? '' : 'text-gray-300' }} w-3 h-3"></i>
                                    @endfor
                                </div>
                                <span class="text-gray-500">({{ $accessory->total_reviews }} @lang('message.Reviews'))</span>
                            </div>

                            <div
                                class="text-[9px] font-bold uppercase {{ $accessory->stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                                {{ $accessory->stock > 0 ? '● In Stock' : '● Out of Stock' }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xl font-bold text-[#2E3192]">
                                    {{ number_format($accessory->price, 2) }} <span class="text-xs font-normal">SAR</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">@lang('message.Description')
                        </h3>
                        <div class="text-gray-600 leading-relaxed text-xs">
                            {!! $accessory->getTranslation('description', app()->getLocale()) ?:
                                $accessory->getTranslation('description', 'fr') !!}
                        </div>
                    </div>

                    <div class="mt-auto pt-6">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center border border-gray-200 rounded-sm bg-white h-11">
                                <button onclick="updateQuantity(-1)"
                                    class="px-3 text-gray-400 hover:text-black hover:bg-gray-50 h-full transition">-</button>
                                <input type="text" id="quantity" value="1" readonly
                                    class="w-10 text-center text-sm border-none focus:ring-0 font-semibold">
                                <button onclick="updateQuantity(1)"
                                    class="px-3 text-gray-400 hover:text-black hover:bg-gray-50 h-full transition">+</button>
                            </div>

                            <button
                                onclick="addToCart({{ $accessory->id }}, 'accessory', parseInt(document.getElementById('quantity').value))"
                                class="flex-1 bg-[#212482] text-white h-11 rounded-sm text-xs font-bold hover:bg-black transition-all flex items-center justify-center gap-2 {{ $accessory->stock <= 0 ? 'opacity-40 cursor-not-allowed' : '' }}"
                                {{ $accessory->stock <= 0 ? 'disabled' : '' }}>
                                <i class="fas fa-shopping-bag text-[14px]"></i> @lang('message.ADD TO CART')
                            </button>
                        </div>
                    </div>
                </div>

            </div>
            {{-- PRODUCT REVIEWS SECTION --}}
            <div x-data="{ showReviewModal: false }"
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-t border-gray-100 mt-12">

                {{-- Header Reviews --}}
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-6">
                    <div>
                        <h2 class="text-3xl text-gray-900 tracking-tight">@lang('message.Customer Reviews')</h2>
                        <p class="text-sm text-gray-400 mt-1 uppercase tracking-widest">@lang('message.Feedback from our community')</p>
                    </div>

                    {{-- Bouton Write Review --}}
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

                {{-- Liste des Reviews --}}
                @if ($accessory->reviews && $accessory->reviews->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($accessory->reviews as $review)
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
                                <p class="text-sm text-gray-500 leading-relaxed">{{ $review->comment }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-20 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                        <p class="text-gray-400 text-sm tracking-wide">@lang('message.No reviews yet. Be the first to share your experience!')</p>
                    </div>
                @endif

                {{-- Modal de Review --}}
                <div x-show="showReviewModal" x-transition.opacity x-cloak
                    class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/30 backdrop-blur-sm">
                    <div class="bg-white w-full max-w-md overflow-hidden rounded-2xl shadow-xl"
                        @click.away="showReviewModal = false">
                        <div class="px-8 pt-8 pb-4 flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">@lang('message.Share your opinion')</h3>
                            <button @click="showReviewModal = false"
                                class="text-gray-400 hover:text-gray-600">&times;</button>
                        </div>
                        <form action="{{ route('reviews.store') }}" method="POST" class="p-8 pt-4 space-y-6">
                            @csrf
                            <input type="hidden" name="reviewable_id" value="{{ $accessory->id }}">
                            <input type="hidden" name="reviewable_type" value="accessory">

                            <div>
                                <label for="title"
                                    class="block text-xs font-bold text-gray-600">@lang('message.Title')</label>
                                <input type="text" name="title" id="title"
                                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            </div>

                            <div>
                                <label for="rating"
                                    class="block text-xs font-bold text-gray-600">@lang('message.Rating')</label>
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
                                <label for="comment"
                                    class="block text-xs font-bold text-gray-600">@lang('message.Comment')</label>
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

            </div>
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
                    @foreach ($relatedProducts as $accessory)
                        <div class="transition-transform duration-500 hover:-translate-y-1">
                            @include('components.accessory-card', ['accessory' => $accessory])
                        </div>
                    @endforeach
                </div>
            </div>


        </div>
    </div>

    <script>
        let currentIndex = 1;
        const images = @json($accessory->images->pluck('image'));

        function setImage(index) {
            currentIndex = index;
            const mainImage = document.getElementById('mainAccessoryImage');
            mainImage.classList.add('opacity-0');
            setTimeout(() => {
                mainImage.src = "{{ asset('') }}" + images[index - 1];
                mainImage.classList.remove('opacity-0');
            }, 150);
        }

        function prevImage() {
            if (currentIndex > 1) setImage(currentIndex - 1);
        }

        function nextImage() {
            if (currentIndex < images.length) setImage(currentIndex + 1);
        }

        function updateQuantity(change) {
            const quantityInput = document.getElementById('quantity');
            let quantity = parseInt(quantityInput.value);
            quantity += change;
            if (quantity < 1) quantity = 1;
            quantityInput.value = quantity;
        }

        async function addToCart(itemId, type, quantity) {
            try {
                const response = await fetch('{{ route('cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        item_id: itemId,
                        item_type: type,
                        quantity: quantity
                    })
                });
                const data = await response.json();
                if (data.success) alert('Added to cart!');
            } catch (error) {
                console.error(error);
                alert('Error adding to cart');
            }
        }
    </script>
@endsection
