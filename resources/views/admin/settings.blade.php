@extends('layouts.admin')
@section('title', __('Settings'))
@section('content')
    <div class="container-fluid py-4">
        <div class="max-w-5xl mx-auto">
            <!-- PAGE HEADER -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fw-bold">@lang('message.site_settings')</h1>
            </div>

            <!-- SUCCESS MESSAGE -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- ERROR MESSAGES -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- SETTINGS FORM -->
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class=" bg-opacity-10 text-primary rounded-3 p-2 me-3">
                                <i class="bi bi-info-circle-fill fs-5"></i>
                            </div>
                            <h5 class="mb-0 fw-bold text-dark">@lang('message.general_info')</h5>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('message.site_name') <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-globe"></i></span>
                                    <input type="text" name="site_name"
                                        value="{{ old('site_name', $settings->site_name ?? config('app.name')) }}"
                                        class="form-control bg-light border-0 py-2" placeholder="Ex: My Store" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('message.default_language')
                                </label>
                                <select name="locale" class="form-select bg-light border-0 py-2">
                                    <option value="fr"
                                        {{ old('locale', $settings->locale ?? 'fr') == 'fr' ? 'selected' : '' }}>🇫🇷
                                        Français
                                    </option>
                                    <option value="en"
                                        {{ old('locale', $settings->locale ?? 'fr') == 'en' ? 'selected' : '' }}>🇺🇸
                                        English
                                    </option>
                                    <option value="ar"
                                        {{ old('locale', $settings->locale ?? 'fr') == 'ar' ? 'selected' : '' }}>🇲🇦
                                        العربية
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('message.site_logo')
                                </label>
                                <div class="p-3 border rounded-3 bg-light bg-opacity-50">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        @if (isset($settings->logo) && $settings->logo)
                                            <div class="bg-white p-2 rounded shadow-sm border">
                                                <img src="{{ asset('images/settings/' . $settings->logo) }}?v={{ time() }}"
                                                    alt="Logo" style="height: 50px; width: 50px; object-fit: contain;">
                                            </div>
                                        @else
                                            <div class="bg-white p-2 rounded shadow-sm border d-flex align-items-center justify-content-center"
                                                style="height: 68px; width: 68px;">
                                                <i class="bi bi-image text-muted fs-3"></i>
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <input type="file" name="logo"
                                                class="form-control form-control-sm border-0 shadow-sm" accept="image/*">
                                        </div>
                                    </div>
                                    <small class="text-muted d-block"><i class="bi bi-info-circle me-1"></i>
                                        @lang('message.logo_recommendation')</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('message.favicon')
                                </label>
                                <div class="p-3 border rounded-3 bg-light bg-opacity-50">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        @if (isset($settings->favicon) && $settings->favicon)
                                            <div class="bg-white p-2 rounded shadow-sm border">
                                                <img src="{{ asset('images/settings/' . $settings->favicon) }}?v={{ time() }}"
                                                    alt="Favicon" style="height: 32px; width: 32px;">
                                            </div>
                                        @else
                                            <div class="bg-white p-2 rounded shadow-sm border d-flex align-items-center justify-content-center"
                                                style="height: 50px; width: 50px;">
                                                <i class="bi bi-intersect text-muted fs-4"></i>
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <input type="file" name="favicon"
                                                class="form-control form-control-sm border-0 shadow-sm" accept="image/*">
                                        </div>
                                    </div>
                                    <small class="text-muted d-block"><i class="bi bi-info-circle me-1"></i>
                                        @lang('message.favicon_format')</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 2. CONTACT DETAILS -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class=" bg-opacity-10 text-success rounded-3 p-2 me-3">
                                <i class="bi bi-telephone-fill fs-5"></i>
                            </div>
                            <h5 class="mb-0 fw-bold text-dark">@lang('message.contact_details')</h5>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('message.main_email') <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-muted">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" name="admin_email"
                                        value="{{ old('admin_email', $settings->admin_email ?? '') }}"
                                        class="form-control bg-light border-0 py-2" placeholder="admin@example.com"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('message.phone_number') <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-muted">
                                        <i class="bi bi-whatsapp"></i>
                                    </span>
                                    <input type="text" name="phone"
                                        value="{{ old('phone', $settings->phone ?? '') }}"
                                        class="form-control bg-light border-0 py-2" placeholder="+212 6XX XXX XXX"
                                        required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('message.full_address')
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-muted align-items-start pt-2">
                                        <i class="bi bi-geo-alt"></i>
                                    </span>
                                    <textarea name="address" class="form-control bg-light border-0" rows="2"
                                        placeholder="123 Rue exemple, Ville, Pays">{{ old('address', $settings->address ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2b. LEGAL IDENTIFIERS (CR / VAT) -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-opacity-10 rounded-3 p-2 me-3" style="color:#0f7a52;">
                                <i class="bi bi-patch-check-fill fs-5"></i>
                            </div>
                            <h5 class="mb-0 fw-bold text-dark">@lang('message.legal_identifiers')</h5>
                        </div>
                        <p class="text-muted small mb-4 ps-1">@lang('message.legal_identifiers_hint')</p>

                        <div class="row g-4">
                            {{-- ===== Commercial register ===== --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('message.cr_number')
                                </label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text bg-light border-0 text-muted">
                                        <i class="bi bi-building"></i>
                                    </span>
                                    <input type="text" name="cr_number" dir="ltr"
                                        value="{{ old('cr_number', $settings->cr_number ?? '') }}"
                                        class="form-control bg-light border-0 py-2 @error('cr_number') is-invalid @enderror"
                                        placeholder="4030061406">
                                    @error('cr_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('message.cr_image')
                                </label>
                                <div class="p-3 border rounded-3 bg-light bg-opacity-50">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        @if (!empty($settings->cr_image))
                                            <div class="bg-white p-2 rounded shadow-sm border">
                                                <img src="{{ asset('images/settings/' . $settings->cr_image) }}?v={{ time() }}"
                                                    alt="CR" style="height: 50px; width: 50px; object-fit: contain;">
                                            </div>
                                        @else
                                            <div class="bg-white p-2 rounded shadow-sm border d-flex align-items-center justify-content-center"
                                                style="height: 68px; width: 68px;">
                                                <i class="bi bi-image text-muted fs-3"></i>
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <input type="file" name="cr_image"
                                                class="form-control form-control-sm border-0 shadow-sm @error('cr_image') is-invalid @enderror"
                                                accept="image/*">
                                            @error('cr_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <small class="text-muted d-block"><i class="bi bi-info-circle me-1"></i>
                                        @lang('message.legal_image_recommendation')</small>
                                </div>
                            </div>

                            {{-- ===== VAT ===== --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('message.vat_number')
                                </label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text bg-light border-0 text-muted">
                                        <i class="bi bi-receipt"></i>
                                    </span>
                                    <input type="text" name="vat_number" dir="ltr"
                                        value="{{ old('vat_number', $settings->vat_number ?? '') }}"
                                        class="form-control bg-light border-0 py-2 @error('vat_number') is-invalid @enderror"
                                        placeholder="310540530300003">
                                    @error('vat_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('message.vat_image')
                                </label>
                                <div class="p-3 border rounded-3 bg-light bg-opacity-50">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        @if (!empty($settings->vat_image))
                                            <div class="bg-white p-2 rounded shadow-sm border">
                                                <img src="{{ asset('images/settings/' . $settings->vat_image) }}?v={{ time() }}"
                                                    alt="VAT" style="height: 50px; width: 50px; object-fit: contain;">
                                            </div>
                                        @else
                                            <div class="bg-white p-2 rounded shadow-sm border d-flex align-items-center justify-content-center"
                                                style="height: 68px; width: 68px;">
                                                <i class="bi bi-image text-muted fs-3"></i>
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <input type="file" name="vat_image"
                                                class="form-control form-control-sm border-0 shadow-sm @error('vat_image') is-invalid @enderror"
                                                accept="image/*">
                                            @error('vat_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <small class="text-muted d-block"><i class="bi bi-info-circle me-1"></i>
                                        @lang('message.legal_image_recommendation')</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-light border mt-4 mb-0 small text-muted">
                            <i class="bi bi-eye me-1"></i> @lang('message.legal_identifiers_footer_note')
                        </div>
                    </div>
                </div>

                <!-- 3. SMTP SETTINGS -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class=" bg-opacity-10 text-info rounded-3 p-2 me-3">
                                <i class="bi bi-send-check-fill fs-5"></i>
                            </div>
                            <h5 class="mb-0 fw-bold text-dark">@lang('message.smtp_settings')</h5>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label
                                    class="form-label small fw-bold text-muted text-uppercase tracking-wider">@lang('message.smtp_host')</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-muted"><i
                                            class="bi bi-server"></i></span>
                                    <input type="text" name="smtp_host"
                                        value="{{ old('smtp_host', $settings->smtp_host ?? '') }}"
                                        class="form-control bg-light border-0 py-2" placeholder="smtp.gmail.com">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label
                                    class="form-label small fw-bold text-muted text-uppercase tracking-wider">@lang('message.smtp_port')</label>
                                <input type="number" name="smtp_port"
                                    value="{{ old('smtp_port', $settings->smtp_port ?? '587') }}"
                                    class="form-control bg-light border-0 py-2" placeholder="587">
                            </div>

                            <div class="col-md-3">
                                <label
                                    class="form-label small fw-bold text-muted text-uppercase tracking-wider">@lang('message.encryption')</label>
                                <select name="smtp_encryption" class="form-select bg-light border-0 py-2">
                                    <option value="tls"
                                        {{ old('smtp_encryption', $settings->smtp_encryption ?? 'tls') == 'tls' ? 'selected' : '' }}>
                                        TLS</option>
                                    <option value="ssl"
                                        {{ old('smtp_encryption', $settings->smtp_encryption ?? 'tls') == 'ssl' ? 'selected' : '' }}>
                                        SSL</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label
                                    class="form-label small fw-bold text-muted text-uppercase tracking-wider">@lang('message.smtp_username')</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-muted"><i
                                            class="bi bi-person-badge"></i></span>
                                    <input type="text" name="smtp_username"
                                        value="{{ old('smtp_username', $settings->smtp_username ?? '') }}"
                                        class="form-control bg-light border-0 py-2">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label
                                    class="form-label small fw-bold text-muted text-uppercase tracking-wider">@lang('message.smtp_password')</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-muted"><i
                                            class="bi bi-key"></i></span>
                                    <input type="password" name="smtp_password"
                                        value="{{ old('smtp_password', $settings->smtp_password ?? '') }}"
                                        class="form-control bg-light border-0 py-2" placeholder="@lang('message.leave_empty_to_keep')">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 4. SEO -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class=" bg-opacity-10 text-warning rounded-3 p-2 me-3">
                                <i class="bi bi-search me-1 fs-5"></i>
                            </div>
                            <h5 class="mb-0 fw-bold text-dark">@lang('message.seo_settings')</h5>
                        </div>

                        <div class="row g-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                        @lang('message.seo_title') <span class="text-danger">*</span>
                                    </label>
                                    <span class="badge bg-light text-dark fw-normal border">60-70 chars</span>
                                </div>
                                <input type="text" name="seo_title"
                                    value="{{ old('seo_title', $settings->seo_title ?? '') }}"
                                    class="form-control bg-light border-0 py-2 shadow-sm" placeholder="@lang('message.seo_title_placeholder')"
                                    required>
                                <div class="form-text mt-2 ms-1">
                                    <i class="bi bi-info-circle me-1"></i> @lang('message.seo_title_note')
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                        @lang('message.seo_description') <span class="text-danger">*</span>
                                    </label>
                                    <span class="badge bg-light text-dark fw-normal border">150-160 chars</span>
                                </div>
                                <textarea name="seo_description" class="form-control bg-light border-0 shadow-sm" rows="3"
                                    placeholder="@lang('message.seo_description_placeholder')" required>{{ old('seo_description', $settings->seo_description ?? '') }}</textarea>
                                <div class="form-text mt-2 ms-1">
                                    <i class="bi bi-megaphone me-1"></i> @lang('message.seo_description_note')
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('message.seo_keywords')
                                </label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-light border-0 text-muted">
                                        <i class="bi bi-tags"></i>
                                    </span>
                                    <input type="text" name="seo_keywords"
                                        value="{{ old('seo_keywords', $settings->seo_keywords ?? '') }}"
                                        class="form-control bg-light border-0 py-2" placeholder="@lang('message.seo_keywords_placeholder')">
                                </div>
                                <div class="form-text mt-2 ms-1 text-info">
                                    <i class="bi bi-lightbulb me-1"></i> @lang('message.seo_keywords_note')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. PAYMENT (TELR) -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center">
                                <div class=" bg-opacity-10 text-danger rounded-3 p-2 me-3">
                                    <i class="bi bi-credit-card-2-front-fill fs-5"></i>
                                </div>
                                <h5 class="mb-0 fw-bold text-dark">@lang('message.payment_telr')</h5>
                            </div>
                            <a href="https://docs.telr.com/reference/get-started" target="_blank"
                                class="btn btn-sm btn-outline-secondary border-0 bg-light rounded-pill px-3">
                                <i class="bi bi-book me-1"></i> @lang('message.documentation')
                            </a>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('message.telr_store_id')
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-muted">
                                        <i class="bi bi-shop"></i>
                                    </span>
                                    <input type="text" name="telr_store_id"
                                        value="{{ old('telr_store_id', $settings->telr_store_id ?? '') }}"
                                        class="form-control bg-light border-0 py-2" placeholder="@lang('message.your_store_id')">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('message.telr_auth_key')
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-muted">
                                        <i class="bi bi-shield-lock"></i>
                                    </span>
                                    <input type="password" name="telr_auth_key"
                                        value="{{ old('telr_auth_key', $settings->telr_auth_key ?? '') }}"
                                        class="form-control bg-light border-0 py-2" placeholder="@lang('message.your_auth_key')">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('message.mode')
                                </label>
                                <div class="d-flex gap-3 mt-1">
                                    <div class="form-check flex-fill p-3 border rounded-3 bg-light bg-opacity-50">
                                        <input class="form-check-input ms-0 me-2" type="radio" name="telr_mode"
                                            id="modeTest" value="test"
                                            {{ old('telr_mode', $settings->telr_mode ?? 'test') == 'test' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="modeTest">
                                            <i class="bi bi-bug me-1"></i> @lang('message.test')
                                        </label>
                                    </div>
                                    <div class="form-check flex-fill p-3 border rounded-3 bg-light bg-opacity-50">
                                        <input class="form-check-input ms-0 me-2" type="radio" name="telr_mode"
                                            id="modeLive" value="live"
                                            {{ old('telr_mode', $settings->telr_mode ?? 'test') == 'live' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold text-danger" for="modeLive">
                                            <i class="bi bi-lightning-fill me-1"></i> @lang('admin.live_mode')
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('message.currency')
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-muted">
                                        <i class="bi bi-currency-exchange"></i>
                                    </span>

                                    <select name="currency" class="form-select bg-light border-0 py-2">
                                        <option value="SAR"
                                            {{ old('currency', $settings->currency ?? 'SAR') == 'SAR' ? 'selected' : '' }}>
                                            SAR </option>
                                        <option value="USD"
                                            {{ old('currency', $settings->currency ?? 'SAR') == 'USD' ? 'selected' : '' }}>
                                            USD </option>
                                        <option value="EUR"
                                            {{ old('currency', $settings->currency ?? 'SAR') == 'EUR' ? 'selected' : '' }}>
                                            EUR </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 6. SHIPPING (OTO) -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-body p-4">

                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center">
                                <div class=" bg-opacity-10 text-secondary rounded-3 p-2 me-3">
                                    <i class="bi bi-truck fs-5"></i>
                                </div>
                                <h5 class="mb-0 fw-bold text-dark">
                                    @lang('admin.oto_settings')
                                </h5>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" id="testOtoBtn"
                                    class="btn btn-sm btn-outline-primary border-0 bg-light rounded-pill px-3">
                                    <i class="bi bi-plug me-1"></i> @lang('admin.test_connection')
                                </button>
                                <a href="https://apis.tryoto.com" target="_blank"
                                    class="btn btn-sm btn-outline-secondary border-0 bg-light rounded-pill px-3">
                                    <i class="bi bi-book me-1"></i>
                                    @lang('admin.oto_docs')
                                </a>
                            </div>
                        </div>

                        <div class="row g-4">

                            <!-- OTO CLIENT ID -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('admin.oto_client_id')
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-muted">
                                        <i class="bi bi-key"></i>
                                    </span>
                                    <input type="text" name="oto_client_id"
                                        value="{{ old('oto_client_id', $settings->oto_client_id ?? '') }}"
                                        class="form-control bg-light border-0 py-2">
                                </div>
                            </div>

                            <!-- OTO CLIENT SECRET -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('admin.oto_client_secret')
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-muted">
                                        <i class="bi bi-shield-lock"></i>
                                    </span>
                                    <input type="password" name="oto_client_secret"
                                        value="{{ old('oto_client_secret', $settings->oto_client_secret ?? '') }}"
                                        class="form-control bg-light border-0 py-2" placeholder="@lang('admin.leave_empty_to_keep')">
                                </div>
                            </div>

                            <!-- OTO WAREHOUSE NAME -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('admin.warehouse_name') <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="oto_warehouse_name"
                                    value="{{ old('oto_warehouse_name', $settings->oto_warehouse_name ?? '') }}"
                                    class="form-control bg-light border-0 py-2"
                                    placeholder="e.g. Al Bashawri Warehouse">
                            </div>

                            <!-- OTO WAREHOUSE PHONE -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('admin.warehouse_phone') <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-muted">
                                        <i class="bi bi-telephone"></i>
                                    </span>
                                    <input type="text" name="oto_warehouse_phone"
                                        value="{{ old('oto_warehouse_phone', $settings->oto_warehouse_phone ?? '') }}"
                                        class="form-control bg-light border-0 py-2"
                                        placeholder="+966 5X XXX XXXX">
                                </div>
                            </div>

                            <!-- OTO WAREHOUSE ADDRESS -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('admin.warehouse_address')
                                </label>
                                <input type="text" name="oto_warehouse_address"
                                    value="{{ old('oto_warehouse_address', $settings->oto_warehouse_address ?? '') }}"
                                    class="form-control bg-light border-0 py-2"
                                    placeholder="Street, District">
                            </div>

                            <!-- OTO WAREHOUSE CODE -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('admin.warehouse_code')
                                </label>
                                <input type="text" name="oto_warehouse_code"
                                    value="{{ old('oto_warehouse_code', $settings->oto_warehouse_code ?? '') }}"
                                    class="form-control bg-light border-0 py-2"
                                    placeholder="e.g. WH-001 (optional)">
                            </div>

                            <!-- OTO MODE -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('admin.oto_mode')
                                </label>
                                <div class="d-flex gap-3">

                                    <div class="form-check flex-fill p-3 border rounded-3 bg-light bg-opacity-50">
                                        <input class="form-check-input ms-0 me-2" type="radio" name="oto_mode"
                                            value="test"
                                            {{ old('oto_mode', $settings->oto_mode ?? 'test') == 'test' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold">
                                            <i class="bi bi-bug me-1"></i>
                                            @lang('admin.test_mode')
                                        </label>
                                    </div>

                                    <div class="form-check flex-fill p-3 border rounded-3 bg-light bg-opacity-50">
                                        <input class="form-check-input ms-0 me-2" type="radio" name="oto_mode"
                                            value="live"
                                            {{ old('oto_mode', $settings->oto_mode ?? 'test') == 'live' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold text-danger">
                                            <i class="bi bi-lightning-fill me-1"></i>
                                            @lang('admin.live_mode')
                                        </label>
                                    </div>

                                </div>
                            </div>

                            <!-- DEFAULT WAREHOUSE CITY -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('admin.warehouse_city')
                                </label>
                                <input type="text" name="oto_warehouse_city"
                                    value="{{ old('oto_warehouse_city', $settings->oto_warehouse_city ?? 'Casablanca') }}"
                                    class="form-control bg-light border-0 py-2">
                            </div>

                            <!-- FREE SHIPPING THRESHOLD -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-wider">
                                    @lang('admin.free_shipping_from')
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="free_shipping_threshold"
                                        value="{{ old('free_shipping_threshold', $settings->free_shipping_threshold ?? '0') }}"
                                        class="form-control bg-light border-0 py-2">
                                    <span class="input-group-text bg-light border-0 text-muted">
                                        {{ $settings->currency ?? 'MAD' }}
                                    </span>
                                </div>
                            </div>

                            <!-- INFO -->
                            <div class="col-12">
                                <div class="p-2 bg-info bg-opacity-10 rounded-3 d-inline-block px-3">
                                    <small class="text-info fw-medium">
                                        <i class="bi bi-info-circle-fill me-1"></i>
                                        @lang('admin.oto_auto_shipping_info')
                                    </small>
                                </div>
                                <div id="oto-test-result"></div>
                            </div>

                        </div>
                    </div>
                </div>



                <!-- SUBMIT BUTTON -->
                <div class="card border-0 shadow-sm mb-5 sticky-bottom"
                    style="border-radius: 20px; background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border: 1px solid rgba(0,0,0,0.05) !important;">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded-circle p-2 me-3">
                                <i class="bi bi-clock-history text-muted"></i>
                            </div>
                            <div class="lh-1">
                                <p class="text-muted x-small text-uppercase fw-bold mb-1"
                                    style="font-size: 0.65rem; letter-spacing: 0.05em;">@lang('admin.last_updated')</p>
                                <span
                                    class="text-dark fw-medium small">{{ $settings->updated_at->diffForHumans() ?? 'n/a' }}</span>
                            </div>
                        </div>

                        <button type="submit"
                            class="btn btn-dark btn-lg px-5 rounded-pill shadow-lg hover-lift transition-all"
                            style="font-weight: 600; font-size: 0.95rem;">
                            <i class="bi bi-shield-check me-2"></i> @lang('message.save_all_settings')
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    @endpush

    @push('scripts')
    <script>
    document.getElementById('testOtoBtn')?.addEventListener('click', function () {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Testing...';

        fetch('{{ route('admin.settings.test-oto') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    || '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            const icon  = data.success ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger';
            const color = data.success ? 'success' : 'danger';
            const msg   = data.message ?? (data.success ? 'Connected!' : 'Failed');
            const extra = data.token ? ` <small class="text-muted">(token: ${data.token})</small>` : '';
            const mode  = data.mode ? ` — mode: <strong>${data.mode}</strong>` : '';

            const alert = `<div class="alert alert-${color} alert-dismissible fade show mt-3">
                <i class="bi ${icon} me-2"></i>${msg}${mode}${extra}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;

            document.getElementById('oto-test-result').innerHTML = alert;
        })
        .catch(err => {
            document.getElementById('oto-test-result').innerHTML =
                `<div class="alert alert-danger mt-3">Network error: ${err.message}</div>`;
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-plug me-1"></i> @lang('admin.test_connection')';
        });
    });
    </script>
    @endpush

@endsection
