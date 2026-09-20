@extends('layouts.admin')

@section('title', __('admin.Edit User'))

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
<div class="container-fluid py-5 px-lg-5">
    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="needs-validation">
        @csrf
        @method('PUT')

        <div class="row mb-5 align-items-end">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-muted text-decoration-none">@lang('admin.Users')</a></li>
                        <li class="breadcrumb-item active">@lang('admin.Edit Profile')</li>
                    </ol>
                </nav>
                <p class="text-muted mb-0">@lang('admin.Information and access management')  <strong>{{ $user->name }}</strong></p>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary border-0 px-4">
                        @lang('admin.Cancel')
                    </a>
                    <button type="submit" class="btn btn-dark px-4 shadow-sm" style="background-color: #0f408e;">
                        @lang('admin.Update Profile')
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">@lang('admin.General Information')</h5>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-medium text-muted">@lang('admin.Full Name')</label>
                                <input type="text" name="name" class="form-control bg-light border-0 py-2 @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-muted">@lang('admin.Email Address')</label>
                                <input type="email" name="email" class="form-control bg-light border-0 py-2 @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-muted">@lang('admin.Phone Number')</label>
                                <input type="text" name="num" class="form-control bg-light border-0 py-2" value="{{ old('num', $user->num) }}">
                            </div>

                            <div class="col-12 mt-4">
                                <div class="p-3 rounded-3 bg-soft-warning border-start border-warning border-3">
                                    <label class="form-label fw-bold text-dark mb-1 small">@lang('admin.Security Update')</label>
                                    <input type="password" name="password" class="form-control border-0 shadow-none bg-white" placeholder="••••••••">
                                    <div class="form-text x-small text-muted mt-2">
                                        <i class="bi bi-info-circle me-1"></i>@lang('admin.Leave blank if you do not wish to change the password.')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">@lang('admin.Roles and Responsibilities')</h5>
                        <div class="row g-3">
                            @foreach ($roles as $role)
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="role_id" id="role_{{ $role->id }}" value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'checked' : '' }} onchange="togglePermissions()">
                                    <label class="role-card border rounded-4 p-3 w-100 h-100" for="role_{{ $role->id }}">
                                        <div class="d-flex align-items-center">
                                            <div class="role-icon rounded-circle bg-white shadow-sm me-3">
                                                <i class="bi {{ $role->name === 'admin' ? 'bi-shield-lock text-danger' : 'bi-person text-primary' }}"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold text-dark">{{ ucfirst($role->name) }}</div>
                                            </div>
                                            <div class="check-mark">
                                                <i class="bi bi-check-lg text-primary"></i>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 small text-uppercase tracking-wider">@lang('admin.Account Status')</h6>
                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3">
                            <div>
                                <span class="fw-bold d-block">@lang('admin.Enable Access')</span>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input h4 m-0" type="checkbox" name="is_active" id="is_active" {{ $user->is_active ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="permissions-section" class="card border-0 shadow-sm rounded-4 overflow-hidden" style="display: none;">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                        <h6 class="fw-bold mb-0 small text-uppercase tracking-wider">@lang('admin.Specific permissions')</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="accordion accordion-flush" id="accordionPermissions">
                            @foreach ($permissionGroups as $groupName => $group)
                                <div class="accordion-item border-0">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed py-3 px-4 fw-medium small" type="button" data-bs-toggle="collapse" data-bs-target="#group-{{ Str::slug($groupName) }}">
                                            {{ $group['title'] }}
                                        </button>
                                    </h2>
                                    <div id="group-{{ Str::slug($groupName) }}" class="accordion-collapse collapse">
                                        <div class="accordion-body px-4 py-2">
                                            @foreach ($group['permissions'] as $permission)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm_{{ $permission->id }}" {{ in_array($permission->id, $userPermissions) ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    body { background-color: #f8fafc; color: #334155; }

    /* Typo & Utilities */
    .x-small { font-size: 0.75rem; }
    .tracking-wider { letter-spacing: 0.05em; }
    .bg-soft-warning { background-color: #fffbeb; }

    /* Form Elements */
    .form-control:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }
    .form-switch .form-check-input:checked {
        background-color: #10b981;
        border-color: #10b981;
    }

    /* Role Cards */
    .role-card {
        cursor: pointer;
        background-color: #fff;
        transition: all 0.2s ease;
        border-color: #e2e8f0 !important;
    }
    .role-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    .check-mark {
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.2s ease;
    }
    .btn-check:checked + .role-card {
        border-color: #4f46e5 !important;
        background-color: #f5f3ff;
    }
    .btn-check:checked + .role-card .check-mark {
        opacity: 1;
        transform: scale(1);
    }

    /* Accordion Custom */
    .accordion-button:not(.collapsed) {
        background-color: transparent;
        color: #4f46e5;
        box-shadow: none;
    }
    .accordion-button:focus { box-shadow: none; }
</style>

<script>
    function togglePermissions() {
        const permissionsSection = document.getElementById('permissions-section');
        const selectedRoleInput = document.querySelector('input[name="role_id"]:checked');

        if (selectedRoleInput) {
            const roleLabel = selectedRoleInput.nextElementSibling.querySelector('.fw-bold').textContent.toLowerCase();
            if (roleLabel === 'user') {
                permissionsSection.style.display = 'block';
            } else {
                permissionsSection.style.display = 'none';
            }
        }
    }
    document.addEventListener('DOMContentLoaded', () => togglePermissions());
</script>
@endsection
