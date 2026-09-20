@extends('layouts.app')

@section('title', __('My Wishlist'))

@section('content')
<div class="bg-white min-h-screen py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- HEADER --}}
        <div class="flex items-baseline justify-between border-b border-gray-100 pb-8 mb-12">
            <div>
                <h1 class="text-3xl font-light text-gray-900 tracking-tight">
                    @lang('message.My Wishlist')
                </h1>

            </div>

            @if ($wishlists->count() > 0)
                <button onclick="clearWishlist()"
                    class="text-xs text-gray-400 hover:text-red-500 transition-colors uppercase tracking-widest flex items-center gap-2">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    @lang('message.Clear all')
                </button>
            @endif
        </div>

        {{-- LISTE DES ITEMS --}}
        @if ($wishlists->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-12">
                @foreach ($wishlists as $wishlist)
                    <div class="relative">
                        @if ($wishlist->item)
                            @switch($wishlist->item_type)
                                @case('product')
                                    @include('components.product-card', ['product' => $wishlist->item])
                                    @break
                                @case('contact_lens')
                                    @include('components.contact-lens-card', ['lens' => $wishlist->item])
                                    @break
                                @case('accessory')
                                    @include('components.accessory-card', ['accessory' => $wishlist->item])
                                    @break
                                @default
                                    <p class="text-gray-400 text-sm">@lang('message.Product not available')</p>
                            @endswitch
                        @else
                            <p class="text-gray-400 text-sm">@lang('message.Product not available')</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            {{-- WISHLIST VIDE --}}
            <div class="py-20 text-center">
                <div class="mb-6 inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 text-gray-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">@lang('message.Your wishlist is empty')</h3>
                <p class="text-gray-500 text-sm mb-8 max-w-sm mx-auto">
                    @lang('message.Save your favorite products here to find them later.')
                </p>

                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('products.eyeglasses') }}"
                        class="text-xs font-bold uppercase tracking-widest px-8 py-4 bg-gray-900 text-white hover:bg-black transition-all">
                        @lang('message.Shop eyeglasses')
                    </a>
                    <a href="{{ route('products.sunglasses') }}"
                        class="text-xs font-bold uppercase tracking-widest px-8 py-4 border border-gray-200 text-gray-900 hover:bg-gray-50 transition-all">
                        @lang('message.Shop sunglasses')
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>

{{-- JS Clear Wishlist --}}
<script>
async function clearWishlist() {
    if (!confirm('{{ __('Clear your wishlist?') }}')) return;
    try {
        const response = await fetch('{{ route('wishlist.clear') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        if (data.success) {
            location.reload();
        }
    } catch (error) {
        console.error('Error:', error);
    }
}
</script>
@endsection

