@extends('layouts.admin')

@section('title', 'Create Contact Lens')

@section('content')

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-semibold" style="font-size:1.4rem;">@lang('admin.Create Contact Lens')</h1>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-light">
            <i class="bi bi-arrow-left"></i> @lang('admin.Back')
        </a>
    </div>

    <form action="{{ route('admin.contact-lenses.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <!-- LEFT COLUMN -->
            <div class="col-lg-8">

                <!-- BASIC INFO -->
                <div class="card p-4 mb-4">
                    <h6 class="fw-semibold mb-3">@lang('admin.Basic Information')</h6>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Name (EN)') </label>
                        <input type="text" name="name[fr]" value="{{ old('name.fr') }}"
                            class="form-control @error('name.fr') is-invalid @enderror">
                        @error('name.fr')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Name (AR)') </label>
                        <input type="text" name="name[ar]" value="{{ old('name.ar') }}" dir="rtl"
                            class="form-control @error('name.ar') is-invalid @enderror">
                        @error('name.ar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Description (EN)') </label>
                        <textarea name="description[fr]" rows="4" class="form-control @error('description.fr') is-invalid @enderror">{{ old('description.fr') }}</textarea>
                        @error('description.fr')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Description (AR)') </label>
                        <textarea name="description[ar]" rows="4" dir="rtl" class="form-control @error('description.ar') is-invalid @enderror">{{ old('description.ar') }}</textarea>
                        @error('description.ar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- TECHNICAL SPECIFICATIONS -->
                <div class="card p-4 mb-4">
                    <h6 class="fw-semibold mb-3">@lang('admin.Technical Specifications')</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">@lang('admin.Water Content') (%)</label>
                            <input type="number" step="0.01" name="water_content" value="{{ old('water_content') }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">@lang('admin.Diameter') (mm)</label>
                            <input type="number" step="0.01" name="diameter" value="{{ old('diameter') }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">@lang('admin.Base Curve')</label>
                            <input type="number" step="0.01" name="convexity" value="{{ old('convexity') }}" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- PRICING & STOCK -->
                <div class="card p-4 mb-4">
                    <h6 class="fw-semibold mb-3">@lang('admin.Pricing & Stock')</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">@lang('admin.Sale Price') (SAR)</label>
                            <input type="number" step="0.01" name="price" value="{{ old('price') }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">@lang('admin.Discount Price')</label>
                            <input type="number" step="0.01" name="discount_price" value="{{ old('discount_price') }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">@lang('admin.Available Stock')</label>
                            <input type="number" name="stock" value="{{ old('stock', 0) }}" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- IMAGES -->
                <div class="card p-4 mb-4">
                    <h6 class="fw-semibold mb-3">@lang('admin.Product Images')</h6>
                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Upload Images')</label>
                        <input type="file" name="images[]" class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror" multiple>
                        @error('images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">@lang('admin.You can upload multiple images. The first image will be the primary.')</small>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN -->
            <div class="col-lg-4">

                <!-- ORGANIZATION -->
                <div class="card p-4 mb-4">
                    <h6 class="fw-semibold mb-3">@lang('admin.Organization')</h6>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Lens Type')</label>
                        <select name="type" class="form-select">
                            @foreach (['daily', 'monthly', 'yearly', 'cosmetic', 'medical'] as $type)
                                <option value="{{ $type }}" @selected(old('type') == $type)>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Duration of Use')</label>
                        <input type="text" name="duration_of_use" value="{{ old('duration_of_use') }}" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Color')</label>
                        <input type="text" name="color" value="{{ old('color') }}" class="form-control">
                    </div>

                    <div class="mb-0">
                        <label class="form-label">@lang('admin.Brand')</label>
                        <select name="brand_id" class="form-select">
                            <option value="">@lang('admin.Select a brand')</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" @selected(old('brand_id') == $brand->id)>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- OPTIONS -->
                <div class="card p-4">
                    <h6 class="fw-semibold mb-3">@lang('admin.Options')</h6>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', true))>
                        <label class="form-check-label">@lang('admin.Active')</label>
                    </div>

                    <button class="btn w-100 mt-3" style="background-color: #2E3192; color:white">
                        <i class="bi bi-save"></i> @lang('admin.Create Contact Lens')
                    </button>
                </div>

            </div>
        </div>
    </form>

@endsection
