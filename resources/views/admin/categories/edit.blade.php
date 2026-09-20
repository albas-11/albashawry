@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary rounded-circle p-2 shadow-sm border-0 bg-white">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h1 class="fw-bold mb-0 text-dark" style="font-size: 1.5rem; letter-spacing: -0.5px;">@lang('admin.Edit Category')</h1>
    </div>

   
</div>

<form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-bold mb-0 text-primary" style="color: #2E3192 !important;">
                        <i class="bi bi-info-circle me-2"></i>@lang('admin.Basic Information')
                    </h6>
                </div>

                <div class="card-body p-4 pt-0">
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">@lang('admin.Category Name')</label>
                            <input type="text" name="name" value="{{ old('name', $category->name) }}"
                                   class="form-control bg-light border-0 py-2.5 rounded-3 @error('name') is-invalid @enderror"
                                   placeholder="Nom de la catégorie">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">@lang('admin.Slug')</label>
                            <input type="text" name="slug" value="{{ old('slug', $category->slug) }}"
                                   class="form-control bg-light border-0 py-2.5 rounded-3"
                                   placeholder="URL-friendly-name">
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">@lang('admin.Description')</label>
                            <textarea name="description" rows="4" class="form-control bg-light border-0 rounded-3"
                                      placeholder="Bref résumé de la catégorie...">{{ old('description', $category->description) }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold small text-uppercase text-muted">@lang('admin.Image')</label>
                            <div class="d-flex align-items-center gap-4 p-3 bg-light rounded-3 border-dashed border-2">
                                @if($category->image)
                                    <div class="position-relative">
                                        <img src="{{ asset('storage/' . $category->image) }}"
                                             class="shadow-sm border border-white"
                                             style="width: 100px; height: 100px; object-fit: cover; border-radius: 12px;">
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success border border-white">Actuelle</span>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <input type="file" name="image" class="form-control border-0 bg-white">
                                    <small class="text-muted d-block mt-2">Formats suggérés: JPG, PNG. Taille max: 2MB</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-diagram-3 me-2"></i>@lang('admin.Hierarchy & Order')
                    </h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase text-muted">@lang('admin.Parent Category')</label>
                            <select name="parent_id" class="form-select bg-light border-0 py-2.5 rounded-3">
                                <option value="">@lang('admin.— None —')</option>
                                @foreach($categories as $cat)
                                    @if($cat->id !== $category->id)
                                        <option value="{{ $cat->id }}" @selected(old('parent_id', $category->parent_id) == $cat->id)>
                                            {{ $cat->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase text-muted">@lang('admin.Order')</label>
                            <input type="number" name="order" value="{{ old('order', $category->order) }}"
                                   class="form-control bg-light border-0 py-2.5 rounded-3">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="border-radius: 16px; top: 90px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-4">@lang('admin.Options')</h6>

                    <div class="form-check form-switch mb-4 p-0 d-flex justify-content-between align-items-center">
                        <div>
                            <label class="form-check-label fw-semibold text-dark">@lang('admin.Active')</label>
                        </div>
                        <input class="form-check-input ms-0" type="checkbox" name="is_active" value="1"
                               style="width: 45px; height: 22px;"
                               @checked(old('is_active', $category->is_active))>
                    </div>

                    <hr class="opacity-10 mb-4">

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn py-2.5 fw-bold shadow-sm"
                                style="background-color: #2E3192; color: white; border-radius: 10px;">
                            <i class="bi bi-check2-circle me-2"></i>@lang('admin.Update Category')
                        </button>

                        <a href="{{ route('admin.categories.index') }}" class="btn btn-light py-2.5 border-0 text-muted fw-semibold">
                            @lang('admin.Cancel')
                        </a>
                    </div>
                </div>
                <div class="card-footer border-0 bg-light-subtle p-3 text-center">
                    <small class="text-muted">@lang('admin.Category ID ') : #{{ $category->id }}</small>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
    /* Focus effects */
    .form-control:focus, .form-select:focus {
        background-color: #fff !important;
        border-color: #2E3192 !important;
        box-shadow: 0 0 0 4px rgba(46, 49, 146, 0.1) !important;
    }

    .border-dashed {
        border-style: dashed !important;
        border-color: #dee2e6 !important;
    }

    /* Style Switch Bootstrap customisé */
    .form-check-input:checked {
        background-color: #2E3192;
        border-color: #2E3192;
    }
</style>

@endsection
