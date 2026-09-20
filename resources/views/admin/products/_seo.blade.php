@php
    /** @var \App\Models\Product|null $product */
    $product = $product ?? null;
    $seoTitleEn = old('seo_title_en', $product?->getTranslation('seo_title', 'en', false));
    $seoTitleAr = old('seo_title_ar', $product?->getTranslation('seo_title', 'ar', false));
    $seoDescEn = old('seo_description_en', $product?->getTranslation('seo_description', 'en', false));
    $seoDescAr = old('seo_description_ar', $product?->getTranslation('seo_description', 'ar', false));
@endphp

<div class="card border-0 shadow-sm rounded-4 p-4 mb-4" id="seo-card">
    <div class="d-flex align-items-center justify-content-between mb-1">
        <h5 class="fw-bold mb-0">
            <i class="bi bi-graph-up-arrow me-2" style="color:#2E3192;"></i>@lang('admin.SEO Enhancements')
        </h5>
        <span class="badge bg-light text-muted fw-normal">@lang('admin.Optional')</span>
    </div>
    <p class="text-muted small mb-4">@lang('admin.Leave empty to use the product name and description automatically.')</p>

    {{-- ============ PAGE TITLE ============ --}}
    <div class="mb-4">
        <label class="form-label fw-semibold">@lang('admin.Page Title')</label>

        <div class="input-group mb-2">
            <span class="input-group-text bg-light"><i class="bi bi-window-sidebar"></i></span>
            <input type="text" name="seo_title_en" dir="ltr" maxlength="255"
                class="form-control seo-input @error('seo_title_en') is-invalid @enderror"
                placeholder="@lang('admin.Page Title') (EN)" value="{{ $seoTitleEn }}">
            <span class="input-group-text bg-light small text-muted">EN</span>
            @error('seo_title_en')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="input-group mb-2">
            <span class="input-group-text bg-light"><i class="bi bi-window-sidebar"></i></span>
            <input type="text" name="seo_title_ar" dir="rtl" maxlength="255"
                class="form-control seo-input @error('seo_title_ar') is-invalid @enderror"
                placeholder="@lang('admin.Page Title') (AR)" value="{{ $seoTitleAr }}">
            <span class="input-group-text bg-light small text-muted">AR</span>
            @error('seo_title_ar')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="small text-muted">@lang('admin.Variables'):</span>
            @foreach (['{Name}' => 'admin.Product Name', '{Category}' => 'admin.Category', '{Brand}' => 'admin.Brand'] as $token => $label)
                <button type="button" class="btn btn-sm seo-token"
                    data-token="{{ $token }}"
                    style="background:#d9f5ec;color:#0d7a5f;border:0;font-size:11px;padding:2px 8px;border-radius:6px;">
                    {{ $token }}
                </button>
                <span class="small text-muted" style="font-size:11px;">@lang($label)</span>
            @endforeach
        </div>
    </div>

    {{-- ============ SEO PAGE URL ============ --}}
    <div class="mb-4">
        <label class="form-label fw-semibold">@lang('admin.SEO Page URL')</label>

        <div class="input-group mb-1">
            <span class="input-group-text bg-light"><i class="bi bi-link-45deg"></i></span>
            <span class="input-group-text bg-light small text-muted d-none d-md-inline" dir="ltr">
                {{ rtrim(url('/products'), '/') }}/
            </span>
            <input type="text" name="slug" id="seo-slug" dir="ltr" maxlength="255"
                class="form-control @error('slug') is-invalid @enderror"
                placeholder="@lang('admin.SEO Page URL')"
                value="{{ old('slug', $product?->slug) }}">
            <button type="button" class="btn btn-outline-secondary" id="seo-slug-generate">
                <i class="bi bi-magic"></i> @lang('admin.Generate')
            </button>
            @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        @if ($product && !$product->slug)
            <small class="text-muted d-block mb-2">
                <i class="bi bi-info-circle"></i>
                @lang('admin.Current URL'): <span dir="ltr">{{ $product->public_url }}</span>
            </small>
        @endif

        <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="small text-muted">@lang('admin.Variables'):</span>
            @foreach (['{Name}' => 'admin.Product Name', '{Category}' => 'admin.Category', '{Brand}' => 'admin.Brand', '{SKU}' => 'admin.SKU'] as $token => $label)
                <button type="button" class="btn btn-sm seo-token" data-token="{{ $token }}"
                    style="background:#d9f5ec;color:#0d7a5f;border:0;font-size:11px;padding:2px 8px;border-radius:6px;">
                    {{ $token }}
                </button>
                <span class="small text-muted" style="font-size:11px;">@lang($label)</span>
            @endforeach
        </div>
    </div>

    {{-- ============ PAGE DESCRIPTION ============ --}}
    <div class="mb-4">
        <label class="form-label fw-semibold">@lang('admin.Page Description')</label>

        <div class="input-group mb-2">
            <span class="input-group-text bg-light align-items-start pt-2"><i class="bi bi-card-text"></i></span>
            <textarea name="seo_description_en" dir="ltr" rows="2" maxlength="500"
                class="form-control seo-input @error('seo_description_en') is-invalid @enderror"
                placeholder="@lang('admin.Page Description') (EN)">{{ $seoDescEn }}</textarea>
            <span class="input-group-text bg-light small text-muted align-items-start pt-2">EN</span>
            @error('seo_description_en')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="input-group mb-2">
            <span class="input-group-text bg-light align-items-start pt-2"><i class="bi bi-card-text"></i></span>
            <textarea name="seo_description_ar" dir="rtl" rows="2" maxlength="500"
                class="form-control seo-input @error('seo_description_ar') is-invalid @enderror"
                placeholder="@lang('admin.Page Description') (AR)">{{ $seoDescAr }}</textarea>
            <span class="input-group-text bg-light small text-muted align-items-start pt-2">AR</span>
            @error('seo_description_ar')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="small text-muted">@lang('admin.Variables'):</span>
            @foreach (['{Name}' => 'admin.Product Name', '{Category}' => 'admin.Category', '{Brand}' => 'admin.Brand'] as $token => $label)
                <button type="button" class="btn btn-sm seo-token" data-token="{{ $token }}"
                    style="background:#d9f5ec;color:#0d7a5f;border:0;font-size:11px;padding:2px 8px;border-radius:6px;">
                    {{ $token }}
                </button>
                <span class="small text-muted" style="font-size:11px;">@lang($label)</span>
            @endforeach
        </div>
    </div>

    {{-- ============ CANONICAL ============ --}}
    <div class="mb-3">
        <label class="form-label fw-semibold">@lang('admin.Canonical URL')</label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-signpost-2"></i></span>
            <input type="url" name="canonical_url" dir="ltr" maxlength="255"
                class="form-control @error('canonical_url') is-invalid @enderror"
                placeholder="https://example.com/products/..."
                value="{{ old('canonical_url', $product?->canonical_url) }}">
            @error('canonical_url')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <small class="text-muted">@lang('admin.Leave empty to point the canonical link to this product page.')</small>
    </div>

    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="seo_noindex" value="1" id="seo_noindex"
            @checked(old('seo_noindex', $product?->seo_noindex))>
        <label class="form-check-label" for="seo_noindex">
            @lang('admin.Hide this product from search engines') <span class="text-muted small">(noindex)</span>
        </label>
    </div>
</div>

@push('scripts')
    <script>
        (function () {
            const card = document.getElementById('seo-card');
            if (!card) return;

            // Insère la variable dans le dernier champ SEO utilisé (ou le premier du bloc)
            let lastField = null;
            card.querySelectorAll('.seo-input, #seo-slug').forEach(el => {
                el.addEventListener('focus', () => lastField = el);
            });

            card.querySelectorAll('.seo-token').forEach(btn => {
                btn.addEventListener('click', () => {
                    const target = lastField && card.contains(lastField)
                        ? lastField
                        : btn.closest('.mb-4').querySelector('.seo-input, #seo-slug');
                    if (!target) return;

                    const start = target.selectionStart ?? target.value.length;
                    const end = target.selectionEnd ?? target.value.length;
                    const token = btn.dataset.token;

                    target.value = target.value.slice(0, start) + token + target.value.slice(end);
                    target.focus();
                    target.setSelectionRange(start + token.length, start + token.length);
                });
            });

            // Génération du slug depuis le nom EN + SKU
            const slugify = (value) => value
                .toString().toLowerCase().trim()
                .replace(/[_]+/g, '-')
                .replace(/[^\p{L}\p{N}\s-]+/gu, '')
                .replace(/[\s-]+/g, '-')
                .replace(/^-+|-+$/g, '');

            document.getElementById('seo-slug-generate')?.addEventListener('click', () => {
                const form = card.closest('form');
                const name = form.querySelector('[name="name_en"]')?.value
                    || form.querySelector('[name="name_ar"]')?.value || '';
                const sku = form.querySelector('[name="sku"]')?.value || '';
                document.getElementById('seo-slug').value = slugify(name + ' ' + sku);
            });
        })();
    </script>
@endpush
