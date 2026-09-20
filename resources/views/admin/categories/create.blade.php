@extends('layouts.admin')

@section('title', 'Create Category')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.categories.index') }}"
                class="btn btn-sm btn-outline-secondary rounded-circle p-2 shadow-sm border-0 bg-white">
                <i class="bi bi-chevron-left"></i>
            </a>
            <div>
                <h1 class="fw-bold mb-0 text-dark" style="font-size: 1.5rem; letter-spacing: -0.5px;">@lang('admin.Create Category')</h1>
                <p class="text-muted small mb-0">@lang('admin.Add a new category')</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 16px;">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h6 class="fw-bold mb-0 text-primary">
                            <i class="bi bi-info-circle me-2"></i>@lang('admin.Basic Information')
                        </h6>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="row g-3">
                            <!-- Name EN -->
                            <div class="col-md-6 mb-3">
                                <label
                                    class="form-label fw-semibold small text-uppercase text-muted">@lang('admin.Name (EN)')</label>
                                <input type="text" name="name_en" id="category_name_en" value="{{ old('name_en') }}"
                                    class="form-control bg-light border-0 py-2.5 rounded-3 @error('name_en') is-invalid @enderror">
                                @error('name_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Name AR -->
                            <div class="col-md-6 mb-3">
                                <label
                                    class="form-label fw-semibold small text-uppercase text-muted">@lang('admin.Name (AR)')</label>
                                <input type="text" name="name_ar" value="{{ old('name_ar') }}"
                                    class="form-control bg-light border-0 py-2.5 rounded-3 @error('name_ar') is-invalid @enderror">
                                @error('name_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Slug -->
                            <div class="col-12 mb-3">
                                <label
                                    class="form-label fw-semibold small text-uppercase text-muted">@lang('admin.Slug')</label>
                                <input type="text" name="slug" id="category_slug" value="{{ old('slug') }}"
                                    class="form-control bg-light border-0 py-2.5 rounded-3">
                            </div>

                            <!-- Description EN -->
                            <div class="col-12 mb-3">
                                <label
                                    class="form-label fw-semibold small text-uppercase text-muted">@lang('admin.Description (EN)')</label>
                                <textarea name="description_en" rows="3" class="form-control bg-light border-0 rounded-3">{{ old('description_en') }}</textarea>
                            </div>

                            <!-- Description AR -->
                            <div class="col-12 mb-3">
                                <label
                                    class="form-label fw-semibold small text-uppercase text-muted">@lang('admin.Description (AR)')</label>
                                <textarea name="description_ar" rows="3" class="form-control bg-light border-0 rounded-3">{{ old('description_ar') }}</textarea>
                            </div>

                            <!-- Image -->
                            <div class="col-12">
                                <label
                                    class="form-label fw-semibold small text-uppercase text-muted">@lang('admin.Image')</label>
                                <div class="p-4 bg-light rounded-3 border-2 border-dashed text-center">
                                    <i class="bi bi-cloud-arrow-up fs-2 text-muted mb-2"></i>
                                    <input type="file" name="image"
                                        class="form-control border-0 bg-white mx-auto shadow-sm" style="max-width: 300px;">
                                    <small class="text-muted d-block mt-2">Formats JPG, PNG, WEBP. Max 2MB.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hierarchy & Order -->
                <div class="card border-0 shadow-sm mt-4" style="border-radius: 16px;">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-diagram-3 me-2"></i>@lang('admin.Hierarchy')
                        </h6>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="row g-3">
                            <!-- Parent Category -->
                            <div class="col-md-6">
                                <label
                                    class="form-label fw-semibold small text-uppercase text-muted">@lang('admin.Parent Category')</label>
                                <select name="parent_id" class="form-select bg-light border-0 py-2.5 rounded-3">
                                    <option value="">— @lang('admin.None') —</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" @selected(old('parent_id') == $cat->id)>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Order -->
                            <div class="col-md-6">
                                <label
                                    class="form-label fw-semibold small text-uppercase text-muted">@lang('admin.Order')</label>
                                <input type="number" name="order" value="{{ old('order', 0) }}"
                                    class="form-control bg-light border-0 py-2.5 rounded-3">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Options -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="border-radius: 16px; top: 90px;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4">@lang('admin.Options')</h6>

                        <div class="form-check form-switch mb-4 p-0 d-flex justify-content-between align-items-center">
                            <div>
                                <label class="form-check-label fw-semibold text-dark">@lang('admin.Active')</label>
                            </div>
                            <input class="form-check-input ms-0" type="checkbox" name="is_active" value="1"
                                style="width: 45px; height: 22px;" checked>
                        </div>

                        <hr class="opacity-10 mb-4">

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn py-2.5 fw-bold shadow-sm"
                                style="background-color: #2E3192; color: white; border-radius: 10px;">
                                <i class="bi bi-plus-lg me-2"></i>@lang('admin.Create Category')
                            </button>

                            <a href="{{ route('admin.categories.index') }}"
                                class="btn btn-light py-2.5 border-0 text-muted fw-semibold">
                                @lang('admin.Cancel')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Styles et script -->
    <style>
        .form-control:focus,
        .form-select:focus {
            background-color: #fff !important;
            border-color: #2E3192 !important;
            box-shadow: 0 0 0 4px rgba(46, 49, 146, 0.1) !important;
        }

        .border-dashed {
            border-style: dashed !important;
            border-color: #dee2e6 !important;
        }

        .form-check-input:checked {
            background-color: #2E3192;
            border-color: #2E3192;
        }
    </style>

    <script>
        // Auto-slug à partir du name_en
        document.getElementById('category_name_en').addEventListener('input', function() {
            let name = this.value;
            let slug = name.toLowerCase()
                .replace(/[^\w ]+/g, '')
                .replace(/ +/g, '-');
            document.getElementById('category_slug').value = slug;
        });
    </script>

@endsection
