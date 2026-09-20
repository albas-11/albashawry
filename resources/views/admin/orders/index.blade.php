@extends('layouts.admin')

@section('title', 'Orders')
@section('content')

    <div class="container-fluid py-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h4 fw-bold mb-1 text-dark">@lang('admin.Orders') </h1>
                <p class="text-muted small mb-0">@lang('admin.Manage and track customer transactions')</p>
            </div>
        </div>

        {{-- Messages de succès/erreur --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">

                <form method="GET" action="{{ route('admin.orders.index') }}">
                    <div class="row g-2">
                        {{-- Champ de recherche --}}
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                                    </svg>
                                </span>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="form-control bg-light border-0">
                            </div>
                        </div>

                        {{-- Filtrage par client --}}
                        <div class="col-md-3">
                            <select name="customer" class="form-select bg-light border-0">
                                <option value="">@lang('admin.All Customers')</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ request('customer') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-auto">
                            <button type="submit" class="btn  px-4"
                                style="background-color: #2E3192; color: white">@lang('admin.Filter')</button>
                        </div>

                        @if (request('search') || request('customer'))
                            <div class="col-md-auto">
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-light px-3 text-muted">
                                    @lang('admin.Reset')
                                </a>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase text-muted small fw-bold" style="width: 12%">
                                @lang('admin.Order ID')</th>
                            <th class="py-3 text-uppercase text-muted small fw-bold">@lang('admin.Customer')</th>
                            <th class="py-3 text-uppercase text-muted small fw-bold">@lang('admin.Date')</th>
                            <th class="py-3 text-uppercase text-muted small fw-bold">@lang('admin.Status')</th>
                            <th class="py-3 text-uppercase text-muted small fw-bold text-end">@lang('admin.Total')</th>
                            <th class="py-3 text-uppercase text-muted small fw-bold text-center pe-4">@lang('admin.Actions')
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="ps-4">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                        class="fw-bold text-dark text-decoration-none">
                                        #{{ $order->order_number }}
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span
                                            class="text-secondary fw-medium">{{ $order->user->name ?? 'Guest Customer' }}</span>
                                    </div>
                                </td>
                                <td class="text-muted small">
                                    {{ $order->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                                        @csrf
                                        @method('POST')
                                        <select name="status" class="form-select form-select-sm"
                                            onchange="this.form.submit()">
                                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>
                                                @lang('admin.pending')
                                            </option>
                                            <option value="processing"
                                                {{ $order->status === 'processing' ? 'selected' : '' }}>
                                                @lang('admin.processing')
                                            </option>
                                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>
                                                @lang('admin.shipped')
                                            </option>
                                            <option value="delivered"
                                                {{ $order->status === 'delivered' ? 'selected' : '' }}>
                                                @lang('admin.delivered')
                                            </option>
                                            <option value="cancelled"
                                                {{ $order->status === 'cancelled' ? 'selected' : '' }}>
                                                @lang('admin.cancelled')
                                            </option>
                                        </select>
                                    </form>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-dark">{{ number_format($order->total, 2) }} <small
                                            class="text-muted fw-normal">SAR</small></span>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-inline-flex gap-2">

                                        {{-- View Button --}}
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                            class="btn btn-action btn-action-secondary" title="@lang('message.view_details')"
                                            aria-label="@lang('message.view_details')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 16 16" fill="currentColor">
                                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                                <path
                                                    d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                            </svg>
                                        </a>

                                        {{-- Delete Button --}}
                                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST"
                                            onsubmit="return confirm('@lang('admin.Are you sure you want to delete this order?')')" class="m-0">
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
                                <td colspan="6" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="fas fa-box-open fa-3x text-light"></i>
                                    </div>
                                    <h6 class="text-muted fw-light">@lang('admin.No orders found.')</h6>
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
        {{ $orders->links('pagination::bootstrap-5') }}
    </div>

    <style>
        /* Clean Styling Adjustments */
        .table thead th {
            border-top: none;
            font-size: 0.7rem;
            letter-spacing: 0.05rem;
        }

        .table tbody td {
            padding: 1.1rem 0.5rem;
            border-bottom: 1px solid #f2f4f7;
        }

        .input-group-text,
        .form-control {
            transition: all 0.2s ease;
        }

        .form-control:focus {
            box-shadow: none;
            background-color: #fff !important;
            border: 1px solid #dee2e6 !important;
        }

        .badge {
            font-size: 0.75rem;
        }

        .btn-dark {
            border-radius: 6px;
        }

        .btn-group .btn {
            margin: 0 2px;
            border-radius: 4px !important;
        }

        .btn-sm {
            padding: 0.35rem 0.7rem;
            font-size: 0.85rem;
        }

        /* Pagination-inspired action buttons */
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

        /* Neutral (like page-link) */
        .btn-action-secondary {
            color: #6c757d;
            border-color: #dee2e6;
        }

        .btn-action-secondary:hover {
            background: #f8f9fa;
            border-color: #cfd4da;
            color: #495057;
        }

        /* Danger (subtle like pagination hover/active) */
        .btn-action-danger {
            color: #dc3545;
            border-color: rgba(220, 53, 69, .35);
        }

        .btn-action-danger:hover {
            background: rgba(220, 53, 69, .08);
            border-color: rgba(220, 53, 69, .55);
            color: #dc3545;
        }

        /* Focus like bootstrap */
        .btn-action:focus {
            box-shadow: 0 0 0 .2rem rgba(46, 49, 146, .15);
        }
    </style>

@endsection
