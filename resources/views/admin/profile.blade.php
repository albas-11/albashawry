@extends('layouts.admin')

@section('title', 'Admin Profile')

@section('content')
<div class="container-fluid">

    <div class="mb-4 d-flex justify-content-between align-items-end">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item small"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted"> @lang('admin.Dashboard')</a></li>
                    <li class="breadcrumb-item small active" aria-current="page">@lang('admin.Account Settings') </li>
                </ol>
            </nav>
            <h1 class="fw-bold mb-0" style="font-size:1.6rem; color: #2E3192;">@lang('admin.My Profile')</h1>
        </div>

        <button class="btn btn-outline-warning border-2 fw-bold d-flex align-items-center gap-2 px-3 py-2"
                data-bs-toggle="modal"
                data-bs-target="#changePasswordModal"
                style="border-radius: 10px;">
            <i class="bi bi-shield-lock"></i>
            @lang('admin.Change Password')
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4 text-center p-4" style="border-radius: 15px;">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle shadow-sm"
                     style="width: 100px; height: 100px; background: #f8f9ff; color: #2E3192; font-size: 2.5rem; font-weight: 800; border: 4px solid #fff;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <p class="text-muted small mb-3">{{ $user->email }}</p>
                <span class="badge bg-light text-primary px-3 py-2 rounded-pill">@lang('admin.Administrator')</span>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-person-gear me-2 text-primary"></i>@lang('admin.Profile Information')</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.profile.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-uppercase text-muted">@lang('admin.Name')</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-person text-muted"></i></span>
                                    <input type="text" name="name"
                                           class="form-control bg-light border-0 @error('name') is-invalid @enderror"
                                           value="{{ old('name', $user->name) }}" style="padding: 0.6rem;">
                                </div>
                                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-uppercase text-muted">@lang('admin.Email Address')</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-envelope text-muted"></i></span>
                                    <input type="email" name="email"
                                           class="form-control bg-light border-0 @error('email') is-invalid @enderror"
                                           value="{{ old('email', $user->email) }}" style="padding: 0.6rem;">
                                </div>
                                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                            <button class="btn px-5 py-2 fw-bold shadow-sm"
                                    style="background-color:#2E3192; color:white; border-radius: 10px;">
                               @lang('admin.Save Changes')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">@lang('admin.Update Security')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('admin.profile.password') }}">
                @csrf
                @method("PUT")
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">@lang('admin.Current Password')</label>
                        <input type="password" name="current_password" class="form-control form-control-lg @error('current_password') is-invalid @enderror" style="font-size: 0.9rem; border-radius: 10px;">
                        @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">@lang('admin.New Password')</label>
                        <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" style="font-size: 0.9rem; border-radius: 10px;">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">@lang('admin.Confirm New Password')</label>
                        <input type="password" name="password_confirmation" class="form-control form-control-lg" style="font-size: 0.9rem; border-radius: 10px;">
                    </div>
                </div>

                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 10px;">@lang('admin.Cancel')</button>
                    <button class="btn btn-warning px-4 fw-bold" style="border-radius: 10px;">@lang('admin.Update Password')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
