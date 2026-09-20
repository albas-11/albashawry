@extends('layouts.admin')

@section('title', __('admin.Users Management'))

@section('content')
    <div class="container-fluid py-4">

        {{-- HEADER SECTION --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <h1 class="fw-bold h4 mb-1 text-dark">@lang('admin.Users Management')</h1>
                <p class="text-muted small mb-0">
                    @lang('admin.Total database'): <strong>{{ $users->total() }}</strong> @lang('admin.registered users')
                </p>
            </div>

            <div class="d-flex gap-1">
                {{-- SEARCH BAR --}}
                <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-1">
                    <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control border-0 bg-white px-3" placeholder="@lang('admin.Search name, email or phone')"
                            style="min-width: 280px;">
                        <button class="btn btn-white border-0 bg-white text-muted" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

                <button type="button" class="btn px-4 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal"
                    data-bs-target="#createUserModal" style="background-color: #2E3192; color: white;">
                    <i class="bi bi-person-plus-fill"></i>
                    <span>@lang('admin.Add user')</span>
                </button>
            </div>
        </div>

        {{-- USERS TABLE CARD --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted small fw-bold text-uppercase">@lang('admin.User')</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">@lang('admin.Contact')</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase text-center">@lang('admin.Role')</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">@lang('admin.Created')</th>
                            <th class="pe-4 py-3 text-muted small fw-bold text-uppercase text-end">@lang('admin.Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="avatar-sm me-3 d-flex align-items-center justify-content-center rounded-circle bg-secondary bg-opacity-10 text-secondary fw-bold">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                                            <div class="text-muted x-small">ID: <span
                                                    class="text-primary">#{{ $user->id }}</span></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark small"><i
                                                class="bi bi-envelope me-1 opacity-50"></i>{{ $user->email }}</span>
                                        <span class="text-muted x-small"><i
                                                class="bi bi-telephone me-1 opacity-50"></i>{{ $user->num ?? __('admin.No phone') }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @php
                                        $roleStyle = match (strtolower($user->role->name ?? '')) {
                                            'admin' => 'badge bg-danger bg-opacity-10 text-danger',
                                            'user' => 'badge bg-warning bg-opacity-10 text-warning',
                                            default => 'badge bg-primary bg-opacity-10 text-primary',
                                        };
                                    @endphp
                                    <span class="{{ $roleStyle }} rounded-pill px-3 py-1 fw-medium"
                                        style="font-size: 0.7rem;">
                                        {{ $user->role->name ?? __('admin.user') }}
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>

                                {{-- ACTIONS (UPDATED like Orders) --}}
                                <td class="pe-4 py-3 text-end">
                                    <div class="d-inline-flex gap-2">

                                        {{-- View --}}
                                        <a href="{{ route('admin.users.show', $user) }}"
                                            class="btn btn-action btn-action-secondary" title="@lang('admin.View Profile')"
                                            aria-label="@lang('admin.View Profile')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 16 16" fill="currentColor">
                                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                                <path
                                                    d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                            </svg>
                                        </a>

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="btn btn-action btn-action-secondary" title="@lang('admin.Edit Profile')"
                                            aria-label="@lang('admin.Edit Profile')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 16 16" fill="currentColor">
                                                <path
                                                    d="M15.502 1.94a.5.5 0 0 1 0 .706l-1.793 1.793-2.121-2.12L13.381.525a.5.5 0 0 1 .707 0l1.414 1.414z" />
                                                <path
                                                    d="M12.794 5.207 10.672 3.086 4.939 8.818a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l5.855-5.612z" />
                                                <path fill-rule="evenodd"
                                                    d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a.5.5 0 0 0 0-1h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 0-1 0v11z" />
                                            </svg>
                                        </a>

                                        {{-- Delete --}}
                                        @if (auth()->id() !== $user->id)
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="btn btn-action btn-action-danger btn-delete-user"
                                                    title="@lang('admin.Delete User')" aria-label="@lang('admin.Delete User')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 16 16" fill="currentColor">
                                                        <path
                                                            d="M2.5 1a1 1 0 0 0-1 1v1h13V2a1 1 0 0 0-1-1h-11zm1 4v8a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1V5h-9z" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="opacity-25 mb-3">
                                        <i class="bi bi-people-x" style="font-size: 3rem;"></i>
                                    </div>
                                    <p class="text-muted fw-medium">@lang('admin.No users found matching your search')</p>
                                    <a href="{{ route('admin.users.index') }}"
                                        class="btn btn-sm btn-light border">@lang('admin.Clear Search')</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-4 d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                @lang('admin.Showing') {{ $users->firstItem() }} @lang('admin.to') {{ $users->lastItem() }}
                @lang('admin.of') {{ $users->total() }} @lang('admin.users')
            </div>
            <div>
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    {{-- MODAL CREATE USER --}}
    <div class="modal fade no-scroll" id="createUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 p-4 pb-0">
                        <h5 class="fw-bold"><i class="bi bi-person-plus text-primary-custom me-2"></i>@lang('admin.create_user')
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">@lang('admin.name')</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i
                                            class="bi bi-person text-muted small"></i></span>
                                    <input type="text" name="name" class="form-control border-0 bg-light py-2"
                                        placeholder="Full Name" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">@lang('admin.num')</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i
                                            class="bi bi-telephone text-muted small"></i></span>
                                    <input type="text" name="num" class="form-control border-0 bg-light py-2"
                                        placeholder="Phone Number">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">@lang('admin.email')</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i
                                            class="bi bi-envelope text-muted small"></i></span>
                                    <input type="email" name="email" class="form-control border-0 bg-light py-2"
                                        placeholder="email@example.com" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">@lang('admin.password')</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i
                                            class="bi bi-lock text-muted small"></i></span>
                                    <input type="password" name="password" class="form-control border-0 bg-light py-2"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">@lang('admin.confirm_password')</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i
                                            class="bi bi-shield-lock text-muted small"></i></span>
                                    <input type="password" name="password_confirmation"
                                        class="form-control border-0 bg-light py-2" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">@lang('admin.role')</label>
                                <select name="role_id" class="form-select border-0 bg-light py-2 fw-medium"
                                    id="roleSelect">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" data-name="{{ strtolower($role->name) }}">
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- PERMISSIONS BOX --}}
                            <div class="col-12" id="permissions-container" style="display: none;">
                                <div class="card border-0 shadow-sm bg-light rounded-3">
                                    <div class="card-header bg-transparent border-0 pt-3 px-4">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h6 class="fw-bold mb-0">
                                                <i
                                                    class="bi bi-shield-check text-primary-custom me-2"></i>@lang('admin.assign_permissions')
                                            </h6>
                                            <span class="badge bg-white text-dark shadow-sm" id="permissionCount">0
                                                @lang('admin.selected')</span>
                                        </div>
                                        <p class="x-small text-muted mt-1 mb-0">@lang('admin.Only for the "User" role')</p>
                                    </div>

                                    <div class="card-body p-4">
                                        <div class="row g-3">
                                            @foreach ($groupedPermissions as $key => $group)
                                                @if (count($group['permissions']) > 0)
                                                    <div class="col-lg-4 col-md-6">
                                                        <div
                                                            class="permission-card bg-white rounded-3 p-3 h-100 shadow-sm border border-light">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <div
                                                                    class="permission-icon-sm me-2 bg-light rounded-circle text-primary-custom">
                                                                    @switch($key)
                                                                        @case('products')
                                                                            <i class="bi bi-box small"></i>
                                                                        @break

                                                                        @case('categories')
                                                                            <i class="bi bi-tags small"></i>
                                                                        @break

                                                                        @case('orders')
                                                                            <i class="bi bi-cart small"></i>
                                                                        @break

                                                                        @case('users')
                                                                            <i class="bi bi-person-badge small"></i>
                                                                        @break

                                                                        @default
                                                                            <i class="bi bi-gear small"></i>
                                                                    @endswitch
                                                                </div>
                                                                <h6 class="x-small fw-bold mb-0 text-dark">
                                                                    {{ $group['title'] }}</h6>
                                                            </div>

                                                            <div class="permission-list mb-3">
                                                                @foreach ($group['permissions'] as $permission)
                                                                    <div class="form-check mb-1">
                                                                        <input class="form-check-input permission-checkbox"
                                                                            type="checkbox" name="permissions[]"
                                                                            value="{{ $permission->id }}"
                                                                            id="perm_{{ $permission->id }}">
                                                                        <label class="form-check-label x-small text-muted"
                                                                            for="perm_{{ $permission->id }}">
                                                                            {{ ucfirst(str_replace('-', ' ', $permission->name)) }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>

                                                            <button type="button"
                                                                class="btn btn-link btn-sm p-0 text-decoration-none x-small select-group"
                                                                data-group="{{ $key }}">
                                                                @lang('admin.select_all')
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>

                                        <div class="d-flex gap-2 mt-4 pt-3 border-top border-secondary border-opacity-10">
                                            <button type="button" class="btn btn-xs btn-outline-primary-custom"
                                                id="selectAllPermissions">@lang('admin.select_all')</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary"
                                                id="deselectAllPermissions">@lang('admin.deselect_all')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div
                                    class="form-check form-switch rounded-3 p-3 bg-light d-inline-flex align-items-center gap-3 w-100">
                                    <input class="form-check-input ms-0 mt-0" type="checkbox" name="is_active"
                                        value="1" id="activeSwitch" checked>
                                    <label class="form-check-label small fw-bold text-dark mb-0"
                                        for="activeSwitch">@lang('admin.Active Account')</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light px-4"
                            data-bs-dismiss="modal">@lang('admin.cancel')</button>
                        <button type="submit" class="btn btn-primary-custom px-4 shadow-sm">@lang('admin.create_user')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        :root {
            --primary-custom: #2E3192;
        }

        .text-primary-custom {
            color: var(--primary-custom) !important;
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
            color: var(--primary-custom);
            border-color: var(--primary-custom);
        }

        .btn-outline-primary-custom:hover {
            background: var(--primary-custom);
            color: white;
        }

        .x-small {
            font-size: 0.72rem;
        }

        .btn-xs {
            padding: 0.25rem 0.6rem;
            font-size: 0.7rem;
            border-radius: 4px;
        }

        .permission-icon-sm {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .permission-card {
            transition: border-color 0.2s;
        }

        .permission-card:hover {
            border-color: var(--primary-custom) !important;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #fff !important;
            box-shadow: 0 0 0 3px rgba(46, 49, 146, 0.1) !important;
            border-color: var(--primary-custom) !important;
        }

        .form-switch .form-check-input:checked {
            background-color: var(--primary-custom);
            border-color: var(--primary-custom);
        }

        .avatar-sm {
            width: 40px;
            height: 40px;
            font-size: 0.9rem;
        }

        .table thead th {
            border-bottom: none;
            letter-spacing: 0.05em;
        }

        .btn-white {
            background: #fff;
            border: 1px solid #eee;
        }

        .btn-white:hover {
            background: #f8f9fa;
        }

        .badge {
            font-size: 0.75rem;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .pagination {
            margin-bottom: 0;
        }

        /* ===== SAME AS ORDERS: Action Buttons ===== */
        .btn-action {
            width: 38px;
            height: 38px;
            padding: 0;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid;
            background: #fff;
            transition: all .2s ease;
        }

        .btn-action svg {
            display: block;
        }

        .btn-action-secondary {
            color: #6c757d;
            border-color: #dee2e6;
        }

        .btn-action-secondary:hover {
            background: #f8f9fa;
            border-color: #cfd4da;
            color: #495057;
        }

        .btn-action-danger {
            color: #dc3545;
            border-color: rgba(220, 53, 69, .35);
        }

        .btn-action-danger:hover {
            background: rgba(220, 53, 69, .08);
            border-color: rgba(220, 53, 69, .55);
            color: #dc3545;
        }

        .btn-action:focus {
            box-shadow: 0 0 0 .2rem rgba(46, 49, 146, .15);
        }

        /* Optional: pagination style */
        .pagination .page-link {
            border-radius: 12px !important;
            margin: 0 3px;
            border-color: #dee2e6;
            color: #6c757d;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-custom);
            border-color: var(--primary-custom);
            color: #fff;
        }

        .pagination .page-link:focus {
            box-shadow: 0 0 0 .2rem rgba(46, 49, 146, .15);
        }

        .no-scroll {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .no-scroll::-webkit-scrollbar {
            display: none;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // SweetAlert delete confirm for users
        document.querySelectorAll('.btn-delete-user').forEach(button => {
            button.addEventListener('click', function() {
                Swal.fire({
                    title: "@lang('message.Are you sure?')",
                    text: "Cette action est irréversible.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2E3192',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Oui, supprimer !',
                    cancelButtonText: 'Annuler',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.closest('form').submit();
                    }
                });
            });
        });

        {{-- PERMISSIONS LOGIC --}}
        const roleSelect = document.querySelector('select[name="role_id"]');
        const permContainer = document.getElementById('permissions-container');
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
        const permissionCount = document.getElementById('permissionCount');

        function updatePermissionCount() {
            const checkedCount = document.querySelectorAll('.permission-checkbox:checked').length;
            permissionCount.textContent = checkedCount === 0 ?
                "@lang('admin.no_permissions_selected')" :
                checkedCount + " @lang('admin.permissions_selected')";
        }

        function togglePermissions() {
            const selectedOption = roleSelect.options[roleSelect.selectedIndex];
            const selectedRole = selectedOption.dataset.name;

            if (selectedRole === 'user') {
                permContainer.style.display = 'block';
                permissionCheckboxes.forEach(cb => cb.disabled = false);
                updatePermissionCount();
            } else {
                permContainer.style.display = 'none';
                permissionCheckboxes.forEach(cb => {
                    cb.checked = false;
                    cb.disabled = true;
                });
                permissionCount.textContent = selectedRole === 'admin' ? 'Accès total' : 'Pas d\'accès admin';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            togglePermissions();
            roleSelect.addEventListener('change', togglePermissions);
            permissionCheckboxes.forEach(checkbox => checkbox.addEventListener('change', updatePermissionCount));

            document.getElementById('selectAllPermissions').addEventListener('click', () => {
                permissionCheckboxes.forEach(cb => cb.checked = true);
                updatePermissionCount();
            });

            document.getElementById('deselectAllPermissions').addEventListener('click', () => {
                permissionCheckboxes.forEach(cb => cb.checked = false);
                updatePermissionCount();
            });

            document.querySelectorAll('.select-group').forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.permission-card').querySelectorAll('input').forEach(cb => cb
                        .checked = true);
                    updatePermissionCount();
                });
            });
        });
    </script>
@endsection
