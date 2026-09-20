@extends('layouts.app')

@section('title', $brand->name)

@section('content')
<div class="bg-white">
    <div class="relative w-full overflow-hidden group">
        @if ($brand->banner_image)
            <div class="h-[300px] md:h-[400px] w-full bg-cover bg-center transition-transform duration-1000 group-hover:scale-105"
                 style="background-image: url('{{ asset($brand->banner_image) }}');">
            </div>
        @else
            <div class="h-[200px] md:h-[250px] w-full bg-gray-100 flex items-center justify-center">
                <span class="text-4xl font-light uppercase tracking-[0.3em] text-gray-300">{{ $brand->name }}</span>
            </div>
        @endif

        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="relative -mt-16 mb-16 flex flex-col items-center">
            <div class="w-32 h-32 bg-white rounded-full p-4 shadow-xl flex items-center justify-center border border-gray-50">
                <img src="{{ asset($brand->logo) }}"
                     alt="{{ $brand->name }}"
                     class="max-h-full max-w-full object-contain">
            </div>

            <div class="text-center mt-6">
                <h1 class="text-4xl font-light tracking-tight text-gray-900 uppercase">
                    {{ $brand->name }}
                </h1>
                <div class="mt-2 flex items-center justify-center gap-4">
                    <span class="h-[1px] w-8 bg-gray-200"></span>
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                        {{ $products->total() }} @lang('message.products')
                    </span>
                    <span class="h-[1px] w-8 bg-gray-200"></span>
                </div>
            </div>
        </div>

        @if ($products->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-y-12 gap-x-6 pb-20">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            <div class="pb-20">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-20 bg-gray-50 rounded-3xl border border-dashed border-gray-200 mb-20">
                <p class="text-gray-400 text-lg italic uppercase tracking-widest">
                    Aucun produit disponible pour cette marque.
                </p>
                <a href="{{ route('brands.index') }}" class="mt-6 inline-block text-[10px] font-bold uppercase tracking-[0.2em] underline">
                    Retour aux marques
                </a>
            </div>
        @endif

    </div>
</div>
@endsection
