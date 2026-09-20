@extends('layouts.admin')

@section('title', 'Pixels')

@section('content')
    <div class="container-fluid py-4" style="max-width: 1400px;">

        {{-- HEADER --}}
        <div class="row align-items-center mb-5 g-3">
            <div class="col-md-6">
                <h1 class="fw-bold h3 mb-1 text-dark">@lang('admin.Pixels')</h1>
                <p class="text-muted small mb-0">
                    @lang('admin.Found')
                    <span class="fw-semibold text-primary-custom">{{ $pixels->count() ?? 0 }}</span>
                    @lang('admin.items')
                </p>
            </div>

            <div class="col-md-6 d-flex justify-content-md-end align-items-center gap-3">
                <a href="{{ route('admin.pixels.create') }}"
                    class="btn btn-primary-custom px-4 shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i>
                    <span class="fw-medium">@lang('admin.Add Pixel')</span>
                </a>
            </div>
        </div>

        {{-- FILTER SECTION --}}
        <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden">
            <div class="card-body p-3 p-lg-4">
                <form method="GET" action="{{ route('admin.pixels.index') }}">
                    <div class="row g-3 align-items-center">

                        {{-- SEARCH --}}
                        <div class="col-lg-4">
                            <div class="input-group search-group">
                                <span class="input-group-text border-0 bg-light ps-3">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <div class="form-floating flex-grow-1">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control border-0 bg-light" id="floatingSearch" placeholder="Search...">
                                    <label for="floatingSearch" class="text-muted small">@lang('admin.Search by name or pixel id')</label>
                                </div>
                            </div>
                        </div>

                        {{-- PROVIDER --}}
                        <div class="col-lg-3">
                            <div class="form-floating border-start-custom">
                                <select name="provider" class="form-select border-0 bg-light" id="floatingProvider">
                                    <option value="">@lang('admin.All Providers')</option>
                                    <option value="facebook" {{ request('provider') == 'facebook' ? 'selected' : '' }}>
                                        Facebook</option>
                                    <option value="tiktok" {{ request('provider') == 'tiktok' ? 'selected' : '' }}>
                                        TikTok</option>
                                    <option value="google" {{ request('provider') == 'google' ? 'selected' : '' }}>
                                        Google</option>
                                </select>
                            </div>
                        </div>

                        {{-- STATUS --}}
                        <div class="col-lg-3">
                            <div class="form-floating border-start-custom">
                                <select name="status" class="form-select border-0 bg-light" id="floatingStatus">
                                    <option value="">@lang('admin.All Status')</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                                        @lang('admin.Active')</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                                        @lang('admin.Inactive')</option>
                                </select>
                            </div>
                        </div>

                        {{-- ACTIONS --}}
                        <div class="col-lg-2">
                            <div class="d-flex gap-2 h-100">
                                <button type="submit"
                                    class="btn btn-primary-custom w-100 rounded-3 d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-filter"></i>
                                    <span>@lang('admin.Filter')</span>
                                </button>
                                <a href="{{ route('admin.pixels.index') }}"
                                    class="btn btn-light rounded-3 d-flex align-items-center justify-content-center px-3"
                                    title="@lang('admin.Reset')">
                                    <i class="bi bi-arrow-clockwise text-muted"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        {{-- VIEW SWITCHER --}}
        <div class="d-flex justify-content-end">
            <div class="btn-group p-1 bg-light rounded-3">
                <button id="btnTable" class="btn btn-sm btn-white shadow-sm rounded-2 active-toggle">
                    <i class="bi bi-list-ul"></i>
                </button>
                <button id="btnGrid" class="btn btn-sm text-muted">
                    <i class="bi bi-grid-fill"></i>
                </button>
            </div>
        </div>

        {{-- GRID VIEW --}}
        <div id="gridView" class="d-none">
            <div class="row g-4">
                @forelse($pixels as $pixel)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card h-100 border-0 shadow-sm product-card-hover">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <p class="text-uppercase text-muted fw-bold mb-1"
                                            style="font-size: 0.65rem; letter-spacing: 1px;">
                                            {{ strtoupper($pixel->provider ?? '-') }}
                                        </p>
                                        <h6 class="fw-semibold text-dark mb-0 text-truncate">
                                            {{ $pixel->name ?? 'Pixel' }}
                                        </h6>
                                        <p class="text-muted small mb-0 mt-1 text-truncate">
                                            {{ $pixel->pixel_id }}
                                        </p>
                                    </div>
                                    <span class="badge {{ ($pixel->is_active ?? false) ? 'badge-active' : 'badge-inactive' }}">
                                        {{ ($pixel->is_active ?? false) ? __('admin.Active') : __('admin.Inactive') }}
                                    </span>
                                </div>

                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @php
                                        $events = $pixel->events ?? [];
                                        if (is_string($events)) {
                                            $events = json_decode($events, true) ?: [];
                                        }
                                    @endphp
                                    @forelse($events as $ev)
                                        <span class="badge bg-light text-dark border fw-medium">{{ strtoupper($ev) }}</span>
                                    @empty
                                        <span class="text-muted small">@lang('admin.No events selected')</span>
                                    @endforelse
                                </div>

                                {{-- ACTIONS (UPDATED like Orders) --}}
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.pixels.edit', $pixel->id) }}"
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

                                    <form method="POST" action="{{ route('admin.pixels.destroy', $pixel->id) }}"
                                        class="delete-pixel-form d-inline m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="btn btn-action btn-action-danger btn-delete-pixel"
                                            title="@lang('admin.Delete')" aria-label="@lang('admin.Delete')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 16 16" fill="currentColor">
                                                <path
                                                    d="M2.5 1a1 1 0 0 0-1 1v1h13V2a1 1 0 0 0-1-1h-11zm1 4v8a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1V5h-9z" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-bullseye display-1 text-light-emphasis mb-3 d-block"></i>
                        <h5 class="text-muted fw-light">@lang('admin.No pixels found.')</h5>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- TABLE VIEW --}}
        <div id="tableView">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-muted small text-uppercase">
                                <th class="ps-4 border-0">@lang('admin.Pixel')</th>
                                <th class="border-0">@lang('admin.Provider')</th>
                                <th class="border-0">@lang('admin.Status')</th>
                                <th class="border-0">@lang('admin.Events')</th>
                                <th class="text-end pe-4 border-0">@lang('admin.Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pixels as $pixel)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-3 bg-light d-flex align-items-center justify-content-center"
                                                style="width:45px;height:45px;">
                                                <i class="bi bi-bullseye text-primary-custom"></i>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold">{{ $pixel->name ?? 'Pixel' }}</span>
                                                <span class="text-muted small">{{ $pixel->pixel_id }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border fw-medium">
                                            {{ strtoupper($pixel->provider ?? '-') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ ($pixel->is_active ?? false) ? 'badge-active' : 'badge-inactive' }}">
                                            {{ ($pixel->is_active ?? false) ? __('admin.Active') : __('admin.Inactive') }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $events = $pixel->events ?? [];
                                            if (is_string($events)) {
                                                $events = json_decode($events, true) ?: [];
                                            }
                                        @endphp
                                        @if (count($events))
                                            <span class="text-muted small">{{ implode(', ', array_map('strtoupper', $events)) }}</span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>

                                    {{-- ACTIONS (UPDATED like Orders) --}}
                                    <td class="text-end pe-4">
                                        <div class="d-inline-flex gap-2">
                                            <a href="{{ route('admin.pixels.edit', $pixel->id) }}"
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

                                            <form method="POST" action="{{ route('admin.pixels.destroy', $pixel->id) }}"
                                                class="delete-pixel-form d-inline m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="btn btn-action btn-action-danger btn-delete-pixel"
                                                    title="@lang('admin.Delete')" aria-label="@lang('admin.Delete')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 16 16" fill="currentColor">
                                                        <path
                                                            d="M2.5 1a1 1 0 0 0-1 1v1h13V2a1 1 0 0 0-1-1h-11zm1 4v8a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1V5h-9z" />
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
                                            <i class="bi bi-bullseye" style="font-size: 3rem;"></i>
                                        </div>
                                        <p class="text-muted fw-medium">@lang('admin.No pixels found.')</p>
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
            {{ $pixels->links('pagination::bootstrap-5') }}
        </div>

    </div>

    <style>
        .search-group .input-group-text {
            border-top-left-radius: 0.5rem !important;
            border-bottom-left-radius: 0.5rem !important;
        }

        .search-group .form-control {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }

        .form-control,
        .form-select {
            height: 38px !important;
            padding: 0.375rem 0.75rem !important;
            font-size: 0.85rem;
        }

        @media (min-width: 992px) {
            .border-start-custom {
                border-left: 1px solid #e9ecef;
            }
        }

        :root {
            --primary-color: #2E3192;
            --bg-faded: #f8f9fa;
        }

        .text-primary-custom { color: var(--primary-color) !important; }

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

        .active-toggle {
            background: white !important;
            color: var(--primary-color) !important;
            font-weight: bold;
        }

        .badge-active {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .badge-inactive {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #fff !important;
            border: 1px solid #2E3192 !important;
            box-shadow: none;
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

        .btn-action svg { display: block; }

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

        /* Pagination */
        .pagination .page-link {
            border-radius: 12px !important;
            margin: 0 3px;
            border-color: #dee2e6;
            color: #6c757d;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
        }

        .pagination .page-link:focus {
            box-shadow: 0 0 0 .2rem rgba(46, 49, 146, .15);
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const btnGrid = document.getElementById('btnGrid');
        const btnTable = document.getElementById('btnTable');
        const gridView = document.getElementById('gridView');
        const tableView = document.getElementById('tableView');

        function toggleView(showGrid) {
            if (showGrid) {
                gridView.classList.remove('d-none');
                tableView.classList.add('d-none');

                btnGrid.classList.add('btn-white', 'shadow-sm', 'active-toggle');
                btnGrid.classList.remove('text-muted');

                btnTable.classList.remove('btn-white', 'shadow-sm', 'active-toggle');
                btnTable.classList.add('text-muted');
            } else {
                gridView.classList.add('d-none');
                tableView.classList.remove('d-none');

                btnTable.classList.add('btn-white', 'shadow-sm', 'active-toggle');
                btnTable.classList.remove('text-muted');

                btnGrid.classList.remove('btn-white', 'shadow-sm', 'active-toggle');
                btnGrid.classList.add('text-muted');
            }
        }

        btnGrid?.addEventListener('click', () => toggleView(true));
        btnTable?.addEventListener('click', () => toggleView(false));

        // Delete confirmation
        document.querySelectorAll('.btn-delete-pixel').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('form');
                Swal.fire({
                    title: "@lang('admin.Delete Item?')",
                    text: "@lang('admin.This action cannot be undone and will remove the item permanently.')",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: "@lang('admin.Yes, Delete')",
                    cancelButtonText: "@lang('admin.Cancel')",
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-4 shadow-lg',
                        confirmButton: 'rounded-pill px-4',
                        cancelButton: 'rounded-pill px-4'
                    }
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
@endpush
