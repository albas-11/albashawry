@extends('layouts.admin')

@section('title', __('admin.User Profile'))

@section('content')
<div class="container-fluid py-4 px-lg-5">

    {{-- HEADER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-muted text-decoration-none">@lang('admin.Users')</a></li>
                    <li class="breadcrumb-item active">@lang('admin.User Profile')</li>
                </ol>
            </nav>
            <p class="text-muted mb-0">@lang('admin.Details and permissions for user') <strong>{{ $user->name }}</strong></p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary-custom px-4 shadow-sm">
                <i class="bi bi-pencil-square me-1"></i> @lang('admin.Edit Profile')
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- LEFT: General Info & Permissions --}}
        <div class="col-lg-8">
            {{-- GENERAL INFO --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">@lang('admin.General Information')</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-muted">@lang('admin.Full Name')</label>
                            <p class="form-control-plaintext">{{ $user->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-muted">@lang('admin.Email Address')</label>
                            <p class="form-control-plaintext">{{ $user->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-muted">@lang('admin.Phone Number')</label>
                            <p class="form-control-plaintext">{{ $user->num ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-muted">@lang('admin.Account Status')</label>
                            <p class="form-control-plaintext">
                                @if($user->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success">@lang('admin.Active')</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger">@lang('admin.Inactive')</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-muted">@lang('admin.Role')</label>
                            @php
                                $roleName = strtolower($user->roles->pluck('name')->first() ?? 'user');
                                $roleClass = match($roleName) {
                                    'admin' => 'badge bg-danger bg-opacity-10 text-danger',
                                    'manager' => 'badge bg-warning bg-opacity-10 text-warning',
                                    default => 'badge bg-primary bg-opacity-10 text-primary',
                                };
                            @endphp
                            <p class="form-control-plaintext"><span class="{{ $roleClass }} px-3 py-1 rounded-pill">{{ ucfirst($roleName) }}</span></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PERMISSIONS --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">@lang('admin.Permissions')</h5>
                    @if($user->permissions->count())
                        <div class="row g-2">
                            @foreach($user->permissions as $permission)
                                <div class="col-md-6">
                                    <span class="badge bg-primary bg-opacity-10 text-primary">{{ $permission->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">@lang('admin.No specific permissions assigned')</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT: User Meta --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 text-center">
                    <h6 class="fw-bold mb-3 small text-uppercase tracking-wider">@lang('admin.User Details')</h6>
                    <p class="mb-1"><strong>@lang('admin.Created at'):</strong> {{ $user->created_at->format('d M Y H:i') }}</p>
                    <p class="mb-1"><strong>@lang('admin.Updated at'):</strong> {{ $user->updated_at->format('d M Y H:i') }}</p>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary-custom: #2E3192;
    }

    .btn-primary-custom {
        background-color: var(--primary-custom);
        color: white;
        border: none;
    }
    .btn-primary-custom:hover {
        background-color: #1e2163;
        color: white;
        transform: translateY(-1px);
    }
    .btn-outline-primary-custom {
        border: 1px solid var(--primary-custom);
        color: var(--primary-custom);
    }
    .btn-outline-primary-custom:hover {
        background: var(--primary-custom);
        color: white;
    }
    body {
        background-color: #f8fafc;
        color: #334155;
    }
    .badge {
        font-size: 0.8rem;
    }
    .card {
        transition: all 0.2s;
    }
    .card:hover {
        transform: translateY(-2px);
    }
</style>
@endsection
