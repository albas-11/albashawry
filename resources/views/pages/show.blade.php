@extends('layouts.app')

@php
    $seoTitle = $page->metaTitle();
    $seoDescription = $page->metaDescription();
    $seoCanonical = $page->canonicalUrl();
    $seoSiteName = $globalSettings->site_name ?? config('app.name');
@endphp

@section('title', $seoTitle)
@section('meta_description', $seoDescription)
@section('canonical', $seoCanonical)

@if ($page->seo_noindex || !$page->is_active)
    @section('robots', 'noindex, follow')
@endif

@section('meta')
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $seoSiteName }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $seoCanonical }}">
    <meta property="og:locale" content="{{ app()->getLocale() === 'ar' ? 'ar_SA' : 'en_US' }}">

    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">

    <script type="application/ld+json">
        {!! json_encode(
            [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => (string) $page->title,
                'description' => $seoDescription,
                'url' => $seoCanonical,
                'inLanguage' => app()->getLocale(),
            ],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        ) !!}
    </script>
@endsection

@section('content')
    <div class="bg-white py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">

            @if (!$page->is_active)
                {{-- Styles inline : ne dépend pas d'une recompilation de Tailwind --}}
                <div
                    style="margin-bottom:1.5rem;border:1px solid #fcd34d;background:#fffbeb;color:#92400e;padding:.75rem 1rem;border-radius:.5rem;font-size:.875rem;">
                    <i class="fas fa-eye-slash"></i>
                    @lang('message.This page is hidden from visitors. You are seeing it because you are an administrator.')
                </div>
            @endif

            <h1 class="text-3xl font-bold text-gray-900 mb-8 border-b pb-4">
                {{ $page->title }}
            </h1>

            <div class="page-content text-gray-700 leading-relaxed">
                {!! $page->content !!}
            </div>

        </div>
    </div>
@endsection

@section('styles')
    <style>
        .page-content {
            font-size: 1rem;
            line-height: 1.9;
        }

        .page-content h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2E3192;
            margin-top: 2rem;
            margin-bottom: .75rem;
            padding-inline-start: .75rem;
            border-inline-start: 4px solid #2E3192;
        }

        .page-content h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
            margin-top: 1.5rem;
            margin-bottom: .5rem;
        }

        .page-content p {
            margin-bottom: 1rem;
        }

        .page-content ul,
        .page-content ol {
            margin: 0 0 1.25rem;
            padding-inline-start: 1.5rem;
        }

        .page-content ul {
            list-style: disc;
        }

        .page-content ol {
            list-style: decimal;
        }

        .page-content li {
            margin-bottom: .5rem;
        }

        .page-content a {
            color: #9E0168;
            text-decoration: underline;
        }

        .page-content blockquote {
            margin: 1.25rem 0;
            padding: .75rem 1rem;
            background: #f7f7fb;
            border-inline-start: 4px solid #9E0168;
        }

        .page-content table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.25rem;
        }

        .page-content th,
        .page-content td {
            border: 1px solid #e5e7eb;
            padding: .5rem .75rem;
            text-align: start;
        }

        .page-content hr {
            margin: 1.5rem 0;
            border-top: 1px solid #e5e7eb;
        }
    </style>
@endsection
