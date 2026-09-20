@extends('layouts.admin')

@section('title', 'Edit Accessory')

@section('content')

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-semibold" style="font-size:1.4rem;">@lang('admin.Edit Accessory')</h1>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-light">
            <i class="bi bi-arrow-left"></i> @lang('admin.Back')
        </a>
    </div>

    <form action="{{ route('admin.accessories.update', $accessory->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-4">

            <!-- LEFT COLUMN -->
            <div class="col-lg-8">

                <!-- BASIC INFO -->
                <div class="card p-4 mb-4">
                    <h6 class="fw-semibold mb-3">@lang('admin.Basic Information')</h6>
                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Name (EN)')</label>
                        <input type="text" name="name_en"
                            value="{{ old('name_en', $accessory->getTranslation('name', 'en')) }}"
                            class="form-control @error('name_en') is-invalid @enderror">
                        @error('name_en')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Name (AR)')</label>
                        <input type="text" name="name_ar"
                            value="{{ old('name_ar', $accessory->getTranslation('name', 'ar')) }}" dir="rtl"
                            class="form-control @error('name_ar') is-invalid @enderror">
                        @error('name_ar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Description (EN)')</label>
                        <textarea name="description_en" rows="3" class="form-control @error('description_en') is-invalid @enderror">{{ old('description_en', $accessory->getTranslation('description', 'en')) }}</textarea>
                        @error('description_en')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Description (AR)')</label>
                        <textarea name="description_ar" rows="3" dir="rtl"
                            class="form-control @error('description_ar') is-invalid @enderror">{{ old('description_ar', $accessory->getTranslation('description', 'ar')) }}</textarea>
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
                            <input type="number" step="0.01" name="price"
                                value="{{ old('price', $accessory->price) }}" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">@lang('admin.Stock')</label>
                            <input type="number" name="stock" value="{{ old('stock', $accessory->stock) }}"
                                class="form-control">
                        </div>
                    </div>
                </div>

                <!-- IMAGES -->
                <div class="card p-4 mb-4">
                    <h6 class="fw-semibold mb-3">@lang('admin.Product Images')</h6>

                    @if ($accessory->images && $accessory->images->count())
                        <div class="mb-3 d-flex flex-wrap gap-2">
                            @foreach ($accessory->images as $img)
                                <div style="width:80px; height:80px;">
                                    <img src="{{ asset($img->image) }}" class="rounded-2 w-100 h-100 object-fit-cover">
                                </div>
                            @endforeach
                        </div>
                    @endif


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
                            @foreach (['case', 'cleaner', 'chain', 'cloth', 'repair kit'] as $type)
                                <option value="{{ $type }}" @selected(old('type', $accessory->type) == $type)>{{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- OPTIONS -->
                <div class="card p-4">
                    <h6 class="fw-semibold mb-3">@lang('admin.Options')</h6>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                            @checked(old('is_active', $accessory->is_active))>
                        <label class="form-check-label">@lang('admin.Active')</label>
                    </div>

                    <button class="btn w-100 mt-3" style="background-color:#2E3192;color:white">
                        <i class="bi bi-save"></i> @lang('admin.Update Accessory')
                    </button>
                </div>

            </div>

        </div>
    </form>

@endsection
