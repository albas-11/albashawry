@extends('layouts.app')
@section('title', __('Contact Lenses'))

@section('content')
    <div class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

            {{-- HEADER SECTION --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-12">
                <div>
                    <h1 class="text-4xl font-light tracking-tight text-gray-900 uppercase">
                        @lang('message.Contact Lenses')
                    </h1>
                    <p class="mt-2 text-sm text-gray-500 font-medium tracking-wide uppercase">
                        @lang('message.Discover our Contact Lenses collection')
                    </p>
                </div>
                
                <div class="flex items-center gap-3">
                    {{-- SEARCH FORM --}}
                    <form method="GET" action="{{ route('products.contact-lenses') }}" class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="@lang('message.Search')..."
                            class="bg-gray-50 border-none rounded-full py-2.5 pl-5 pr-12 w-64 focus:ring-2 focus:ring-gray-200 transition-all placeholder-gray-400 text-sm">
                        <button type="submit"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 group-hover:text-black">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>

                    {{-- FILTER BUTTON --}}
                    <button id="toggleFilters"
                        class="flex items-center gap-2 px-5 py-2.5 bg-black text-white rounded-full hover:bg-gray-800 transition-all text-sm font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-sliders" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3zM4.5 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M2.05 8a2.5 2.5 0 0 1 4.9 0H16v1H6.95a2.5 2.5 0 0 1-4.9 0H0V8zm9.5 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3m-2.45 1a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0v-1z" />
                        </svg>
                        @lang('message.Filters')
                    </button>
                </div>
            </div>

            {{-- HORIZONTAL BRAND  --}}
            <div class="flex gap-4 overflow-x-auto pb-6 mb-8 no-scrollbar">
                <a href="{{ request()->fullUrlWithQuery(['brand' => null]) }}"
                    class="flex items-center px-6 py-2 rounded-full border transition-all whitespace-nowrap text-[11px] font-bold uppercase tracking-widest
                {{ !request('brand') ? 'border-black bg-gray-50': 'border-gray-200 hover:border-gray-400 bg-white text-gray-500' }}">
                    @lang('message.All Brands')
                </a>

                @foreach ($brands as $brand)
                    <a href="{{ request()->fullUrlWithQuery(['brand' => $brand->id]) }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-full border transition-all whitespace-nowrap
            {{ request('brand') == $brand->id ? 'border-black bg-gray-50 ring-1 ring-black' : 'border-gray-200 hover:border-gray-400 bg-white' }}">

                        <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-100 bg-gray-50 flex-shrink-0">
                            @if ($brand->logo)
                                <img src="{{ asset($brand->logo) }}" class="w-full h-full object-cover"
                                    alt="{{ $brand->name }}">
                            @endif
                        </div>
                        <span class="text-sm font-semibold tracking-wide text-gray-800 pr-2">
                            {{ $brand->name }}
                        </span>
                    </a>
                @endforeach
            </div>

            {{-- ACTIVE FILTERS DISPLAY --}}
            @if (request()->hasAny(['search', 'brand', 'color', 'convexity', 'water_content']))
                <div class="flex flex-wrap gap-2 mb-8 items-center">
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-400 mr-2">@lang('message.Active'):</span>
                    @foreach (['search', 'brand', 'color', 'convexity', 'water_content'] as $key)
                        @if (request($key))
                            @php
                                $displayValue = request($key);
                                if ($key === 'brand') {
                                    $displayValue = $brands->firstWhere('id', request('brand'))?->name;
                                }
                                if ($key === 'convexity') {
                                    $displayValue = 'BC ' . $displayValue;
                                }
                                if ($key === 'water_content') {
                                    $displayValue = $displayValue . '%';
                                }
                            @endphp
                            <span
                                class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium border border-gray-200">
                                {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $displayValue }}
                                <a href="{{ request()->fullUrlWithQuery([$key => null]) }}" class="hover:text-red-500">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            </span>
                        @endif
                    @endforeach
                    <a href="{{ route('products.contact-lenses') }}"
                        class="text-xs text-gray-400 underline ml-2">@lang('message.Reset All')</a>
                </div>
            @endif

            <div class="mb-6 border-b border-gray-100 pb-4">
                <p class="text-xs text-gray-400 uppercase tracking-widest">
                    {{ $lenses->total() }} @lang('message.products')
                </p>
            </div>

            {{-- PRODUCTS GRID --}}
            @if ($lenses->count())
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-y-12 gap-x-6">
                    @foreach ($lenses as $lens)
                        @include('components.contact-lens-card', ['lens' => $lens])
                    @endforeach
                </div>
                <div class="mt-20">
                    {{ $lenses->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-32 bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                    <p class="text-gray-400 text-lg italic uppercase tracking-widest">@lang('message.No products found')</p>
                </div>
            @endif
        </div>
    </div>

    {{-- SIDEBAR OVERLAY --}}
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/20 backdrop-blur-sm z-50 hidden transition-opacity"></div>

    {{-- FILTER SIDEBAR --}}
    <div id="filterSidebar"
        class="fixed top-0 right-0 h-full w-full max-w-sm bg-white shadow-2xl z-[60] transform translate-x-full transition-transform duration-500">
        <div class="p-8 h-full flex flex-col">
            <div class="flex items-center justify-between mb-12">
                <h2 class="text-xl font-light uppercase tracking-widest">@lang('message.Filters')</h2>
                <button id="closeSidebar" class="p-2 hover:rotate-90 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="GET" action="{{ route('products.contact-lenses') }}"
                class="flex-1 space-y-10 overflow-y-auto pr-2">
                {{-- BRAND --}}
                <div>
                    <label
                        class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-4">@lang('message.Brand')</label>
                    <select name="brand"
                        class="w-full border-b border-gray-200 border-t-0 border-l-0 border-r-0 focus:ring-0 focus:border-black py-2 px-0 transition-colors">
                        <option value="">@lang('message.All Brands')</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- COLOR --}}
                <div>
                    <label
                        class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-4">@lang('message.Color')</label>
                    <input type="text" name="color" value="{{ request('color') }}" placeholder="Blue, Brown..."
                        class="w-full border-b border-gray-200 border-t-0 border-l-0 border-r-0 focus:ring-0 focus:border-black py-2 px-0 transition-colors">
                </div>

                {{-- BASE CURVE --}}
                <div>
                    <label
                        class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-4">@lang('message.Base Curve')</label>
                    <select name="convexity"
                        class="w-full border-b border-gray-200 border-t-0 border-l-0 border-r-0 focus:ring-0 focus:border-black py-2 px-0">
                        <option value="">@lang('message.All')</option>
                        @foreach (['8.5', '8.6', '8.7'] as $bc)
                            <option value="{{ $bc }}" {{ request('convexity') == $bc ? 'selected' : '' }}>
                                {{ $bc }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- WATER CONTENT --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-4">@lang('message.Water Content')
                        (%)</label>
                    <input type="number" name="water_content" value="{{ request('water_content') }}" placeholder="e.g. 38"
                        class="w-full border-b border-gray-200 border-t-0 border-l-0 border-r-0 focus:ring-0 focus:border-black py-2 px-0 transition-colors">
                </div>

                {{-- BUTTONS --}}
                <div class="pt-10 flex flex-col gap-3">
                    <button type="submit"
                        class="w-full py-4 bg-black text-white text-xs font-bold uppercase tracking-widest hover:bg-gray-800 transition-colors">
                        @lang('message.Apply Filters')
                    </button>
                    <a href="{{ route('products.contact-lenses') }}"
                        class="text-center py-2 text-xs text-gray-400 uppercase tracking-widest hover:text-black transition-colors">
                        @lang('message.Reset All')
                    </a>
                </div>
            </form>
        </div>
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

            toggleButton.onclick = openSidebar;
            closeButton.onclick = closeSidebar;
            overlay.onclick = closeSidebar;
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
