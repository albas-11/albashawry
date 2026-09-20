@extends('layouts.admin')

@section('title', __('admin.Site Pages'))

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-semibold mb-1" style="font-size:1.4rem;">@lang('admin.Site Pages')</h1>
            <p class="text-muted small mb-0">@lang('admin.Edit the content of the static pages of your website.')</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        @foreach ($pages as $page)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                            style="width:44px;height:44px;background:#eef0ff;">
                            <i class="bi {{ ['about' => 'bi-info-circle', 'policy' => 'bi-arrow-repeat', 'terms' => 'bi-file-earmark-text'][$page->slug] ?? 'bi-file-text' }} fs-5"
                                style="color:#2E3192;"></i>
                        </div>

                        @if ($page->is_active)
                            <span class="badge bg-success-subtle text-success">@lang('admin.Visible')</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary">@lang('admin.Hidden')</span>
                        @endif
                    </div>

                    <h6 class="fw-bold mb-1">{{ $page->getTranslation('title', app()->getLocale(), true) }}</h6>
                    <p class="text-muted small mb-2" dir="ltr">{{ $page->public_url }}</p>

                    @if ($page->seo_noindex)
                        <p class="small text-warning mb-2">
                            <i class="bi bi-eye-slash"></i> @lang('admin.Excluded from search engines')
                        </p>
                    @endif

                    <p class="text-muted small mb-4">
                        @lang('admin.Last updated'): {{ $page->updated_at?->format('Y-m-d H:i') }}
                    </p>

                    <div class="mt-auto d-flex gap-2">
                        <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm text-white flex-fill"
                            style="background-color:#2E3192;">
                            <i class="bi bi-pencil"></i> @lang('admin.Edit')
                        </a>
                        <a href="{{ $page->public_url }}" target="_blank" rel="noopener"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

@endsection
