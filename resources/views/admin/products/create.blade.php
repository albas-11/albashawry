@extends('layouts.admin')

@section('title', 'Create Product')

@section('content')

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-semibold" style="font-size:1.4rem;">@lang('message.Create Product')</h1>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-light">
            <i class="bi bi-arrow-left"></i> @lang('message.Back')
        </a>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <!-- LEFT COLUMN -->
            <div class="col-lg-8">

                <!-- BASIC INFO -->
                <div class="card p-4 mb-4">
                    <h6 class="fw-semibold mb-3">@lang('message.Basic Information')</h6>

                    <div class="mb-3">
                        <label class="form-label">@lang('message.Name') (EN)</label>
                        <input type="text" name="name_en" value="{{ old('name_en') }}"
                            class="form-control @error('name_en') is-invalid @enderror">
                        @error('name_en')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('message.Name') (AR)</label>
                        <input type="text" name="name_ar" value="{{ old('name_ar') }}"
                            class="form-control @error('name_ar') is-invalid @enderror">
                        @error('name_ar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('message.SKU')</label>
                        <input type="text" name="sku" value="{{ old('sku') }}"
                            class="form-control @error('sku') is-invalid @enderror">
                        @error('sku')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('message.Description') (EN)</label>
                        <textarea name="description_en" rows="4" class="form-control @error('description_en') is-invalid @enderror">{{ old('description_en') }}</textarea>
                        @error('description_en')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('message.Description') (AR)</label>
                        <textarea name="description_ar" rows="4" class="form-control @error('description_ar') is-invalid @enderror">{{ old('description_ar') }}</textarea>
                        @error('description_ar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- PRICING -->
                <div class="card p-4 mb-4">
                    <h6 class="fw-semibold mb-3">@lang('message.Pricing & Stock')</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">@lang('message.Price') (SAR)</label>
                            <input type="number" step="0.01" name="price" value="{{ old('price') }}"
                                class="form-control @error('price') is-invalid @enderror">
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">@lang('message.Discount Price')</label>
                            <input type="number" step="0.01" name="discount_price" value="{{ old('discount_price') }}"
                                class="form-control @error('discount_price') is-invalid @enderror">
                            @error('discount_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">@lang('message.Stock')</label>
                            <input type="number" name="stock" value="{{ old('stock', 0) }}"
                                class="form-control @error('stock') is-invalid @enderror">
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- FRAME DETAILS -->
                <div class="card p-4 mb-4">
                    <h6 class="fw-semibold mb-3">@lang('message.Frame Details')</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input class="form-control" name="lens_shape" placeholder="@lang('admin.Lens Shape')"
                                value="{{ old('lens_shape') }}">
                        </div>
                        <div class="col-md-4">
                            <input class="form-control" name="frame_color" placeholder="@lang('admin.Frame Color')"
                                value="{{ old('frame_color') }}">
                        </div>
                        <div class="col-md-4">
                            <input class="form-control" name="frame_type" placeholder="@lang('admin.Frame Type')"
                                value="{{ old('frame_type') }}">
                        </div>
                        <div class="col-md-4">
                            <input class="form-control" name="lens_size" placeholder="@lang('admin.Lens Size')"
                                value="{{ old('lens_size') }}">
                        </div>
                        <div class="col-md-4">
                            <input class="form-control" name="bridge_width" placeholder="@lang('admin.Bridge Width')"
                                value="{{ old('bridge_width') }}">
                        </div>
                        <div class="col-md-4">
                            <input class="form-control" name="arm_length" placeholder="@lang('admin.Arm Length')"
                                value="{{ old('arm_length') }}">
                        </div>
                    </div>
                </div>

                <!-- IMAGES -->
                <div class="card p-4 mb-4">
                    <h6 class="fw-semibold mb-3">@lang('message.Product Images')</h6>
                    <div class="mb-3">
                        <label class="form-label">@lang('message.Upload Images')</label>
                        <input type="file" name="images[]"
                            class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror"
                            multiple>
                        @error('images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">@lang('message.You can upload multiple images. The first image will be the primary.')</small>
                    </div>
                </div>

                <!-- SEO -->
                @include('admin.products._seo')

            </div>

            <!-- RIGHT COLUMN -->
            <div class="col-lg-4">

                <!-- ORGANIZATION -->
                <div class="card p-4 mb-4">
                    <h6 class="fw-semibold mb-3">@lang('message.Organization')</h6>

                    <div class="mb-3">
                        <label class="form-label">@lang('message.Category')</label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('message.Brand')</label>
                        <select name="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" @selected(old('brand_id') == $brand->id)>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('message.Type')</label>
                        <select name="type" class="form-select">
                            @foreach (['sunglasses', 'eyeglasses', 'clipon'] as $type)
                                <option value="{{ $type }}" @selected(old('type') == $type)>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('message.Gender')</label>
                        <select name="gender" class="form-select">
                            <option value="">—</option>
                            @foreach (['men', 'women', 'girls', 'boys'] as $gender)
                                <option value="{{ $gender }}" @selected(old('gender') == $gender)>
                                    {{ ucfirst($gender) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- OPTIONS -->
                <div class="card p-4">
                    <h6 class="fw-semibold mb-3">@lang('message.Options')</h6>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                            @checked(old('is_active', true))>
                        <label class="form-check-label">@lang('message.Active')</label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                            @checked(old('is_featured'))>
                        <label class="form-check-label">@lang('message.Featured')</label>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="tryon_available" value="1"
                            @checked(old('tryon_available'))>
                        <label class="form-check-label">@lang('message.Enable Virtual Try-On')</label>
                    </div>

                    <select name="tryon_file_id" class="form-select">
                        <option value="">@lang('message.No Try-On File')</option>
                        @foreach ($tryonFiles as $file)
                            <option value="{{ $file->id }}" @selected(old('tryon_file_id') == $file->id)>
                                {{ $file->name }}
                            </option>
                        @endforeach
                    </select>

                    <button class="btn w-100 mt-4" style="background-color: #2E3192; color:aliceblue">
                        <i class="bi bi-save"></i> @lang('admin.Create EYEWARE')
                    </button>
                </div>

            </div>
        </div>
    </form>

@endsection
