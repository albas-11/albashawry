@extends('layouts.app')

@section('title', __('Accessories'))

@section('content')
    <div class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

            {{-- 1. ENTÊTE : TITRE + RECHERCHE + BOUTON FILTRE --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-8">
                <div>
                    <h1 class="text-4xl font-light tracking-tight text-gray-900 uppercase">
                        @lang('message.Accessories')
                    </h1>
                    <p class="mt-2 text-sm text-gray-500 font-medium tracking-wide uppercase">
                        @lang('message.Discover our Accessories collection')
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <form method="GET" action="{{ route('products.accessories') }}" class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="@lang('message.Search')..."
                            class="bg-gray-50 border-none rounded-full py-2.5 pl-5 pr-12 w-64 focus:ring-2 focus:ring-gray-200 transition-all text-sm">
                        <button type="submit"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 group-hover:text-black">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>

                    <button id="toggleFilters"
                        class="flex items-center gap-2 px-5 py-2.5 bg-black text-white rounded-full hover:bg-gray-800 transition-all text-sm font-medium shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-sliders" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3zM4.5 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M2.05 8a2.5 2.5 0 0 1 4.9 0H16v1H6.95a2.5 2.5 0 0 1-4.9 0H0V8zm9.5 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3m-2.45 1a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0v-1z" />
                        </svg>
                        @lang('message.Filters')
                    </button>
                </div>
            </div>

            {{-- 3. BADGES DE FILTRES ACTIFS --}}
            @if (request()->hasAny(['search', 'type', 'min_price', 'max_price']))
                <div class="flex flex-wrap gap-2 mb-8 items-center">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mr-2">
                        @lang('message.Active'):</span>

                    @foreach (['search', 'type'] as $key)
                        @if (request($key))
                            <span
                                class="inline-flex items-center gap-2 px-3 py-1 bg-gray-50 text-gray-700 rounded-full text-[10px] font-bold uppercase border border-gray-200">
                                {{ $key }}: {{ request($key) }}
                                <a href="{{ request()->fullUrlWithQuery([$key => null]) }}"
                                    class="hover:text-red-500 transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            </span>
                        @endif
                    @endforeach

                    <a href="{{ route('products.accessories') }}"
                        class="text-[10px] font-bold text-gray-400 uppercase underline ml-2 hover:text-black">
                        @lang('message.Reset All')
                    </a>
                </div>
            @endif

            <div class="mb-6 border-b border-gray-100 pb-4">
                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-[0.2em]">
                    {{ $accessories->total() }} @lang('message.products')
                </p>
            </div>

            {{-- 4. GRILLE DE PRODUITS --}}
            @if ($accessories->count())
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-y-12 gap-x-6">
                    @foreach ($accessories as $accessory)
                        @include('components.accessory-card', ['accessory' => $accessory])
                    @endforeach
                </div>

                <div class="mt-20">
                    {{ $accessories->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-32 bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                    <p class="text-gray-400 text-sm uppercase tracking-widest font-medium italic">
                        @lang('message.No products found').
                    </p>
                    <a href="{{ route('products.accessories') }}"
                        class="mt-4 inline-block text-[10px] font-bold uppercase tracking-widest underline hover:text-black">
                        @lang('message.View All')
                    </a>
                </div>
            @endif

        </div>
    </div>

    {{-- 5. SIDEBAR DE FILTRAGE --}}
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/20 backdrop-blur-sm z-50 hidden transition-opacity"></div>
    <div id="filterSidebar"
        class="fixed top-0 right-0 h-full w-full max-w-sm bg-white shadow-2xl z-[60] transform translate-x-full transition-transform duration-500 flex flex-col">

        <div class="p-8 pb-4 flex items-center justify-between border-b border-gray-50">
            <h2 class="text-xl font-light uppercase tracking-widest">@lang('message.Filters')</h2>
            <button id="closeSidebar" class="p-2 hover:rotate-90 transition-transform focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="GET" action="{{ route('products.accessories') }}" class="flex-1 flex flex-col overflow-hidden">
            <div class="flex-1 overflow-y-auto p-8 space-y-10 no-scrollbar">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">
                        @lang('message.Type')
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($types as $type)
                            <label class="cursor-pointer group">
                                <input type="radio" name="type" value="{{ $type }}"
                                    {{ request('type') == $type ? 'checked' : '' }} class="hidden">
                                <div
                                    class="py-2 text-center border rounded-full text-[10px] font-bold uppercase tracking-widest transition-all
                                    {{ request('type') == $type ? 'bg-black text-white border-black shadow-md' : 'bg-white text-gray-400 border-gray-200 group-hover:border-gray-400' }}">
                                    {{ $type }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Filtre Prix --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">
                        @lang('message.Price Range') (SAR)
                    </label>
                    <div class="flex items-center gap-4">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min"
                            class="w-1/2 border-b border-gray-200 focus:border-black py-2 border-t-0 border-l-0 border-r-0 focus:ring-0 text-sm bg-transparent">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max"
                            class="w-1/2 border-b border-gray-200 focus:border-black py-2 border-t-0 border-l-0 border-r-0 focus:ring-0 text-sm bg-transparent">
                    </div>
                </div>
            </div>

            <div class="p-8 border-t border-gray-50 bg-white">
                <button type="submit"
                    class="w-full py-4 bg-black text-white text-xs font-bold uppercase tracking-widest hover:bg-gray-800 transition-colors shadow-lg">
                    @lang('message.Apply Filters')
                </button>
                <button type="button" onclick="window.location='{{ route('products.accessories') }}'"
                    class="w-full mt-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest hover:text-black">
                    @lang('message.Reset All')
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggleFilters');
            const sidebar = document.getElementById('filterSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const closeBtn = document.getElementById('closeSidebar');

            const open = () => {
                sidebar.classList.remove('translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            };
            const close = () => {
                sidebar.classList.add('translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            };

            toggleBtn.addEventListener('click', open);
            closeBtn.addEventListener('click', close);
            overlay.addEventListener('click', close);
        });
    </script>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endsection
