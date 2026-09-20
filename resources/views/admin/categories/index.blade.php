@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
    <div class="container-fluid px-4 py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="h3 fw-light text-dark mb-1">@lang('admin.Categories')</h1>
                <p class="text-muted small mb-0">Structurez votre catalogue et gérez les rayons de votre boutique.</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-dark btn-sm px-4 py-2 rounded-pill shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> @lang('admin.Add Category')
            </a>
        </div>

        {{-- Card Management --}}
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-uppercase text-muted" style="font-size: 0.7rem; letter-spacing: 0.1em;">
                            <th class="ps-4 py-3">@lang('admin.Image')</th>
                            <th>@lang('admin.Name')</th>
                            <th class="text-center">@lang('admin.Products')</th>
                            <th class="text-center">@lang('admin.Status')</th>
                            <th class="text-end pe-4">@lang('admin.Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr class="transition-all">
                                {{-- Image --}}
                                <td class="ps-4">
                                    @if ($category->image)
                                        <div class="avatar-wrapper">
                                           <img src="{{ asset('storage/' . $category->image) }}"
                                                alt="{{ $category->name }}" class="rounded-circle border"
                                                style="width:42px; height:42px; object-fit:cover;">
                                        </div>
                                    @else
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border"
                                            style="width:42px; height:42px;">
                                            <i class="bi bi-folder2 text-muted opacity-50"></i>
                                        </div>
                                    @endif
                                </td>

                                {{-- Name / Slug --}}
                                <td>
                                    <div class="fw-medium text-dark">{{ $category->name }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">/{{ $category->slug }}</div>
                                </td>

                                {{-- Products count --}}
                                <td class="text-center">
                                    <span
                                        class="small fw-semibold px-2 py-1 rounded bg-warning-subtle text-warning-emphasis">
                                        {{ $category->products_count }}
                                        <span class="fw-normal opacity-75" style="font-size: 0.7rem;">items</span>
                                    </span>
                                </td>


                                {{-- Status --}}
                                <td class="text-center">
                                    @if ($category->is_active)
                                        <span class="badge rounded-pill fw-normal px-3 py-2 bg-success-subtle text-success">
                                            <span class="d-inline-block bg-success rounded-circle me-1"
                                                style="width: 5px; height: 5px;"></span>
                                            @lang('admin.Active')
                                        </span>
                                    @else
                                        <span
                                            class="badge rounded-pill fw-normal px-3 py-2 bg-light text-secondary border border-light">
                                            @lang('admin.Hidden')
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions (UPDATED like Orders) --}}
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-2">

                                        {{-- Edit Button --}}
                                        <a href="{{ route('admin.categories.edit', $category) }}"
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

                                        {{-- Delete Button --}}
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                            class="m-0" onsubmit="return confirm('@lang('admin.Are you sure you want to delete this category?')')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-action btn-action-danger"
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
                                    <i class="bi bi-inbox fs-2 text-muted opacity-25 d-block mb-3"></i>
                                    <span class="text-muted">@lang('admin.No categories found')</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($categories, 'links') && $categories->hasPages())
                <div class="card-footer bg-white border-top border-light py-3">
                    {{ $categories->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    <style>
        .transition-all {
            transition: all 0.2s ease-in-out;
        }

        .table tbody tr:hover {
            background-color: #fcfcfc;
        }

        .avatar-wrapper {
            transition: transform 0.3s ease;
        }

        .table tr:hover .avatar-wrapper {
            transform: scale(1.1);
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

        /* Optional: pagination same feel */
        .pagination .page-link {
            border-radius: 12px !important;
            margin: 0 3px;
            border-color: #dee2e6;
            color: #6c757d;
        }

        .pagination .page-item.active .page-link {
            background-color: #2E3192;
            border-color: #2E3192;
            color: #fff;
        }

        .pagination .page-link:focus {
            box-shadow: 0 0 0 .2rem rgba(46, 49, 146, .15);
        }
    </style>
@endsection
