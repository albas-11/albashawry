@extends('layouts.admin')

@section('title', 'Manage Stores')

@section('content')
    <div class="container-fluid py-4">

        {{-- HEADER SECTION --}}
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
            <div>
                <h1 class="h4 fw-bold mb-1 text-dark">
                    <i class="bi bi-geo-alt-fill text-primary-custom me-2"></i>@lang('admin.Manage Stores')
                </h1>
                <p class="text-muted small mb-0">@lang('admin.Configure and update your retail locations')</p>
            </div>
            <a href="{{ route('admin.stores.create') }}"
                class="btn btn-primary-custom px-4 py-2 rounded-3 shadow-sm d-flex align-items-center justify-content-center">
                <i class="bi bi-plus-lg me-2"></i>@lang('admin.Add Store')
            </a>
        </div>

        {{-- FLASH MESSAGES --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center mb-4"
                role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- SEARCH/FILTER SECTION --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.stores.index') }}">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 ps-3">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <div class="form-floating flex-grow-1">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control bg-light border-0" id="storeSearch"
                                        placeholder="@lang('admin.Search...')">
                                    <label for="storeSearch" class="text-muted small">@lang('admin.Search by name, city or country')</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit"
                                class="btn btn-primary-custom rounded-3 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-filter"></i>
                                <span>@lang('admin.Filter')</span>
                            </button>
                        </div>
                        @if (request('search'))
                            <div class="col-md-2">
                                <a href="{{ route('admin.stores.index') }}"
                                    class="btn btn-light w-100 h-100 rounded-3 py-2 text-muted d-flex align-items-center justify-content-center">
                                    <i class="bi bi-arrow-clockwise me-1"></i> @lang('admin.Reset')
                                </a>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- TABLE SECTION --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase text-muted x-small fw-bold">@lang('admin.Store Name')</th>
                            <th class="py-3 text-uppercase text-muted x-small fw-bold">@lang('admin.Location')</th>
                            <th class="py-3 text-uppercase text-muted x-small fw-bold">@lang('admin.Phone')</th>
                            <th class="py-3 text-uppercase text-muted x-small fw-bold">@lang('admin.Country')</th>
                            <th class="py-3 text-uppercase text-muted x-small fw-bold text-end pe-4">@lang('admin.Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stores as $store)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="avatar-sm me-3 bg-light rounded-3 d-flex align-items-center justify-content-center text-primary-custom">
                                            <i class="bi bi-shop fs-5"></i>
                                        </div>
                                        <span class="fw-bold text-dark">{{ $store->name['en'] ?? $store->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-medium">{{ $store->city }}</span>
                                        <span class="text-muted x-small">ID: #{{ $store->id }}</span>
                                    </div>
                                </td>
                                <td>
                                    <a href="tel:{{ $store->phone }}" class="text-secondary text-decoration-none small">
                                        <i class="bi bi-telephone me-1 opacity-50"></i> {{ $store->phone }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-2 fw-medium bg-light text-dark border">
                                        {{ $store->country }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-2">

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.stores.edit', $store) }}"
                                            class="btn btn-action btn-action-secondary" title="@lang('admin.Edit')"
                                            aria-label="@lang('admin.Edit')">
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
                                        <form action="{{ route('admin.stores.destroy', $store) }}" method="POST"
                                            onsubmit="return confirm('@lang('admin.Are you sure you want to delete this store?')')" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-action btn-action-danger"
                                                title="@lang('admin.Delete')" aria-label="@lang('admin.Delete')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 16 16" fill="currentColor">
                                                    <path
                                                        d="M5.5 5.5A.5.5 0 0 1 6 6v7a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0A.5.5 0 0 1 8.5 6v7a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0V6z" />
                                                    <path
                                                        d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3h11V2h-11v1z" />
                                                </svg>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="opacity-25 mb-3">
                                        <i class="bi bi-geo-fill display-4"></i>
                                    </div>
                                    <h6 class="text-muted fw-light">@lang('admin.No stores found.')</h6>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    
    {{-- PAGINATION --}}
    <div class="mt-2">
        {{ $stores->links('pagination::bootstrap-5') }}
    </div>
    <style>
        /* Variables de couleurs */
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
            box-shadow: 0 4px 12px rgba(46, 49, 146, 0.2);
        }

        .x-small {
            font-size: 0.7rem;
        }

        /* Avatar pour la liste */
        .avatar-sm {
            width: 38px;
            height: 38px;
        }

        /* Styles Table */
        .table thead th {
            border: none;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 1rem 0.5rem;
            border-bottom: 1px solid #f8f9fa;
        }

        /* Floating labels adjustments */
        .form-floating>.form-control:focus~label,
        .form-floating>.form-control:not(:placeholder-shown)~label {
            transform: scale(.85) translateY(-0.5rem) translateX(0.15rem);
        }

        /* Boutons actions */
        .btn-light {
            background: #f8f9fa;
            border: 1px solid #eee;
        }

        .btn-light:hover {
            background: #fff;
            border-color: #ddd;
        }

        /* Pagination-inspired action buttons (same as Orders) */
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

        /* Neutral */
        .btn-action-secondary {
            color: #6c757d;
            border-color: #dee2e6;
        }

        .btn-action-secondary:hover {
            background: #f8f9fa;
            border-color: #cfd4da;
            color: #495057;
        }

        /* Danger */
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

        /* Optional: tighten bootstrap pagination to match your clean look */
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
    </style>

    <script>
    document.querySelectorAll('.btn-delete-confirm').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Delete this store?')) {
                this.closest('form').submit();
            }
        });
    });
</script>

@endsection
