@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}"
                            class="text-decoration-none text-muted">@lang('admin.Products')</a></li>
                    <li class="breadcrumb-item active" aria-current="page">@lang('admin.Edit Mode')</li>
                </ol>
            </nav>
            <h1 class="fw-black text-dark mb-0" style="font-size:1.75rem;">{{ $product->name_en ?? $product->name }}</h1>
            <p class="text-muted small">ID: <span
                    class="badge bg-light text-dark font-monospace">#PRD-{{ str_pad($product->id, 5, '0', STR_PAD_LEFT) }}</span>
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.index') }}" class="btn btn-white shadow-sm border-0 px-4 rounded-3">
                <i class="bi bi-x-lg me-2"></i> @lang('admin.Cancel')
            </a>
            <button type="submit" form="edit-product-form" class="btn shadow-sm px-4 rounded-3 text-white"
                style="background-color: #2E3192;">
                <i class="bi bi-check2-circle me-2"></i> @lang('admin.Save Changes')
            </button>
        </div>
    </div>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data"
        id="edit-product-form">
        @csrf
        @method('PUT')

        <div class="row g-4">

            <!-- LEFT COLUMN -->
            <div class="col-lg-8">

                <!-- BASIC INFO -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3">@lang('admin.Basic Information')</h5>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Name (EN)')</label>
                        <input type="text" name="name_en"
                            value="{{ old('name_en', $product->getTranslation('name', 'en')) }}" class="form-control">
                        @error('name_en')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Name (AR)') </label>
                        <input type="text" name="name_ar" dir="rtl"
                            value="{{ old('name_ar', $product->getTranslation('name', 'ar')) }}" class="form-control">
                        @error('name_ar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('message.SKU')</label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                            class="form-control @error('sku') is-invalid @enderror">
                        @error('sku')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Description (EN)')</label>
                        <textarea name="description_en" rows="4" class="form-control">  {{ old('description_en', $product->getTranslation('description', 'en')) }}</textarea>
                        @error('description_en')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Description (AR)') </label>
                        <textarea name="description_ar" rows="4" dir="rtl"
                            class="form-control @error('description_ar') is-invalid @enderror">{{ old('description_ar', $product->getTranslation('description', 'ar')) }}</textarea>
                        @error('description_ar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- PRICING & STOCK -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3">@lang('admin.Pricing & Stock')</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">@lang('admin.Price') (DH)</label>
                            <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}"
                                class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">@lang('admin.Discount Price')</label>
                            <input type="number" step="0.01" name="discount_price"
                                value="{{ old('discount_price', $product->discount_price) }}"
                                class="form-control text-danger">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">@lang('admin.Stock')</label>
                            <input type="number" name="stock" value="{{ old('stock', $product->stock) }}"
                                class="form-control">
                        </div>
                    </div>
                </div>

                <!-- FRAME DETAILS -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3">@lang('admin.Frame Details')</h5>
                    <div class="row g-3">
                        @foreach (['frame_shape', 'frame_color', 'frame_type', 'lens_size', 'bridge_width', 'arm_length'] as $field)
                            <div class="col-md-4">
                                <input class="form-control" name="{{ $field }}" placeholder="@lang('admin.' . $field)"
                                    value="{{ old($field, $product->$field) }}">
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- IMAGES -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3">@lang('admin.Product Images')</h5>
                    <input type="file" name="images[]" multiple
                        class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror">
                    @error('images')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @error('images.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">@lang('admin.You can upload multiple images. The first image will be the primary.')</small>

                    @if ($product->images && $product->images->count())
                        <label class="form-label small fw-bold text-muted mb-3">@lang('admin.Current Images')</label>
                        <div class="row g-3 mb-4">
                            @foreach ($product->images as $image)
                                <div class="col-md-3 col-6">
                                    <div class="position-relative group rounded-3 overflow-hidden border">
                                        <img src="{{ asset($image->image) }}" class="w-100 object-fit-cover"
                                            style="height: 120px;">
                                        @if ($image->is_primary)
                                            <span class="position-absolute top-0 start-0 badge bg-primary m-1 shadow-sm"
                                                style="font-size: 10px;">@lang('admin.Primary')</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- SEO -->
                @include('admin.products._seo', ['product' => $product])

            </div>

            <!-- RIGHT COLUMN -->
            <div class="col-lg-4">

                <!-- ORGANIZATION -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h6 class="fw-bold mb-3">@lang('admin.Organization')</h6>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Category')</label>
                        <select name="category_id" class="form-select">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected($product->category_id == $category->id)>{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Brand')</label>
                        <select name="brand_id" class="form-select">
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" @selected($product->brand_id == $brand->id)>{{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Type')</label>
                        <select name="type" class="form-select">
                            @foreach (['sunglasses', 'glasses', 'clipon'] as $type)
                                <option value="{{ $type }}" @selected($product->type == $type)>{{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Gender')</label>
                        <select name="gender" class="form-select">
                            <option value="">—</option>
                            @foreach (['men', 'women', 'girls', 'boys'] as $gender)
                                <option value="{{ $gender }}" @selected($product->gender == $gender)>{{ ucfirst($gender) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- OPTIONS -->
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h6 class="fw-bold mb-3">@lang('admin.Options')</h6>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                            @checked($product->is_active)>
                        <label class="form-check-label">@lang('admin.Active')</label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                            @checked($product->is_featured)>
                        <label class="form-check-label">@lang('admin.Featured')</label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="tryon_available" value="1"
                            @checked($product->tryon_available)>
                        <label class="form-check-label">@lang('admin.Enable Virtual Try-On')</label>
                    </div>

                    <select name="tryon_file_id" class="form-select">
                        <option value="">@lang('admin.No Try-On File')</option>
                        @foreach (\App\Models\TryonFile::active()->get() as $file)
                            <option value="{{ $file->id }}" @selected($product->tryon_file_id == $file->id)>{{ $file->name }}</option>
                        @endforeach
                    </select>

                </div>

            </div>
        </div>
    </form>

@endsection
