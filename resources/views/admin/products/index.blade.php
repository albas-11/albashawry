@extends('layouts.admin')

@section('title', 'Products Catalog')

@section('content')
    <div class="container-fluid py-4" style="max-width: 1400px;">

        {{-- HEADER --}}
        <div class="row align-items-center mb-5 g-3">
            <div class="col-md-6">
                <h1 class="fw-bold h3 mb-1 text-dark">@lang('admin.Products Catalog')</h1>
                <p class="text-muted small mb-0">
                    @lang('admin.Found') <span class="fw-semibold text-primary-custom">{{ $products->total() }}</span>
                    @lang('admin.items')
                </p>
            </div>
            <div class="col-md-6 d-flex justify-content-md-end align-items-center gap-3">
                <button class="btn btn-primary-custom px-4 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal"
                    data-bs-target="#addItemModal">
                    <i class="bi bi-plus-lg"></i>
                    <span class="fw-medium">@lang('admin.Add Item')</span>
                </button>
            </div>
        </div>

        {{-- FILTER SECTION --}}
        <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden">
            <div class="card-body p-3 p-lg-4">
                <form method="GET" action="{{ route('admin.products.index') }}">
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
                                    <label for="floatingSearch"
                                        class="text-muted small">@lang('admin.Search by name or SKU')</label>
                                </div>
                            </div>
                        </div>

                        {{-- TYPE --}}
                        <div class="col-lg-3">
                            <div class="form-floating border-start-custom">
                                <select name="type" class="form-select border-0 bg-light" id="floatingType">
                                    <option value="">@lang('admin.All Categories')</option>
                                    <option value="product" {{ request('type') == 'product' ? 'selected' : '' }}>
                                        @lang('admin.Eyewear')</option>
                                    <option value="lens" {{ request('type') == 'lens' ? 'selected' : '' }}>
                                        @lang('admin.Contact Lens')</option>
                                    <option value="accessory" {{ request('type') == 'accessory' ? 'selected' : '' }}>
                                        @lang('admin.Accessory')</option>
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
                                <a href="{{ route('admin.products.index') }}"
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

        {{-- PRODUCT GRID --}}
        <div id="gridView" class="d-none">
            <div class="row g-4">
                @forelse($products as $item)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card h-100 border-0 shadow-sm product-card-hover">

                            {{-- IMAGE --}}
                            <div class="position-relative p-2">
                                <div class="ratio ratio-1x1 bg-faded rounded-4 overflow-hidden">
                                    @if ($item->primaryImage ?? false)
                                       <img src="{{ asset($item->primaryImage->image ?? 'assets/placeholder.png') }}"
                                                class="rounded-3 bg-light" width="45" height="45"
                                                style="object-fit: cover;">
                                    @else
                                        <div class="d-flex justify-content-center align-items-center text-muted opacity-25">
                                            <i class="bi bi-image fs-1"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge badge-type-{{ $item->item_type }} shadow-sm">
                                        {{ strtoupper($item->item_type) }}
                                    </span>
                                </div>
                            </div>

                            <div class="card-body pt-0 px-4 pb-4">
                                <p class="text-uppercase text-muted fw-bold mb-1"
                                    style="font-size: 0.65rem; letter-spacing: 1px;">
                                    {{ $item->brand->name ?? 'General' }}
                                </p>

                                <h6 class="fw-semibold text-dark mb-2 text-truncate">
                                    @if (method_exists($item, 'getTranslation'))
                                        {{ $item->getTranslation('name', 'fr') }}
                                    @else
                                        {{ is_array($item->name) ? $item->name['fr'] ?? ($item->name['en'] ?? '') : $item->name }}
                                    @endif
                                </h6>

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class=" fw-bold text-primary-custom mb-0">
                                        {{ number_format($item->price ?? 0, 2) }}
                                        <small class="fw-normal opacity-50">SAR</small>
                                    </span>

                                    {{-- ACTIONS (UPDATED like Orders) --}}
                                    @php
                                        $editRoute = match ($item->item_type) {
                                            'product' => route('admin.products.edit', $item->id),
                                            'lens' => route('admin.contact-lenses.edit', $item->id),
                                            default => route('admin.accessories.edit', $item->id),
                                        };

                                        $deleteRoute = match ($item->item_type) {
                                            'product' => route('admin.products.destroy', $item->id),
                                            'lens' => route('admin.contact-lenses.destroy', $item->id),
                                            'accessory' => route('admin.accessories.destroy', $item->id),
                                        };
                                    @endphp

                                    <div class="d-inline-flex gap-2">

                                        {{-- Edit --}}
                                        <a href="{{ $editRoute }}"
                                            class="btn btn-action btn-action-secondary"
                                            title="@lang('admin.Edit')" aria-label="@lang('admin.Edit')">
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

                                        {{-- Delete (SweetAlert uses this class) --}}
                                        <form method="POST" action="{{ $deleteRoute }}" class="delete-product-form m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn btn-action btn-action-danger btn-delete-product"
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
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-box-seam display-1 text-light-emphasis mb-3 d-block"></i>
                        <h5 class="text-muted fw-light">@lang('admin.No items found in your catalog.')</h5>
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
                                <th class="ps-4 border-0">@lang('admin.Product')</th>
                                <th class="border-0">@lang('admin.Type')</th>
                                <th class="border-0">@lang('admin.Brand')</th>
                                <th class="border-0">@lang('admin.Price')</th>
                                <th class="text-end pe-4 border-0">@lang('admin.Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $item)
                                @php
                                    $editRoute = match ($item->item_type) {
                                        'product' => route('admin.products.edit', $item->id),
                                        'lens' => route('admin.contact-lenses.edit', $item->id),
                                        default => route('admin.accessories.edit', $item->id),
                                    };

                                    $deleteRoute = match ($item->item_type) {
                                        'product' => route('admin.products.destroy', $item->id),
                                        'lens' => route('admin.contact-lenses.destroy', $item->id),
                                        'accessory' => route('admin.accessories.destroy', $item->id),
                                    };
                                @endphp

                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ asset($item->primaryImage->image ?? 'assets/placeholder.png') }}"
                                                class="rounded-3 bg-light" width="45" height="45"
                                                style="object-fit: cover;">
                                            <span class="fw-semibold">
                                                @if (method_exists($item, 'getTranslation'))
                                                    {{ $item->getTranslation('name', 'fr') }}
                                                @else
                                                    {{ is_array($item->name) ? $item->name['fr'] ?? ($item->name['en'] ?? '') : $item->name }}
                                                @endif
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark border fw-medium">
                                            {{ strtoupper($item->item_type) }}
                                        </span>
                                    </td>

                                    <td class="text-muted">{{ $item->brand->name ?? '-' }}</td>

                                    <td class="fw-bold text-primary-custom">{{ number_format($item->price, 2) }} SAR</td>

                                    {{-- ACTIONS (UPDATED like Orders) --}}
                                    <td class="text-end pe-4">
                                        <div class="d-inline-flex gap-2">

                                            {{-- Edit --}}
                                            <a href="{{ $editRoute }}"
                                                class="btn btn-action btn-action-secondary"
                                                title="@lang('admin.Edit')" aria-label="@lang('admin.Edit')">
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
                                            <form method="POST" action="{{ $deleteRoute }}" class="delete-product-form m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="btn btn-action btn-action-danger btn-delete-product"
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
                                            <i class="bi bi-box-seam" style="font-size: 3rem;"></i>
                                        </div>
                                        <p class="text-muted fw-medium">@lang('admin.No items found in your catalog.')</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- PAGINATION --}}
    <div class="mt-2">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>

    {{-- MODAL ADD --}}
    <div class="modal fade" id="addItemModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form id="addItemForm" class="w-100">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 p-4 pb-0">
                        <h5 class="fw-bold">@lang('admin.Add New Catalog Item')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <label class="form-label small fw-bold text-muted mb-2">@lang('admin.Select Item Category')</label>
                        <select id="item_type" class="form-select bg-light border-0 py-2">
                            <option value="product">@lang('admin.Eyewear')</option>
                            <option value="lens">@lang('admin.Contact Lens')</option>
                            <option value="accessory">@lang('admin.Accessory')</option>
                        </select>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light px-4 fw-medium text-muted"
                            data-bs-dismiss="modal">@lang('admin.Cancel')</button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm"
                            style="background:#2E3192; border:none;">@lang('admin.Continue')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Search icon + floating label */
        .search-group .input-group-text {
            border-top-left-radius: 0.5rem !important;
            border-bottom-left-radius: 0.5rem !important;
        }

        .search-group .form-control {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }

        /* Inputs */
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

        .text-primary-custom {
            color: var(--primary-color) !important;
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
            box-shadow: 0 4px 12px rgba(46, 49, 146, 0.2);
        }

        .bg-faded {
            background-color: var(--bg-faded);
        }

        /* Badges */
        .badge {
            padding: 0.5em 1em;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.65rem;
        }

        .badge-type-product {
            background: #e0e7ff;
            color: #4338ca;
        }

        .badge-type-lens {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-type-accessory {
            background: #fef9c3;
            color: #a16207;
        }

        /* View Switcher */
        .active-toggle {
            background: white !important;
            color: var(--primary-color) !important;
            font-weight: bold;
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

        .x-small {
            font-size: 0.7rem;
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

        /* Optional: make pagination match the style */
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

        btnGrid.addEventListener('click', () => toggleView(true));
        btnTable.addEventListener('click', () => toggleView(false));
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Redirect Logic for adding items
        document.getElementById('addItemForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let type = document.getElementById('item_type').value;
            const routes = {
                'product': "{{ route('admin.products.create') }}",
                'lens': "{{ route('admin.contact-lenses.create') }}",
                'accessory': "{{ route('admin.accessories.create') }}"
            };
            if (routes[type]) location.href = routes[type];
        });

        // Delete confirmation with SweetAlert2
        document.querySelectorAll('.btn-delete-product').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('form');
                Swal.fire({
                    title: "Delete Item?",
                    text: "This action cannot be undone and will remove the item permanently.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: "Yes, Delete",
                    cancelButtonText: "Cancel",
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
