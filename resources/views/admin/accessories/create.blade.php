@extends('layouts.admin')

@section('title', 'Create Accessory')

@section('content')

<!-- PAGE HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-semibold" style="font-size:1.4rem;">@lang('admin.Create Accessory')</h1>
    </div>
    <a href="{{ route('admin.products.index') }}" class="btn btn-light">
        <i class="bi bi-arrow-left"></i> @lang('admin.Back')
    </a>
</div>

<form action="{{ route('admin.accessories.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">

        <!-- LEFT COLUMN -->
        <div class="col-lg-8">

            <!-- BASIC INFO -->
            <div class="card p-4 mb-4">
                <h6 class="fw-semibold mb-3">@lang('admin.Basic Information')</h6>

                <div class="mb-3">
                    <label class="form-label">@lang('admin.Name (EN)')</label>
                    <input type="text" name="name_en" value="{{ old('name_en') }}"
                        class="form-control @error('name_en') is-invalid @enderror">
                    @error('name_en')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">@lang('admin.Name (AR)')</label>
                    <input type="text" name="name_ar" value="{{ old('name_ar') }}" dir="rtl"
                        class="form-control @error('name_ar') is-invalid @enderror">
                    @error('name_ar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">@lang('admin.Description (EN)')</label>
                    <textarea name="description_en" rows="3"
                        class="form-control @error('description_en') is-invalid @enderror">{{ old('description_en') }}</textarea>
                    @error('description_en')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">@lang('admin.Description (AR)')</label>
                    <textarea name="description_ar" rows="3" dir="rtl"
                        class="form-control @error('description_ar') is-invalid @enderror">{{ old('description_ar') }}</textarea>
                    @error('description_ar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- PRICING -->
            <div class="card p-4 mb-4">
                <h6 class="fw-semibold mb-3">@lang('admin.Pricing & Stock')</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">@lang('admin.Price')</label>
                        <input type="number" step="0.01" name="price" value="{{ old('price') }}"
                            class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">@lang('admin.Stock')</label>
                        <input type="number" name="stock" value="{{ old('stock', 0) }}" class="form-control">
                    </div>
                </div>
            </div>

            <!-- IMAGES -->
            <div class="card p-4 mb-4">
                <h6 class="fw-semibold mb-3">@lang('admin.Product Images')</h6>
                <input type="file" name="images[]" multiple
                    class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror">
                @error('images')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @error('images.*')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">@lang('admin.You can upload multiple images. The first image will be the primary.')</small>
            </div>

        </div>

        <!-- RIGHT COLUMN -->
        <div class="col-lg-4">

            <!-- ORGANIZATION -->
            <div class="card p-4 mb-4">
                <h6 class="fw-semibold mb-3">@lang('admin.Organization')</h6>

                <div class="mb-3">
                    <label class="form-label">@lang('admin.Type')</label>
                    <select name="type" class="form-select">
                        @foreach(['case','cleaner','chain','cloth','repair kit'] as $type)
                            <option value="{{ $type }}" @selected(old('type')==$type)>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- OPTIONS -->
            <div class="card p-4">
                <h6 class="fw-semibold mb-3">@lang('admin.Options')</h6>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                        @checked(old('is_active', true))>
                    <label class="form-check-label">@lang('admin.Active')</label>
                </div>

                <button class="btn w-100 mt-3" style="background-color:#2E3192;color:white">
                    <i class="bi bi-save"></i> @lang('admin.Create Accessory')
                </button>
            </div>

        </div>

    </div>
</form>

@endsection
