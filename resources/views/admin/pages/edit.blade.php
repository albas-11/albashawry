@extends('layouts.admin')

@section('title', __('admin.Edit Page'))

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.pages.index') }}" class="text-decoration-none text-muted">@lang('admin.Site Pages')</a>
                    </li>
                    <li class="breadcrumb-item active">@lang('admin.Edit Mode')</li>
                </ol>
            </nav>
            <h1 class="fw-bold mb-0" style="font-size:1.6rem;">
                {{ $page->getTranslation('title', app()->getLocale(), true) }}
            </h1>
            <p class="text-muted small mb-0" dir="ltr">{{ $page->public_url }}</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.pages.index') }}" class="btn btn-white shadow-sm border-0 px-4 rounded-3">
                <i class="bi bi-x-lg me-2"></i>@lang('admin.Cancel')
            </a>
            <button type="submit" form="edit-page-form" class="btn shadow-sm px-4 rounded-3 text-white"
                style="background-color:#2E3192;">
                <i class="bi bi-check2-circle me-2"></i>@lang('admin.Save Changes')
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.pages.update', $page) }}" method="POST" id="edit-page-form">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-8">

                {{-- ============ TITRES ============ --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3">@lang('admin.Page Content')</h5>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Title') (EN)</label>
                        <input type="text" name="title_en" dir="ltr"
                            class="form-control @error('title_en') is-invalid @enderror"
                            value="{{ old('title_en', $page->getTranslation('title', 'en', false)) }}">
                        @error('title_en')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">@lang('admin.Title') (AR)</label>
                        <input type="text" name="title_ar" dir="rtl"
                            class="form-control @error('title_ar') is-invalid @enderror"
                            value="{{ old('title_ar', $page->getTranslation('title', 'ar', false)) }}">
                        @error('title_ar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ============ CONTENU EN ============ --}}
                    <label class="form-label">@lang('admin.Content') (EN)</label>
                    <x-admin.rich-editor name="content_en" dir="ltr"
                        :value="old('content_en', $page->getTranslation('content', 'en', false))" />
                    @error('content_en')
                        <div class="text-danger small mb-3">{{ $message }}</div>
                    @enderror

                    {{-- ============ CONTENU AR ============ --}}
                    <label class="form-label mt-4">@lang('admin.Content') (AR)</label>
                    <x-admin.rich-editor name="content_ar" dir="rtl"
                        :value="old('content_ar', $page->getTranslation('content', 'ar', false))" />
                    @error('content_ar')
                        <div class="text-danger small mb-3">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ============ SEO ============ --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-graph-up-arrow me-2" style="color:#2E3192;"></i>@lang('admin.SEO Enhancements')
                        </h5>
                        <span class="badge bg-light text-muted fw-normal">@lang('admin.Optional')</span>
                    </div>
                    <p class="text-muted small mb-4">@lang('admin.Leave empty to use the page title and content automatically.')</p>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">@lang('admin.Page Title')</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light"><i class="bi bi-window-sidebar"></i></span>
                            <input type="text" name="seo_title_en" dir="ltr" maxlength="255" class="form-control"
                                placeholder="@lang('admin.Page Title') (EN)"
                                value="{{ old('seo_title_en', $page->getTranslation('seo_title', 'en', false)) }}">
                            <span class="input-group-text bg-light small text-muted">EN</span>
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light"><i class="bi bi-window-sidebar"></i></span>
                            <input type="text" name="seo_title_ar" dir="rtl" maxlength="255" class="form-control"
                                placeholder="@lang('admin.Page Title') (AR)"
                                value="{{ old('seo_title_ar', $page->getTranslation('seo_title', 'ar', false)) }}">
                            <span class="input-group-text bg-light small text-muted">AR</span>
                        </div>
                        <small class="text-muted">@lang('admin.Variables'): <code>{Title}</code> <code>{SiteName}</code></small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">@lang('admin.Page Description')</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light align-items-start pt-2"><i class="bi bi-card-text"></i></span>
                            <textarea name="seo_description_en" dir="ltr" rows="2" maxlength="500" class="form-control"
                                placeholder="@lang('admin.Page Description') (EN)">{{ old('seo_description_en', $page->getTranslation('seo_description', 'en', false)) }}</textarea>
                            <span class="input-group-text bg-light small text-muted align-items-start pt-2">EN</span>
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light align-items-start pt-2"><i class="bi bi-card-text"></i></span>
                            <textarea name="seo_description_ar" dir="rtl" rows="2" maxlength="500" class="form-control"
                                placeholder="@lang('admin.Page Description') (AR)">{{ old('seo_description_ar', $page->getTranslation('seo_description', 'ar', false)) }}</textarea>
                            <span class="input-group-text bg-light small text-muted align-items-start pt-2">AR</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">@lang('admin.Canonical URL')</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-signpost-2"></i></span>
                            <input type="url" name="canonical_url" dir="ltr" maxlength="255"
                                class="form-control @error('canonical_url') is-invalid @enderror"
                                placeholder="{{ $page->public_url }}"
                                value="{{ old('canonical_url', $page->canonical_url) }}">
                            @error('canonical_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="text-muted">@lang('admin.Leave empty to point the canonical link to this page.')</small>
                    </div>
                </div>
            </div>

            {{-- ============ COLONNE DROITE ============ --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h6 class="fw-bold mb-3">@lang('admin.Options')</h6>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                            @checked(old('is_active', $page->is_active))>
                        <label class="form-check-label" for="is_active">@lang('admin.Visible on the website')</label>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="seo_noindex" value="1" id="seo_noindex"
                            @checked(old('seo_noindex', $page->seo_noindex))>
                        <label class="form-check-label" for="seo_noindex">
                            @lang('admin.Hide this page from search engines') <span class="text-muted small">(noindex)</span>
                        </label>
                    </div>

                    <hr>

                    <p class="small text-muted mb-2">@lang('admin.Public URL')</p>
                    <a href="{{ $page->public_url }}" target="_blank" rel="noopener" class="small" dir="ltr">
                        {{ $page->public_url }} <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>

@endsection
