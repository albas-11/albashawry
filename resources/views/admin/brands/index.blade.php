@extends('layouts.admin')

@section('title', __('message.Manage Brands'))

@section('content')
    <div class="container-fluid px-4 py-4">

        {{-- Header --}}
        <div class="row align-items-center mb-5">
            <div class="col">
                <h1 class="h3 fw-light text-dark tracking-tight">@lang('message.Manage Brands')</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.brands.create') }}"
                    class="btn btn-sm px-4 py-2 rounded-pill shadow-sm transition-all"
                    style="background-color: #2E3192; color:aliceblue">
                    <i class="bi bi-plus-lg me-2"></i>
                    @lang('admin.Add Brand')
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success') || session('error'))
            <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} border-0 shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi {{ session('success') ? 'bi-check-circle' : 'bi-exclamation-triangle' }} me-2"></i>
                    <div>{{ session('success') ?? session('error') }}</div>
                </div>
            </div>
        @endif

        {{-- Table Card --}}
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.1em;">
                            <th class="ps-4 py-3">#</th>
                            <th>@lang('admin.Logo')</th>
                            <th>@lang('message.Name')</th>
                            <th>@lang('message.Status')</th>
                            <th class="text-end pe-4">@lang('admin.Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($brands as $brand)
                            <tr class="transition-all">
                                <td class="ps-4 text-muted small">#{{ $brand->id }}</td>

                                {{-- Logo --}}
                                <td>
                                    <div class="p-2 bg-light rounded-3 d-inline-block border border-light">
                                        @if ($brand->logo)
                                            <img src="{{ asset($brand->logo) }}"
                                                style="width: 40px; height: 40px; object-fit: contain; filter: grayscale(1) contrast(0.8);"
                                                class="hover-color transition-all" alt="">
                                        @else
                                            <i class="bi bi-image text-secondary opacity-50" style="font-size: 1.2rem;"></i>
                                        @endif
                                    </div>
                                </td>

                                {{-- Name --}}
                                <td>
                                    <div class="fw-medium text-dark">{{ $brand->getTranslation('name', 'en') }}</div>
                                    <div class="text-muted" style="font-size: 0.8rem;">
                                        {{ $brand->getTranslation('name', 'ar') }}
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td>
                                    @if ($brand->is_active)
                                        <span class="badge rounded-pill fw-normal px-3 py-2 bg-success-subtle text-success">
                                            <span class="d-inline-block bg-success rounded-circle me-1"
                                                style="width: 6px; height: 6px;"></span>
                                            @lang('message.Active')
                                        </span>
                                    @else
                                        <span class="badge rounded-pill fw-normal px-3 py-2 bg-light text-secondary">
                                            <span class="d-inline-block bg-secondary rounded-circle me-1"
                                                style="width: 6px; height: 6px;"></span>
                                            @lang('message.Inactive')
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions (UPDATED like Orders) --}}
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-2">

                                        {{-- Edit Button --}}
                                        <a href="{{ route('admin.brands.edit', $brand) }}"
                                            class="btn btn-action btn-action-secondary"
                                            title="@lang('admin.Edit')" aria-label="@lang('admin.Edit')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 16 16" fill="currentColor">
                                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706l-1.793 1.793-2.121-2.12L13.381.525a.5.5 0 0 1 .707 0l1.414 1.414z"/>
                                                <path d="M12.794 5.207 10.672 3.086 4.939 8.818a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l5.855-5.612z"/>
                                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a.5.5 0 0 0 0-1h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 0-1 0v11z"/>
                                            </svg>
                                        </a>

                                        {{-- Delete Button --}}
                                        <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST"
                                            onsubmit="return confirm('@lang('message.Are you sure?')')" class="m-0">
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
                                    <p class="text-muted mb-0">@lang('message.No brands found')</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-2">
            {{ $brands->links('pagination::bootstrap-5') }}
        </div>

    </div>

    <style>
        .tracking-tight { letter-spacing: -0.02em; }
        .transition-all { transition: all 0.3s ease; }
        .hover-color:hover { filter: grayscale(0) !important; }

        .table thead th { border-bottom: none; }
        .table tbody tr:hover { background-color: #fafafa; }

        /* === SAME AS ORDERS (Action buttons) === */
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
    </style>
@endsection
