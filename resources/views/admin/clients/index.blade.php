@extends('layouts.admin')

@section('title', __('admin.Clients Management'))

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <h1 class="fw-bold h4 mb-1 text-dark">@lang('admin.Clients Management')</h1>
                <p class="text-muted small mb-0">
                    @lang('admin.Total database'): <strong>{{ $clients->total() }}</strong> @lang('admin.registered clients')
                </p>
            </div>

            <form method="GET" action="{{ route('admin.clients.index') }}" class="d-flex gap-1">
                <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control border-0 bg-white px-3" placeholder="@lang('admin.Search name, email or phone')"
                        style="min-width: 280px;">
                    <button class="btn btn-white border-0 bg-white text-muted" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted small fw-bold text-uppercase">@lang('admin.Client')</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">@lang('admin.Contact Info')</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase text-center">@lang('admin.Status')</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">@lang('admin.Joined Date')</th>
                            <th class="pe-4 py-3 text-muted small fw-bold text-uppercase text-end">@lang('admin.Quick Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 d-flex align-items-center justify-content-center rounded-circle bg-secondary bg-opacity-10 text-secondary fw-bold"
                                            style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($client->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $client->name }}</div>
                                            <div class="text-muted x-small">@lang('admin.Ref'):
                                                #{{ $client->num ?? $client->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark small"><i class="bi bi-envelope-at me-1 opacity-50"></i>
                                            {{ $client->email }}</span>
                                        <span class="text-muted x-small"><i class="bi bi-telephone me-1 opacity-50"></i>
                                            {{ $client->num ?? __('admin.No phone') }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fw-medium"
                                        style="font-size: 0.7rem;">
                                        @lang('admin.Active Client')
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    {{ $client->created_at->format('M d, Y') }}
                                </td>

                                {{-- ACTIONS (UPDATED like Orders) --}}
                                <td class="pe-4 py-3 text-end">
                                    <div class="d-inline-flex gap-2">

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.users.edit', $client) }}"
                                            class="btn btn-action btn-action-secondary"
                                            title="@lang('admin.Edit Profile')" aria-label="@lang('admin.Edit Profile')">
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

                                        {{-- Orders --}}
                                        <a href="{{ route('admin.orders.index', ['customer' => $client->id]) }}"
                                            class="btn btn-action btn-action-primary"
                                            title="@lang('admin.View Purchase History')"
                                            aria-label="@lang('admin.View Purchase History')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 16 16" fill="currentColor">
                                                <path
                                                    d="M2.5 1a.5.5 0 0 0-.5.5V2h12v-.5a.5.5 0 0 0-.5-.5h-11z" />
                                                <path
                                                    d="M14 3H2v11.5a.5.5 0 0 0 .5.5h11a.5.5 0 0 0 .5-.5V3zm-9.5 3a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zm0 2.5A.5.5 0 0 1 5 8h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5z" />
                                            </svg>
                                        </a>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="opacity-25 mb-3">
                                        <i class="bi bi-person-x" style="font-size: 3rem;"></i>
                                    </div>
                                    <p class="text-muted fw-medium">@lang('admin.No clients found matching your criteria')</p>
                                    <a href="{{ route('admin.clients.index') }}"
                                        class="btn btn-sm btn-light border">@lang('admin.Clear Search')</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                @lang('admin.Showing') {{ $clients->firstItem() }} @lang('admin.to') {{ $clients->lastItem() }}
                @lang('admin.of') {{ $clients->total() }} @lang('admin.clients')
            </div>
            <div>
                {{ $clients->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <style>
        :root { --primary-custom: #2E3192; }

        .avatar-sm { font-size: 0.9rem; }
        .x-small { font-size: 0.72rem; }

        .table thead th {
            border-bottom: none;
            letter-spacing: 0.05em;
        }

        .btn-white {
            background: #fff;
            border: 1px solid #eee;
        }
        .btn-white:hover { background: #f8f9fa; }

        .form-control:focus {
            background-color: #fff !important;
            box-shadow: 0 0 0 3px rgba(46, 49, 146, 0.1) !important;
            border: 1px solid #2E3192 !important;
        }

        .pagination { margin-bottom: 0; }

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

        /* Primary (for Orders button) */
        .btn-action-primary {
            color: var(--primary-custom);
            border-color: rgba(46, 49, 146, .35);
        }
        .btn-action-primary:hover {
            background: rgba(46, 49, 146, .08);
            border-color: rgba(46, 49, 146, .55);
            color: var(--primary-custom);
        }

        .btn-action:focus {
            box-shadow: 0 0 0 .2rem rgba(46, 49, 146, .15);
        }

        /* Optional pagination feel */
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
@endsection
