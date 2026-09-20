@extends('layouts.admin')

@section('title', 'Edit Pixel')

@section('content')
    <div class="container-fluid py-4" style="max-width: 1000px;">

        {{-- HEADER --}}
        <div class="row align-items-center mb-4 g-3">
            <div class="col-md-8">
                <h1 class="fw-bold h4 mb-1 text-dark">@lang('admin.Edit Pixel')</h1>
                <p class="text-muted small mb-0">@lang('admin.Update tracking pixel settings')</p>
            </div>
            <div class="col-md-4 d-flex justify-content-md-end gap-2">
                <a href="{{ route('admin.pixels.index') }}" class="btn btn-light rounded-pill px-4">
                    <i class="bi bi-arrow-left me-2"></i>@lang('admin.Back')
                </a>
            </div>
        </div>

        {{-- FORM --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-4 p-lg-5">

                <form method="POST" action="{{ route('admin.pixels.update', $pixel->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        {{-- PROVIDER --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="provider" class="form-select bg-light border-0" id="provider">
                                    @php
                                        $provider = old('provider', $pixel->provider);
                                    @endphp
                                    <option value="facebook" {{ $provider === 'facebook' ? 'selected' : '' }}>Facebook</option>
                                    <option value="tiktok" {{ $provider === 'tiktok' ? 'selected' : '' }}>TikTok</option>
                                    <option value="google" {{ $provider === 'google' ? 'selected' : '' }}>Google</option>
                                </select>
                                 @error('provider')
                                    <div style="color: rgb(187, 25, 25)">{{ $message }}</div>
                                @enderror
                                <label for="provider" class="text-muted small">@lang('admin.Provider')</label>
                            </div>
                            <div class="form-text small text-muted mt-2">
                                @lang('admin.Select the platform you want to track')
                            </div>
                        </div>

                        {{-- STATUS --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                @php
                                    $isActive = old('is_active', (string) $pixel->is_active);
                                @endphp
                                <select name="is_active" class="form-select bg-light border-0" id="is_active">
                                    <option value="1" {{ $isActive == '1' ? 'selected' : '' }}>@lang('admin.Active')</option>
                                    <option value="0" {{ $isActive == '0' ? 'selected' : '' }}>@lang('admin.Inactive')</option>
                                </select>
                                @error('is_active')
                                    <div style="color: red">{{ $message }}</div>
                                @enderror
                                <label for="is_active" class="text-muted small">@lang('admin.Status')</label>
                            </div>
                            <div class="form-text small text-muted mt-2">
                                @lang('admin.You can disable it anytime without deleting')
                            </div>
                        </div>

                        {{-- NAME --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="name"
                                    value="{{ old('name', $pixel->name) }}"
                                    class="form-control bg-light border-0" id="name" placeholder="Pixel name">
                                    @error('name')
                                    <div style="color: red">{{ $message }}</div>
                                @enderror
                                <label for="name" class="text-muted small">@lang('admin.Name (optional)')</label>
                            </div>
                        </div>

                        {{-- PIXEL ID --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="pixel_id"
                                    value="{{ old('pixel_id', $pixel->pixel_id) }}"
                                    class="form-control bg-light border-0" id="pixel_id" placeholder="Pixel ID">
                                     @error('pixel_id')
                                    <div style="color: red">{{ $message }}</div>
                                @enderror
                                <label for="pixel_id" class="text-muted small">@lang('admin.Pixel ID')</label>
                            </div>
                            <div class="form-text small text-muted mt-2">
                                @lang('admin.Example'): <span class="fw-semibold">1234567890</span>
                            </div>
                        </div>

                        {{-- EVENTS --}}
                        <div class="col-12">
                            <div class="p-3 p-lg-4 bg-light rounded-4 border-0">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div>
                                        <div class="fw-semibold text-dark">@lang('admin.Events')</div>
                                        <div class="text-muted small">@lang('admin.Select which events you want to track')</div>
                                    </div>
                                    <span class="badge bg-white text-dark border fw-medium">
                                        <i class="bi bi-lightning-charge me-1"></i>@lang('admin.Recommended')
                                    </span>
                                </div>

                                @php
                                    // If events stored as JSON/array cast => this works
                                    $selectedEvents = old('events', $pixel->events ?? []);
                                    $selectedEvents = is_array($selectedEvents) ? $selectedEvents : (array) $selectedEvents;
                                @endphp

                                <div class="row g-3 mt-1">
                                    @foreach ([
                                        'pageview' => 'PageView',
                                        'view_content' => 'ViewContent',
                                        'add_to_cart' => 'AddToCart',
                                        'purchase' => 'Purchase',
                                        'initiate_checkout' => 'InitiateCheckout',
                                    ] as $key => $label)
                                        <div class="col-md-4 col-sm-6">
                                            <label class="d-flex align-items-center gap-2 p-3 bg-white rounded-4 border shadow-xs w-100">
                                                <input class="form-check-input m-0" type="checkbox" name="events[]"
                                                    value="{{ $key }}" {{ in_array($key, $selectedEvents) ? 'checked' : '' }}>
                                                @error('events')
                                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                                @enderror
                                                <span class="fw-medium">{{ $label }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- ACTIONS --}}
                        <div class="col-12 d-flex justify-content-end gap-2 pt-2">
                            <a href="{{ route('admin.pixels.index') }}" class="btn btn-light rounded-pill px-4">
                                @lang('admin.Cancel')
                            </a>
                            <button type="submit" class="btn btn-primary-custom rounded-pill px-4 shadow-sm">
                                <i class="bi bi-check2-circle me-2"></i>@lang('admin.Update')
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <style>
        :root {
            --primary-color: #2E3192;
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            border: none;
            color: white;
            transition: all 0.3s;
        }

        .btn-primary-custom:hover {
            background-color: #1e2163;
            color: white;
            transform: translateY(-1px);
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #fff !important;
            border: 1px solid #2E3192 !important;
            box-shadow: none;
        }

        .shadow-xs {
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
    </style>
@endsection
