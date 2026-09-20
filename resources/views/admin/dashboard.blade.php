@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')

    <div class="container-fluid py-4" style="min-height: 100vh;">

        {{-- HEADER SECTION --}}
        <div class="row mb-5 align-items-center">
            <div class="col-md-7">
                <h6 class="text-primary fw-bold text-uppercase tracking-widest mb-2"
                    style="font-size: 0.75rem; letter-spacing: 2px;">
                    @lang('admin.Tableau de Bord Administratif')
                </h6>
                <h1 class="display-6 fw-bold text-dark mb-0">@lang('message.Dashboard')</h1>
                <p class="text-muted mt-2">
                    @lang('admin.Welcome, here is an overview of your store activity today.')
                </p>
            </div>
            <div class="col-md-5 text-md-end">
                <div class="d-inline-flex align-items-center bg-white border rounded-4 px-4 py-2 shadow-sm">
                    <div class="text-end me-3">
                        <span class="d-block small text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">
                            @lang('admin.Today Date')
                        </span>
                        <span class="fw-bold text-dark">{{ now()->format('d M Y') }}</span>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3">
                        <i class="bi bi-calendar3 text-primary fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- STATISTICS CARDS --}}
        <div class="row g-4 mb-5">
            @php
                $statsData = [
                    [
                        'label' => __('admin.Total Orders'),
                        'value' => $stats['total_orders'],
                        'icon' => 'bi-cart3',
                        'color' => '#2E3192',
                        'bg' => '#eff6ff',
                        'permission' => 'view_orders',
                    ],
                    [
                        'label' => __('admin.Pending Orders'),
                        'value' => $stats['pending_orders'],
                        'icon' => 'bi-clock-history',
                        'color' => '#f59e0b',
                        'bg' => '#fffbeb',
                        'permission' => 'view_orders',
                    ],
                    [
                        'label' => __('admin.Total Revenue'),
                        'value' => number_format($stats['total_revenue'], 2) . ' SAR',
                        'icon' => 'bi-wallet2',
                        'color' => '#10b981',
                        'bg' => '#f0fdf4',
                        'permission' => 'view_orders',
                    ],
                    [
                        'label' => __('admin.Customers'),
                        'value' => $stats['total_users'],
                        'icon' => 'bi-people',
                        'color' => '#06b6d4',
                        'bg' => '#ecfeff',
                        'permission' => 'manage_clients',
                    ],
                ];
            @endphp

            @foreach ($statsData as $stat)
                @if (!isset($stat['permission']) || ($permissions[$stat['permission']] ?? false))
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 stat-card overflow-hidden">
                            <div class="card-body p-4 position-relative">
                                <div class="d-flex justify-content-between mb-3">
                                    <div class="rounded-4 d-flex align-items-center justify-content-center"
                                        style="width: 48px; height: 48px; background-color: {{ $stat['bg'] }};">
                                        <i class="bi {{ $stat['icon'] }} fs-4" style="color: {{ $stat['color'] }}"></i>
                                    </div>
                                </div>
                                <p class="mb-1 text-muted small fw-bold text-uppercase tracking-wider">
                                    {{ $stat['label'] }}
                                </p>
                                <h3 class="fw-bold mb-0 text-dark">{{ $stat['value'] }}</h3>
                                <div class="stat-pattern" style="background-color: {{ $stat['color'] }};"></div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="row g-4">
            {{-- RECENT ORDERS TABLE --}}
            @if ($permissions['view_orders'])
                <div class="col-xl-8">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                        <div
                            class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-dark">@lang('admin.Recent Orders')</h5>
                            <a href="{{ route('admin.orders.index') }}"
                                class="btn btn-outline-dark btn-sm rounded-pill px-3">
                                @lang('admin.View All')
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="bg-light bg-opacity-50">
                                        <tr>
                                            <th class="border-0 px-4 py-3 text-muted small fw-bold text-uppercase">
                                                @lang('admin.Reference')</th>
                                            <th class="border-0 py-3 text-muted small fw-bold text-uppercase">
                                                @lang('admin.Customer')</th>
                                            <th class="border-0 py-3 text-muted small fw-bold text-uppercase">
                                                @lang('admin.Date')</th>
                                            <th class="border-0 py-3 text-muted small fw-bold text-uppercase">
                                                @lang('admin.Total')</th>
                                            <th class="border-0 py-3 text-muted small fw-bold text-uppercase text-center">
                                                @lang('admin.Status')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentOrders as $order)
                                            <tr class="hover-bg-light transition-all">
                                                <td class="px-4 py-3 fw-bold" style="color: #2E3192;">#{{ $order->id }}
                                                </td>
                                                <td class="py-3">
                                                    <div class="d-flex align-items-center">
                                                        <div
                                                            class="avatar-box rounded-3 bg-primary bg-opacity-10 text-primary fw-bold me-2">
                                                            {{ substr($order->user->name, 0, 1) }}
                                                        </div>
                                                        <span
                                                            class="text-dark fw-medium small">{{ $order->user->name }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3 text-muted small">
                                                    {{ $order->created_at->format('d M, H:i') }}</td>
                                                <td class="py-3 fw-bold text-dark">{{ number_format($order->total, 2) }}
                                                    SAR</td>
                                                <td class="py-3 text-center">
                                                    <span class="badge-elegant {{ $order->status }}">
                                                        {{ $order->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5">@lang('admin.No recent orders.')</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- STOCK ALERTS --}}
            @if ($permissions['manage_products'])
                <div class="col-xl-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                        <div class="card-header bg-white border-0 py-4 px-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="mb-0 fw-bold text-dark">@lang('admin.Stock Alerts')</h5>
                                <i class="bi bi-bell text-danger pulse"></i>
                            </div>
                        </div>
                        <div class="card-body p-4 pt-0">
                            @forelse($lowStockProducts as $product)
                                <div
                                    class="d-flex align-items-center mb-4 p-3 rounded-4 border border-dashed shadow-xs hover-shadow transition-all">
                                    <div class="bg-light rounded-3 p-2 me-3">
                                        <i class="bi bi-box-seam text-secondary fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h6 class="mb-0 text-truncate fw-bold small">{{ $product->name }}</h6>
                                        <span class="text-muted x-small">
                                            @lang('admin.Remaining: ')
                                            <b class="text-danger">{{ $product->stock }}</b>
                                            @lang('admin.units')
                                        </span>
                                    </div>
                                    @if ($permissions['manage_products'])
                                        <a href="{{ route('admin.products.edit', $product->id) }}"
                                            class="btn btn-sm btn-light rounded-circle">
                                            <i class="bi bi-arrow-right-short"></i>
                                        </a>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <i class="bi bi-shield-check fs-1 text-success opacity-50"></i>
                                    <p class="text-muted mt-2 small">@lang('admin.Your stock is optimal.')</p>
                                </div>
                            @endforelse
                        </div>
                        @if ($lowStockProducts->count() > 0 && $permissions['manage_products'])
                            <div class="card-footer border-0 bg-transparent p-4 pt-0">
                                <a href="{{ route('admin.products.index') }}"
                                    class="btn btn-dark w-100 rounded-pill py-2 fw-bold">
                                    @lang('admin.Restock')
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- MESSAGE SI AUCUNE PERMISSION --}}
        @if (
            !$permissions['is_admin'] &&
                !$permissions['view_orders'] &&
                !$permissions['manage_products'] &&
                !$permissions['manage_clients']
        )
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-shield-lock fs-1 text-muted opacity-50 mb-3"></i>
                            <h4 class="fw-bold text-dark mb-2">@lang('admin.Access Limited')</h4>
                            <p class="text-muted mb-0">
                                @lang('admin.You do not have permissions to view dashboard statistics.')
                                <br>
                                @lang('admin.Contact your administrator to request access.')
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

    <style>
        /* TYPOGRAPHIE ET ESPACEMENT */
        .tracking-widest {
            letter-spacing: 0.15em;
        }

        .tracking-wider {
            letter-spacing: 0.05em;
        }

        .x-small {
            font-size: 0.7rem;
        }

        .transition-all {
            transition: all 0.25s ease;
        }

        /* STAT CARDS */
        .stat-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08) !important;
        }

        .stat-pattern {
            position: absolute;
            bottom: -20px;
            right: -20px;
            width: 100px;
            height: 100px;
            opacity: 0.03;
            border-radius: 50%;
            filter: blur(20px);
        }

        /* AVATARS ET BADGES */
        .avatar-box {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        .badge-elegant {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-block;
        }

        .badge-elegant.pending {
            background: #fffbeb;
            color: #92400e;
        }

        .badge-elegant.completed {
            background: #f0fdf4;
            color: #166534;
        }

        .badge-elegant.cancelled {
            background: #fef2f2;
            color: #991b1b;
        }

        .badge-elegant.processing {
            background: #eff6ff;
            color: #1e40af;
        }

        /* ANIMATIONS */
        .pulse {
            animation: pulse-red 2s infinite;
        }

        @keyframes pulse-red {
            0% {
                transform: scale(0.95);
                opacity: 0.7;
            }

            70% {
                transform: scale(1.1);
                opacity: 1;
            }

            100% {
                transform: scale(0.95);
                opacity: 0.7;
            }
        }

        .hover-bg-light:hover {
            background-color: #f8fafc;
        }

        .shadow-xs {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .hover-shadow:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
    </style>
@endsection
