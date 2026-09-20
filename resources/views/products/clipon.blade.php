@extends('layouts.app')

@section('title', __('Clip-Ons'))

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- 1. ENTÊTE : TITRE + RECHERCHE + BOUTON FILTRE --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-8">
            <div>
                <h1 class="text-4xl font-light tracking-tight text-gray-900 uppercase">
                    @lang('message.ClipOns')
                </h1>
                <p class="mt-2 text-sm text-gray-500 font-medium tracking-wide uppercase">
                    @lang('message.Discover our ClipOns collection')
                </p>
            </div>

            <div class="flex items-center gap-3">
                {{-- Barre de recherche --}}
                <form method="GET" action="{{ route('products.clipon') }}" class="relative group">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="@lang('message.Search')..."
                        class="bg-gray-50 border-none rounded-full py-2.5 pl-5 pr-12 w-64 focus:ring-2 focus:ring-gray-200 transition-all text-sm">
                    <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 group-hover:text-black">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>

                {{-- Bouton déclencheur Sidebar --}}
                <button id="toggleFilters"
                    class="flex items-center gap-2 px-5 py-2.5 bg-black text-white rounded-full hover:bg-gray-800 transition-all text-sm font-medium shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sliders" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3zM4.5 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M2.05 8a2.5 2.5 0 0 1 4.9 0H16v1H6.95a2.5 2.5 0 0 1-4.9 0H0V8zm9.5 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3m-2.45 1a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0v-1z" />
                    </svg>
                    @lang('message.Filters')
                </button>
            </div>
        </div>

        {{-- 2. NAVIGATION HORIZONTALE (BULLLES MARQUES) --}}
        <div class="flex gap-4 overflow-x-auto pb-6 mb-8 no-scrollbar scroll-smooth">
            {{-- Bouton "Tout" --}}
            <a href="{{ request()->fullUrlWithQuery(['brand' => null]) }}"
                class="flex items-center px-6 py-2 rounded-full border transition-all whitespace-nowrap text-[11px] font-bold uppercase tracking-widest
                {{ !request('brand') ? 'border-black bg-black text-white' : 'border-gray-200 hover:border-gray-400 bg-white text-gray-500' }}">
                @lang('message.All Brands')
            </a>

            @foreach ($brands as $brand)
                <a href="{{ request()->fullUrlWithQuery(['brand' => $brand->id]) }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-full border transition-all whitespace-nowrap
                    {{ request('brand') == $brand->id ? 'border-black bg-gray-50': 'border-gray-200 hover:border-gray-400 bg-white' }}">

                    <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-100 bg-gray-50 flex-shrink-0">
                        @if ($brand->logo)
                            <img src="{{ asset($brand->logo) }}" class="w-full h-full object-cover" alt="{{ $brand->name }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-100 text-[10px] text-gray-400 uppercase">
                                {{ substr($brand->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <span class="text-[11px] font-bold uppercase tracking-widest {{ request('brand') == $brand->id ? 'text-black' : 'text-gray-800' }} pr-2">
                        {{ $brand->name }}
                    </span>
                </a>
            @endforeach
        </div>

        {{-- 3. BADGES DE FILTRES ACTIFS --}}
        @if (request()->hasAny(['search', 'brand', 'gender']))
            <div class="flex flex-wrap gap-2 mb-8 items-center">
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mr-2">@lang('message.Active'):</span>
                @foreach (['search', 'brand', 'gender'] as $key)
                    @if (request($key))
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-gray-50 text-gray-700 rounded-full text-[10px] font-bold uppercase border border-gray-200">
                            {{ $key }}: {{ $key == 'brand' ? ($brands->firstWhere('id', request($key))->name ?? request($key)) : request($key) }}
                            <a href="{{ request()->fullUrlWithQuery([$key => null]) }}" class="hover:text-red-500 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>
                            </a>
                        </span>
                    @endif
                @endforeach
                <a href="{{ route('products.clipon') }}" class="text-[10px] font-bold text-gray-400 uppercase underline ml-2 hover:text-black">@lang('message.Reset All')</a>
            </div>
        @endif

        <div class="mb-6 border-b border-gray-100 pb-4">
            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-[0.2em]">
                {{ $products->total() }} @lang('message.products')
            </p>
        </div>

        {{-- 4. GRILLE DE PRODUITS --}}
        @if ($products->count())
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-y-12 gap-x-6">
                @foreach ($products as $product)
                    @include('components.product-card-enhanced', ['product' => $product])
                @endforeach
            </div>
            <div class="mt-20">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-32 bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                <p class="text-gray-400 text-sm uppercase tracking-widest font-medium">@lang('message.No products found').</p>
                <a href="{{ route('products.clipon') }}" class="mt-4 inline-block text-[10px] font-bold uppercase tracking-widest underline hover:text-black">@lang('message.Reset All Filters')</a>
            </div>
        @endif
    </div>
</div>

{{-- 5. SIDEBAR DE FILTRAGE --}}
<div id="sidebarOverlay" class="fixed inset-0 bg-black/20 backdrop-blur-sm z-50 hidden transition-opacity"></div>
<div id="filterSidebar" class="fixed top-0 right-0 h-full w-full max-w-sm bg-white shadow-2xl z-[60] transform translate-x-full transition-transform duration-500 flex flex-col">

    <div class="p-8 pb-4 flex items-center justify-between border-b border-gray-50">
        <h2 class="text-xl font-light uppercase tracking-widest">@lang('message.Filters')</h2>
        <button id="closeSidebar" class="p-2 hover:rotate-90 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>

    <form method="GET" action="{{ route('products.clipon') }}" class="flex-1 flex flex-col overflow-hidden">
        <div class="flex-1 overflow-y-auto p-8 space-y-10 no-scrollbar">

            {{-- Filtre Marque --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-4">@lang('message.Brand')</label>
                <select name="brand" class="w-full border-b border-gray-200 border-t-0 border-l-0 border-r-0 focus:ring-0 focus:border-black py-2 px-0 transition-colors bg-transparent">
                    <option value="">@lang('message.All Brands')</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtre Genre --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-4">@lang('message.Gender')</label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach(['men', 'women', 'boys', 'girls'] as $g)
                        <label class="cursor-pointer group">
                            <input type="radio" name="gender" value="{{ $g }}" {{ request('gender') == $g ? 'checked' : '' }} class="hidden">
                            <div class="py-2 text-center border rounded-full text-[10px] font-bold uppercase tracking-widest transition-all
                                {{ request('gender') == $g ? 'bg-black text-white border-black' : 'bg-white text-gray-400 border-gray-200 group-hover:border-gray-400' }}">
                                {{ $g }}
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="p-8 border-t border-gray-50 bg-white">
            <button type="submit" class="w-full py-4 bg-black text-white text-xs font-bold uppercase tracking-widest hover:bg-gray-800 transition-colors">
                @lang('message.Apply Filters')
            </button>
            <a href="{{ route('products.clipon') }}" class="block text-center mt-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest hover:text-black">
                @lang('message.Reset All')
            </a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButton = document.getElementById('toggleFilters');
        const sidebar = document.getElementById('filterSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const closeButton = document.getElementById('closeSidebar');

        const openSidebar = () => {
            sidebar.classList.remove('translate-x-full');
            overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        };
        const closeSidebar = () => {
            sidebar.classList.add('translate-x-full');
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
        };

        toggleButton.addEventListener('click', openSidebar);
        closeButton.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);
    });
</script>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection
