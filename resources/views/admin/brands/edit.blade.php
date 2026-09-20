@extends('layouts.admin')

@section('title', __('admin.Edit Brand'))

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 fw-semibold mb-0">@lang('admin.Edit Brand')</h1>
        <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> @lang('admin.Back')
        </a>
    </div>

    {{-- Alerts --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('admin.brands.update', $brand) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Name EN --}}
                <div class="mb-3">
                    <label class="form-label">@lang('admin.Name (EN)')</label>
                    <input type="text" name="name_en" class="form-control"
                           value="{{ old('name_en', $brand->getTranslation('name', 'en')) }}" required>
                </div>

                {{-- Name AR --}}
                <div class="mb-3">
                    <label class="form-label">@lang('admin.Name (AR)')</label>
                    <input type="text" name="name_ar" class="form-control"
                           value="{{ old('name_ar', $brand->getTranslation('name', 'ar')) }}" required>
                </div>

                {{-- Logo --}}
                <div class="mb-3">
                    <label class="form-label">@lang('admin.Logo')</label>
                    @if ($brand->logo)
                        <div class="mb-2">
                            <img src="{{ asset($brand->logo) }}"
                                 style="width: 80px; height: 80px; object-fit: contain;" class="rounded border">
                        </div>
                    @endif
                    <input type="file" name="logo" class="form-control">
                </div>

                {{-- Banner Image --}}
                <div class="mb-3">
                    <label class="form-label">@lang('admin.Banner Image')</label>
                    @if ($brand->banner_image)
                        <div class="mb-2">
                            <img src="{{ asset($brand->banner_image) }}"
                                 style="width: 120px; height: 60px; object-fit: cover;" class="rounded border">
                        </div>
                    @endif
                    <input type="file" name="banner_image" class="form-control">
                </div>

                {{-- Status --}}
                <div class="form-check mb-3">
                    <input type="checkbox" name="is_active" class="form-check-input" value="1"
                           {{ $brand->is_active ? 'checked' : '' }}>
                    <label class="form-check-label">@lang('admin.Active')</label>
                </div>

                <button type="submit" class="btn" style="background-color: #2E3192; color:aliceblue">
                    <i class="bi bi-pencil"></i> @lang('admin.Update Brand')
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
