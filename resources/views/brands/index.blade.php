@extends('layouts.app')
@section('title', __('Our Brands'))

@section('content')
    <div class="bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-24">

            {{-- En-tête minimaliste --}}
            <div class="mb-24">
                <h1 class="text-sm font-medium tracking-[0.4em]  uppercase mb-4">
                    @lang('message.Our Brands')
                </h1>
                <div class="h-[1px] w-20 bg-black"></div>
            </div>

            {{-- Grille de logos --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-px bg-gray-100 border-[0.5px] border-gray-100">
                @foreach ($brands as $brand)
                    <a href="{{ route('brand', $brand->slug) }}"
                        class="group relative bg-white aspect-[4/3] flex flex-col items-center justify-center overflow-hidden transition-colors duration-500 hover:bg-gray-50">

                        {{-- Logo --}}
                        <div class="relative z-10 p-8 w-full h-full flex items-center justify-center">
                            <img src="{{ asset($brand->logo) }}" alt="{{ $brand->name }}"
                                class="max-h-12 w-auto object-contain  opacity-60 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-110 transition-all duration-700 ease-out">
                        </div>

                        {{-- Label discret apparaissant au survol --}}
                        <div
                            class="absolute inset-0 flex flex-col items-center justify-end pb-6 translate-y-2 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-500">
                            <span class="text-[10px] tracking-[0.2em] font-medium uppercase text-black">
                                {{ $brand->name }}
                            </span>
                        </div>

                    </a>
                @endforeach
            </div>

        </div>
    </div>
@endsection
