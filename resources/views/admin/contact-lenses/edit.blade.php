@extends('layouts.admin')

@section('title', 'Edit Contact Lens')

@section('content')

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.contact-lenses.index') }}"
                            class="text-decoration-none text-muted">@lang('admin.Contact Lenses')</a></li>
                </ol>
            </nav>
            <h1 class="fw-black text-dark mb-0" style="font-size:1.75rem;">
                {{ $contact_lens->getTranslation('name', app()->getLocale()) ?: $contact_lens->getTranslation('name', 'fr') }}
            </h1>
            <p class="text-muted small">ID: <span
                    class="badge bg-light text-dark font-monospace">#CL-{{ str_pad($contact_lens->id, 5, '0', STR_PAD_LEFT) }}</span>
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.index') }}" class="btn btn-white shadow-sm border-0 px-4 rounded-3">
                <i class="bi bi-x-lg me-2"></i> @lang('admin.Cancel')
            </a>
            <button type="submit" form="edit-contact-lense-form" class="btn shadow-sm px-4 rounded-3 text-white"
                style="background-color: #2E3192;">
                <i class="bi bi-check2-circle me-2"></i> @lang('admin.Save Changes')
            </button>
        </div>
    </div>

    <form action="{{ route('admin.contact-lenses.update', $contact_lens->id) }}" method="POST" id="edit-contact-lense-form"
        enctype="multipart/form-data">


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
                        <input type="text" name="name[fr]" value="{{ old('name.fr', $contact_lens->name['fr'] ?? '') }}"
                            class="form-control @error('name.fr') is-invalid @enderror">
                        @error('name.fr')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Name (AR)')</label>
                        <input type="text" name="name[ar]" value="{{ old('name.ar', $contact_lens->name['ar'] ?? '') }}"
                            dir="rtl" class="form-control @error('name.ar') is-invalid @enderror">
                        @error('name.ar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Description (EN)')</label>
                        <textarea name="description[fr]" rows="4" class="form-control @error('description.fr') is-invalid @enderror">{{ old('description.fr', $contact_lens->description['fr'] ?? '') }}</textarea>
                        @error('description.fr')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Description (AR)')</label>
                        <textarea name="description[ar]" rows="4" dir="rtl"
                            class="form-control @error('description.ar') is-invalid @enderror">{{ old('description.ar', $contact_lens->description['ar'] ?? '') }}</textarea>
                        @error('description.ar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- TECHNICAL SPECIFICATIONS -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3">@lang('admin.Technical Specifications')</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">@lang('admin.Water Content') (%)</label>
                            <input type="number" step="0.01" name="water_content"
                                value="{{ old('water_content', $contact_lens->water_content) }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">@lang('admin.Diameter') (mm)</label>
                            <input type="number" step="0.01" name="diameter"
                                value="{{ old('diameter', $contact_lens->diameter) }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">@lang('admin.Base Curve')</label>
                            <input type="number" step="0.01" name="convexity"
                                value="{{ old('convexity', $contact_lens->convexity) }}" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- PRICING & STOCK -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3">@lang('admin.Pricing & Stock')</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">@lang('admin.Sale Price') (SAR)</label>
                            <input type="number" step="0.01" name="price"
                                value="{{ old('price', $contact_lens->price) }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">@lang('admin.Discount Price')</label>
                            <input type="number" step="0.01" name="discount_price"
                                value="{{ old('discount_price', $contact_lens->discount_price) }}"
                                class="form-control text-danger">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">@lang('admin.Available Stock')</label>
                            <input type="number" name="stock" value="{{ old('stock', $contact_lens->stock) }}"
                                class="form-control">
                        </div>
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

                    @if ($contact_lens->images && $contact_lens->images->count())
                        <label class="form-label small fw-bold text-muted mt-3">@lang('admin.Current Images')</label>

                        <div class="row g-3 mt-2">
                            @foreach ($contact_lens->images as $image)
                                <div class="col-md-3 col-6" id="img-card-{{ $image->id }}">
                                    <div class="position-relative rounded-3 overflow-hidden border">
                                        <img src="{{ asset($image->image) }}" class="w-100 object-fit-cover"
                                            style="height: 120px;">

                                        <button type="button"
                                            class="btn position-absolute top-0 end-0 m-1 js-delete-image"
                                            data-url="{{ route('admin.contact-lenses.images.destroy', [$contact_lens->id, $image->id]) }}"
                                            data-card="img-card-{{ $image->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                        @if ($image->is_primary)
                                            <span class="badge bg-primary position-absolute bottom-0 start-0 m-1">
                                                @lang('admin.Primary')
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <script>
                            document.addEventListener('click', async (e) => {
                                const btn = e.target.closest('.js-delete-image');
                                if (!btn) return;

                                if (!confirm('Delete this image?')) return;

                                const url = btn.dataset.url;
                                const cardId = btn.dataset.card;

                                btn.disabled = true;

                                try {
                                    const res = await fetch(url, {
                                        method: 'DELETE',
                                        credentials: 'same-origin', // IMPORTANT (session cookies)
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json',
                                        },
                                    });

                                    const contentType = res.headers.get('content-type') || '';
                                    const body = contentType.includes('application/json') ?
                                        await res.json() :
                                        await res.text();

                                    if (!res.ok) {
                                        console.error('Delete failed:', res.status, body);
                                        alert(`Delete failed (${res.status}). Check console.`);
                                        btn.disabled = false;
                                        return;
                                    }

                                    // remove from UI
                                    const card = document.getElementById(cardId);
                                    if (card) card.remove();

                                } catch (err) {
                                    console.error(err);
                                    alert('Could not delete image (network/JS error). Check console.');
                                    btn.disabled = false;
                                }
                            });
                        </script>
                        <small class="text-muted d-block mt-2">
                            @lang('admin.Click the trash icon to mark an image for deletion, then save changes.')
                        </small>
                    @endif
                </div>

            </div>

            <!-- RIGHT COLUMN -->
            <div class="col-lg-4">

                <!-- ORGANIZATION -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h6 class="fw-bold mb-3">@lang('admin.Organization')</h6>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Lens Type')</label>
                        <select name="type" class="form-select">
                            @foreach (['daily', 'monthly', 'yearly', 'cosmetic', 'medical'] as $type)
                                <option value="{{ $type }}" @selected(old('type', $contact_lens->type) == $type)>{{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Duration of Use')</label>
                        <input type="text" name="duration_of_use"
                            value="{{ old('duration_of_use', $contact_lens->duration_of_use) }}" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">@lang('admin.Color')</label>
                        <input type="text" name="color" value="{{ old('color', $contact_lens->color) }}"
                            class="form-control">
                    </div>

                    <div class="mb-0">
                        <label class="form-label">@lang('admin.Brand')</label>
                        <select name="brand_id" class="form-select">
                            <option value="">@lang('admin.Select a brand')</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" @selected(old('brand_id', $contact_lens->brand_id) == $brand->id)>{{ $brand->name }}
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
                            @checked(old('is_active', $contact_lens->is_active))>
                        <label class="form-check-label">@lang('admin.Active')</label>
                    </div>

                </div>

            </div>
        </div>
    </form>

@endsection
