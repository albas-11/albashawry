@extends('layouts.app')

@section('title', __('message.find_store'))

@section('content')
    <div class="bg-white min-h-screen text-slate-800">
        <div class="border-b border-gray-100">
            <div class="container mx-auto px-6 py-16">
                <div class="max-w-4xl">
                    <nav class="flex mb-6 text-[10px] font-medium uppercase tracking-[0.2em] text-gray-400">
                        <span class="hover:text-black transition-colors cursor-pointer">
                            @lang('message.Home')
                        </span>
                        <span class="mx-3">/</span>
                        <span class="text-black">@lang('message.find_store')</span>
                    </nav>

                    <h1 class="text-5xl text-[#2E3192] tracking-tight mb-4">
                        @lang('message.our_stores')
                    </h1>
                    <p class="text-gray-500 text-lg leading-relaxed">
                        @lang('message.store_description')
                    </p>
                </div>
            </div>
        </div>

        <div class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-gray-100">
            <div class="container mx-auto px-6 py-4">
                <form method="GET" action="{{ route('stores.index') }}"
                    class="flex flex-col md:flex-row gap-4 items-center">
                    <div class="relative w-full md:w-72">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="@lang('message.search_store')"
                            class="w-full pl-0 pr-4 py-2 bg-transparent border-b border-gray-200 focus:border-black focus:ring-0 text-sm outline-none">
                    </div>
                    <select name="city"
                        class="w-full md:w-48 bg-transparent border-b border-gray-200 focus:border-black focus:ring-0 text-sm py-2 px-0 cursor-pointer appearance-none">
                        <option value="">@lang('message.all_cities')</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city }}" @selected(request('city') == $city)>
                                {{ $city }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="group relative overflow-hidden w-full md:w-auto px-10 py-3 border border-[#2E3192] bg-[#2E3192] text-white text-[11px] font-bold uppercase tracking-[0.2em] transition-all duration-300 hover:shadow-xl hover:shadow-indigo-100 active:scale-95">
                        <span
                            class="absolute inset-0 w-full h-full bg-white/10 transition-transform duration-500 -translate-x-full group-hover:translate-x-0"></span>
                        <span class="relative flex items-center justify-center gap-2">
                            <i class="bi bi-sliders2 "></i>
                            @lang('message.filter')
                        </span>
                    </button>
                </form>
            </div>
        </div>

        <div class="container mx-auto px-6 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

                <div class="lg:col-span-4 space-y-8 h-[600px] overflow-y-auto pr-4 custom-scrollbar">
                    @forelse($stores as $store)
                        <div class="group cursor-pointer store-item border-l-2 border-transparent pl-6 hover:border-black transition-all"
                            data-lat="{{ $store->latitude }}" data-lng="{{ $store->longitude }}">

                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                    @lang('message.destination')
                                </span>
                                <span class="w-2 h-2 rounded-full bg-green-400"></span>
                            </div>

                            <h2 class="text-xl font-medium text-black group-hover:translate-x-1 transition-transform">
                                {{ $store->name }}
                            </h2>

                            <div class="mt-3 space-y-1">
                                <p class="text-sm text-gray-500">
                                    {{ $store->address }}, {{ $store->city }}
                                </p>
                                <p class="flex items-center gap-2 text-xs text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                        fill="currentColor" class="bi bi-telephone shrink-0" viewBox="0 0 16 16">
                                        <path
                                            d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58z" />
                                    </svg>

                                    <a href="tel:{{ $store->phone }}" class="tele">{{ $store->phone }}</a>
                                </p>

                            </div>

                            <div
                                class="mt-4 flex items-center text-[10px] font-bold uppercase tracking-[0.2em] text-black opacity-0 group-hover:opacity-100 transition-all">
                                @lang('message.discover')
                                <i class="bi bi-arrow-right ms-2"></i>
                            </div>
                        </div>
                    @empty
                        <div class="py-20 text-center border border-dashed border-gray-200 rounded-lg">
                            <p class="text-gray-400 italic">
                                @lang('message.no_store_found')
                            </p>
                        </div>
                    @endforelse
                </div>

                <div class="lg:col-span-8 relative h-[500px] lg:h-[650px] grayscale-[0.2] hover:grayscale-0 transition-all">
                    <div id="map" class="w-full h-full rounded-sm"></div>
                </div>
            </div>
        </div>
    </div>



    <style>
        .tele {
            text-decoration: none;
            color: #2E3192;
        }

        /* Scrollbar minimaliste */
        .custom-scrollbar::-webkit-scrollbar {
            width: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #eee;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #000;
        }

        .gm-style-iw {
            border-radius: 0px !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .gm-style-iw-d {
            overflow: hidden !important;
            padding: 20px !important;
        }
    </style>
@endsection

@section('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDIO7S2040XkDd2WI8xVjwcmmXhGVzT14A"></script>
    <script>
        let map;
        let markers = [];
        const stores = @json($storesForMap);

        function initMap() {
            // Style de carte "Silver" ou "Minimal" pour l'élégance
            const mapStyle = [{
                    "featureType": "water",
                    "elementType": "geometry",
                    "stylers": [{
                        "color": "#e9e9e9"
                    }, {
                        "lightness": 17
                    }]
                },
                {
                    "featureType": "landscape",
                    "elementType": "geometry",
                    "stylers": [{
                        "color": "#f5f5f5"
                    }, {
                        "lightness": 20
                    }]
                },
                {
                    "featureType": "road.highway",
                    "elementType": "geometry.fill",
                    "stylers": [{
                        "color": "#ffffff"
                    }, {
                        "lightness": 17
                    }]
                }
            ];

            map = new google.maps.Map(document.getElementById("map"), {
                center: {
                    lat: stores[0]?.latitude || 24.7136,
                    lng: stores[0]?.longitude || 46.6753
                },
                zoom: 6,
                styles: mapStyle,
                disableDefaultUI: false,
                zoomControl: true,
                mapTypeControl: false,
                streetViewControl: false
            });

            const infoWindow = new google.maps.InfoWindow();

            stores.forEach(store => {
                const marker = new google.maps.Marker({
                    position: {
                        lat: parseFloat(store.latitude),
                        lng: parseFloat(store.longitude)
                    },
                    map: map,
                    title: store.name,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        fillColor: '#2563eb',
                        fillOpacity: 1,
                        strokeColor: '#ffffff',
                        strokeWeight: 2,
                        scale: 8
                    }
                });

                marker.addListener('click', () => {
                    const content = `
                    <div style="padding:10px; font-family: sans-serif;">
                        <h5 style="margin:0 0 5px; font-weight:bold; color:#1e293b;">${store.name}</h5>
                        <p style="margin:0; font-size:12px; color:#64748b;">${store.address}</p>
                        <p style="margin:5px 0 0; font-size:12px; font-weight:600; color:#2563eb;">${store.phone}</p>
                    </div>
                `;
                    infoWindow.setContent(content);
                    infoWindow.open(map, marker);
                });
            });

            document.querySelectorAll('.store-item').forEach(item => {
                item.addEventListener('click', function() {
                    const lat = parseFloat(this.dataset.lat);
                    const lng = parseFloat(this.dataset.lng);

                    // Effet visuel sur la liste
                    document.querySelectorAll('.store-item').forEach(el => el.classList.remove(
                        'border-blue-500', 'bg-blue-50'));
                    this.classList.add('border-blue-500', 'bg-blue-50');

                    map.panTo({
                        lat,
                        lng
                    });
                    map.setZoom(14);
                });
            });
        }

        window.onload = initMap;
    </script>
@endsection
