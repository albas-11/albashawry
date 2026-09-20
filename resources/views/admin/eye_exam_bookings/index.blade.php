@extends('layouts.admin')

@section('title', __('admin.Eye Exam Bookings'))

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h1 class="fw-bold h4 mb-1 text-dark">@lang('admin.Eye Exam Bookings')</h1>
                <p class="text-muted small mb-0">@lang('admin.Free eye test appointments booked from the store finder')</p>
            </div>
            <span class="badge bg-white border text-dark px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-calendar-check me-1 text-primary"></i>
                {{ $counts['all'] }} @lang('admin.Bookings')
            </span>
        </div>

        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4">{{ session('success') }}</div>
        @endif

        {{-- Filters --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.eye-exam-bookings.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">@lang('admin.Search')</label>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                            placeholder="@lang('admin.Name or phone')">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">@lang('admin.Status')</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">@lang('admin.All')</option>
                            @foreach (['pending', 'confirmed', 'done', 'cancelled'] as $st)
                                <option value="{{ $st }}" @selected(request('status') === $st)>
                                    @lang('admin.booking_status_' . $st) ({{ $counts[$st] }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">@lang('admin.Store')</label>
                        <select name="store_id" class="form-select form-select-sm">
                            <option value="">@lang('admin.All')</option>
                            @foreach ($stores as $store)
                                <option value="{{ $store->id }}" @selected((string) request('store_id') === (string) $store->id)>
                                    {{ is_array($store->name) ? $store->name[app()->getLocale()] ?? reset($store->name) : $store->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">@lang('admin.Date')</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button class="btn btn-sm btn-primary w-100">@lang('admin.Filter')</button>
                        <a href="{{ route('admin.eye-exam-bookings.index') }}"
                            class="btn btn-sm btn-light border w-100">@lang('admin.Reset')</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted small fw-bold text-uppercase">@lang('admin.Customer')</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">@lang('admin.Appointment')</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">@lang('admin.Store')</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">@lang('admin.Product')</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase text-center">@lang('admin.Status')</th>
                            <th class="pe-4 py-3 text-muted small fw-bold text-uppercase text-end">@lang('admin.Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary fw-bold"
                                            style="width: 38px; height: 38px; font-size: 0.8rem;">
                                            {{ mb_strtoupper(mb_substr($booking->customer_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark mb-0">{{ $booking->customer_name }}</div>
                                            <a href="tel:{{ $booking->customer_phone }}"
                                                class="text-muted small text-decoration-none" dir="ltr">
                                                <i class="bi bi-telephone me-1"></i>{{ $booking->customer_phone }}
                                            </a>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3">
                                    <div class="fw-semibold text-dark">
                                        {{ optional($booking->booking_date)->format('Y-m-d') }}</div>
                                    <div class="text-muted small"><i
                                            class="bi bi-clock me-1"></i>{{ $booking->booking_time }}</div>
                                    <div class="text-muted small">
                                        {{ $booking->flow === 'store_measurement' ? __('admin.Store measurement') : __('admin.Free eye test') }}
                                    </div>
                                </td>

                                <td class="py-3">
                                    <span class="text-dark small">{{ $booking->store_label ?? '—' }}</span>
                                </td>

                                <td class="py-3">
                                    <div class="text-dark small">{{ $booking->product_name ?? '—' }}</div>
                                    @if ($booking->order_id)
                                        <a href="{{ route('admin.orders.show', $booking->order_id) }}"
                                            class="small text-decoration-none">
                                            <i class="bi bi-receipt me-1"></i>#{{ $booking->order_id }}
                                        </a>
                                    @else
                                        <span class="badge bg-warning bg-opacity-25 text-warning-emphasis">
                                            @lang('admin.No order yet')
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3 text-center">
                                    @php
                                        $badge = [
                                            'pending' => 'bg-secondary',
                                            'confirmed' => 'bg-primary',
                                            'done' => 'bg-success',
                                            'cancelled' => 'bg-danger',
                                        ][$booking->status] ?? 'bg-secondary';
                                    @endphp
                                    <form method="POST"
                                        action="{{ route('admin.eye-exam-bookings.update-status', $booking) }}"
                                        class="d-inline">
                                        @csrf
                                        <select name="status" class="form-select form-select-sm d-inline-block w-auto"
                                            onchange="this.form.submit()">
                                            @foreach (['pending', 'confirmed', 'done', 'cancelled'] as $st)
                                                <option value="{{ $st }}" @selected($booking->status === $st)>
                                                    @lang('admin.booking_status_' . $st)
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>

                                <td class="pe-4 py-3 text-end">
                                    @if ($booking->customer_phone)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $booking->customer_phone) }}"
                                            target="_blank" class="btn btn-sm btn-light border text-success"
                                            title="WhatsApp">
                                            <i class="bi bi-whatsapp"></i>
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ route('admin.eye-exam-bookings.destroy', $booking) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('{{ __('admin.Are you sure?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-light border text-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bi bi-calendar-x fs-3 d-block mb-2 opacity-50"></i>
                                    @lang('admin.No bookings yet')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($bookings->hasPages())
                <div class="card-footer bg-white border-0 py-3 px-4">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
