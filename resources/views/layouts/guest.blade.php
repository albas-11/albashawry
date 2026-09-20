<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AL Bashawri') }}</title>

        {{-- Pages d'authentification : jamais indexées --}}
        @if (robots_directive())
            <meta name="robots" content="{{ robots_directive() }}">
        @endif

        <link rel="icon"
            href="{{ isset($globalSettings) && $globalSettings && $globalSettings->favicon
                ? asset('images/settings/' . $globalSettings->favicon)
                : asset('images/default-favicon.ico') }}"
            type="image/x-icon">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body, h1, h2, h3, h4, h5, h6, p, span, a, li, button, input, select, textarea, label, div {
                font-family: 'Cairo', sans-serif !important;
            }
        </style>
    </head>
    <body class="antialiased text-gray-900" style="background-color: #f5f5f5;">
        <div class="min-h-screen flex flex-col justify-center items-center px-4 py-10"
             style="background: linear-gradient(135deg, #920a69 0%, #2E3192 100%);">

            <a href="{{ url('/') }}" class="mb-6">
                @php
                    $savedLogo = isset($globalSettings) && $globalSettings && $globalSettings->logo
                        ? public_path('images/settings/' . $globalSettings->logo)
                        : null;
                    $logoSrc = $savedLogo && file_exists($savedLogo)
                        ? asset('images/settings/' . $globalSettings->logo)
                        : asset('images/logo.png');
                @endphp
                <img src="{{ $logoSrc }}" alt="{{ config('app.name', 'AL Bashawri') }}"
                     class="h-14 w-auto bg-white/95 rounded-xl px-4 py-2 shadow-lg">
            </a>

            <div class="w-full sm:max-w-md bg-white shadow-2xl rounded-2xl px-6 py-8 sm:px-8">
                {{ $slot }}
            </div>

            <p class="mt-6 text-xs text-white/80">
                &copy; {{ date('Y') }} {{ config('app.name', 'AL Bashawri') }}
            </p>
        </div>
    </body>
</html>
