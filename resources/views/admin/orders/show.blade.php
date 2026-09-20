@extends('layouts.admin')

@section('title', __('admin.order_details_title', ['number' => $order->order_number]))

@section('content')
    <div class="container-fluid py-4">
        {{-- Top Header --}}
        <div class="row align-items-center mb-4">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}"
                                class="text-muted text-decoration-none">@lang('admin.Orders')</a></li>
                        <li class="breadcrumb-item active" aria-current="page">#{{ $order->order_number }}</li>
                    </ol>
                </nav>
                <h1 class="h3 fw-bold text-dark mb-0">@lang('admin.order_number', ['number' => $order->order_number])</h1>
                <span class="text-muted small"><i class="far fa-calendar-alt me-1"></i>
                    {{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="col-auto">
                @php
                    $statusColors = [
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'shipped' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                    ];
                    $color = $statusColors[$order->status] ?? 'secondary';
                @endphp
                <span
                    class="badge rounded-pill bg-{{ $color }} bg-opacity-10 text-{{ $color }} border border-{{ $color }} px-3 py-2 fw-semibold">
                    <i class="fas fa-circle small me-1"></i> @lang('admin.order_status.' . $order->status)
                </span>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if (session('success') || session('error'))
            <div
                class="alert alert-{{ session('success') ? 'success' : 'danger' }} border-0 shadow-sm alert-dismissible fade show mb-4">
                {{ session('success') ?? session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            {{-- Main Column --}}
            <div class="col-lg-8">
                {{-- Order Items Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0">@lang('admin.order_items')</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap mb-0">
                                <thead class="bg-light text-muted small text-uppercase font-monospace">
                                    <tr>
                                        <th class="ps-4 py-3 border-0">@lang('admin.product')</th>
                                        <th class="text-center border-0">@lang('admin.quantity')</th>
                                        <th class="text-end border-0">@lang('admin.price')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->items as $item)
                                        @php
                                            $lens = json_decode($item->lens_type ?? '', true);
                                            $lens = is_array($lens) ? $lens : [];
                                            $lensFlow = $lens['flow'] ?? null;
                                            $isBooking = in_array($lensFlow, ['book_eye_test', 'store_measurement'], true);
                                            $lensStoreName = $lens['store']['name'] ?? null;
                                            if (is_array($lensStoreName)) {
                                                $lensStoreName =
                                                    $lensStoreName[app()->getLocale()] ?? (reset($lensStoreName) ?: null);
                                            }
                                        @endphp
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center">

                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $item->product_name }}</div>
                                                        <small class="text-muted">SKU:
                                                            {{ $item->product->sku ?? 'N/A' }}</small>

                                                        @if ($isBooking)
                                                            <div class="mt-2 p-2 rounded-3 border bg-light bg-opacity-50"
                                                                style="max-width: 380px;">
                                                                <div class="fw-bold small text-primary mb-1">
                                                                    <i class="bi bi-calendar-check me-1"></i>
                                                                    {{ $lensFlow === 'store_measurement' ? __('admin.Store measurement') : __('admin.Free eye test') }}
                                                                </div>
                                                                <div class="small text-muted">
                                                                    <i class="bi bi-shop me-1"></i>{{ $lensStoreName ?? '—' }}
                                                                </div>
                                                                <div class="small text-muted">
                                                                    <i class="bi bi-clock me-1"></i>
                                                                    {{ $lens['schedule']['day'] ?? '—' }}
                                                                    — {{ $lens['schedule']['slot'] ?? '—' }}
                                                                </div>
                                                                @if (!empty($lens['customer_name']) || !empty($lens['customer_phone']))
                                                                    <div class="small text-muted">
                                                                        <i class="bi bi-person me-1"></i>
                                                                        {{ $lens['customer_name'] ?? '—' }}
                                                                        @if (!empty($lens['customer_phone']))
                                                                            <span dir="ltr">
                                                                                — {{ $lens['customer_phone'] }}</span>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                                <a href="{{ route('admin.eye-exam-bookings.index', ['q' => $lens['customer_phone'] ?? ($order->shipping_phone ?? '')]) }}"
                                                                    class="small text-decoration-none">
                                                                    @lang('admin.Eye Exam Bookings') →
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center fw-medium">{{ $item->quantity }}</td>
                                            <td class="text-end">{{ number_format($item->price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- Summary Footer --}}
                    <div class="card-footer bg-light bg-opacity-50 border-0 p-4">
                        <div class="row justify-content-end">
                            <div class="col-md-5 col-lg-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">@lang('admin.subtotal')</span>
                                    <span class="fw-medium">{{ number_format($order->subtotal ?? 0, 2) }} SAR</span>
                                </div>
                                @if (isset($order->tax))
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">@lang('admin.tax')</span>
                                        <span class="fw-medium">{{ number_format($order->tax, 2) }} SAR</span>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">@lang('admin.shipping')</span>
                                    <span class="fw-medium text-success">+ {{ number_format($order->shipping_cost, 2) }}
                                        SAR</span>
                                </div>
                                <hr class="my-3 opacity-10">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 fw-bold mb-0">@lang('admin.total')</span>
                                    <span class="h6 fw-bold mb-0" style="color: red">{{ number_format($order->total, 2) }}
                                        SAR</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment & Shipping Details --}}
                <div class="row g-4">
                   <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-transparent border-0 pt-4 px-4">
                                <h6 class="fw-bold mb-0 text-uppercase small tracking-wider text-muted">@lang('admin.payment_information') (Tap Payments)
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                {{-- الدفع عند الاستلام --}}
                                @if ($order->payment_method === 'cod')
                                    <div class="mb-3">
                                        <small class="text-muted d-block">@lang('admin.payment_status')</small>
                                        <span class="badge bg-warning bg-opacity-10 text-warning fw-bold">UNPAID (COD) - الدفع عند الاستلام</span>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted d-block">طريقة الدفع</small>
                                        <strong class="text-dark"><i class="fas fa-money-bill-wave text-success me-1"></i> Cash on Delivery</strong>
                                    </div>

                                {{-- الدفع الإلكتروني عبر Tap --}}
                                @elseif ($order->tapTransaction)
                                    @php
                                        $tapStatus = strtolower($order->tapTransaction->status);
                                        $badgeClass = ($tapStatus === 'paid' || $tapStatus === 'captured') ? 'success' : ($tapStatus === 'failed' ? 'danger' : 'warning');
                                        $statusText = ($tapStatus === 'paid' || $tapStatus === 'captured') ? 'PAID (مدفوع)' : ($tapStatus === 'failed' ? 'FAILED (فشل)' : 'PENDING (قيد الانتظار)');
                                    @endphp
                                    <div class="mb-3">
                                        <small class="text-muted d-block">@lang('admin.payment_status')</small>
                                        <span class="badge bg-{{ $badgeClass }} bg-opacity-10 text-{{ $badgeClass }} fw-bold">
                                            <i class="fas fa-circle small me-1"></i> {{ $statusText }}
                                        </span>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted d-block">المبلغ عبر البوابة</small>
                                        <strong class="text-dark">{{ $order->tapTransaction->amount ?? $order->total }} {{ $order->tapTransaction->currency ?? 'SAR' }}</strong>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">الرقم المرجعي (Charge ID)</small>
                                        <code class="text-primary small">{{ $order->tapTransaction->charge_id }}</code>
                                    </div>

                                {{-- في حال لم يتم تسجيل الحركة بعد --}}
                                @else
                                    <div class="text-center py-3">
                                        <i class="fas fa-credit-card fa-2x text-light mb-2"></i>
                                        <p class="text-muted small">@lang('admin.payment_pending')</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-uppercase small tracking-wider text-muted">@lang('admin.shipping_information_oto')</h6>
                                @php $otoMode = \App\Models\Setting::getInstance()->oto_mode ?? 'test'; @endphp
                                @if ($otoMode === 'test')
                                    <span class="badge bg-warning text-dark small">TEST MODE</span>
                                @else
                                    <span class="badge bg-success small">LIVE</span>
                                @endif
                            </div>
                            <div class="card-body p-4">
                                @if ($order->otoShipment)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3 text-primary">
                                            <i class="fas fa-truck fa-lg"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">@lang('admin.carrier')</small>
                                            <strong class="text-dark">{{ $order->otoShipment->carrier_name ?? 'OTO' }}</strong>
                                            <span class="ms-2">{!! $order->otoShipment->status_badge !!}</span>
                                        </div>
                                    </div>

                                    @if ($order->otoShipment->oto_id)
                                        <a href="{{ route('shipping.track.oto', ['otoId' => $order->otoShipment->oto_id]) }}"
                                            class="btn btn-outline-primary btn-sm w-100 rounded-pill mb-2">
                                            <i class="fas fa-map-marker-alt me-1"></i> @lang('admin.track_shipment')
                                        </a>
                                    @endif

                                    @if ($order->otoShipment->label_url)
                                        <a href="{{ $order->otoShipment->label_url }}" target="_blank"
                                            class="btn btn-success btn-sm w-100 rounded-pill mb-2">
                                            <i class="fas fa-download me-1"></i> @lang('admin.download_label')
                                        </a>
                                    @elseif($order->otoShipment->oto_id)
                                        {{-- otoId saved but label not yet available --}}
                                        <div class="alert alert-info py-2 px-3 small mb-2">
                                            <i class="fas fa-box me-1"></i>
                                            OTO ID: <strong>{{ $order->otoShipment->oto_id }}</strong>
                                        </div>
                                        <button type="button" id="fetchLabelBtn"
                                            class="btn btn-primary btn-sm w-100 rounded-pill mb-2"
                                            onclick="fetchOtoLabel()">
                                            <i class="fas fa-download me-1"></i> تحميل بوليصة الشحن (AWB)
                                        </button>
                                        <a href="https://app.tryoto.com/shipments" target="_blank"
                                            class="btn btn-outline-secondary btn-sm w-100 rounded-pill mb-2">
                                            <i class="fas fa-external-link-alt me-1"></i> OTO Dashboard
                                        </a>
                                        <div id="labelResult" class="mt-2"></div>

                                        {{-- Reset button (hidden by default, shown if shipment not found) --}}
                                        <form id="resetOtoForm"
                                            action="{{ route('admin.oto.resetShipment', $order) }}"
                                            method="POST" style="display:none" class="mt-2">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm w-100 rounded-pill"
                                                onclick="return confirm('هل أنت متأكد؟ سيتم حذف بيانات OTO وإعادة إنشاء الشحنة.')">
                                                <i class="fas fa-redo me-1"></i> إعادة إنشاء الشحنة
                                            </button>
                                        </form>

                                        <script>
                                        function fetchOtoLabel() {
                                            const btn = document.getElementById('fetchLabelBtn');
                                            btn.disabled = true;
                                            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جاري الجلب...';

                                            fetch('{{ route('admin.oto.fetchLabel', $order) }}', {
                                                headers: {
                                                    'Accept': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                }
                                            })
                                            .then(r => r.json())
                                            .then(data => {
                                                const result = document.getElementById('labelResult');
                                                if (data.success && data.label_url) {
                                                    result.innerHTML = '<a href="' + data.label_url + '" target="_blank" class="btn btn-success btn-sm w-100 rounded-pill"><i class="fas fa-file-pdf me-1"></i> فتح/تحميل البوليصة</a>';
                                                    btn.style.display = 'none';
                                                } else {
                                                    result.innerHTML = '<div class="alert alert-warning py-2 px-3 small">' + (data.error || 'البوليصة غير جاهزة بعد، حاول مجدداً.') + '</div>';
                                                    btn.disabled = false;
                                                    btn.innerHTML = '<i class="fas fa-download me-1"></i> تحميل بوليصة الشحن (AWB)';
                                                    // If OTO says shipment doesn't exist, show reset button
                                                    if (data.no_shipment) {
                                                        document.getElementById('resetOtoForm').style.display = 'block';
                                                    }
                                                }
                                            })
                                            .catch(e => {
                                                document.getElementById('labelResult').innerHTML = '<div class="alert alert-danger py-2 px-3 small">خطأ: ' + e.message + '</div>';
                                                btn.disabled = false;
                                                btn.innerHTML = '<i class="fas fa-download me-1"></i> تحميل بوليصة الشحن (AWB)';
                                            });
                                        }
                                        </script>
                                    @else
                                        {{-- otoShipment exists but no oto_id yet --}}
                                        @if ($otoMode === 'test')
                                            <div class="alert alert-warning py-2 px-3 small mb-2">
                                                <i class="fas fa-flask me-1"></i>
                                                <strong>وضع تجريبي:</strong> OTO استقبل الطلب لكن البوليصة لا تُولَّد في sandbox.
                                                للاختبار الكامل استخدم <strong>Live</strong>.
                                            </div>
                                        @endif
                                        <button type="button" id="fetchLabelBtn"
                                            class="btn btn-primary btn-sm w-100 rounded-pill mb-2"
                                            onclick="fetchOtoLabel()">
                                            <i class="fas fa-download me-1"></i> تحميل بوليصة الشحن (AWB)
                                        </button>
                                        <div id="labelResult" class="mt-2"></div>
                                        <form id="resetOtoForm"
                                            action="{{ route('admin.oto.resetShipment', $order) }}"
                                            method="POST" class="mt-2">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm w-100 rounded-pill"
                                                onclick="return confirm('سيتم حذف بيانات OTO وإعادة إنشاء الشحنة.')">
                                                <i class="fas fa-redo me-1"></i> إعادة إنشاء الشحنة
                                            </button>
                                        </form>
                                        <script>
                                        function fetchOtoLabel() {
                                            const btn = document.getElementById('fetchLabelBtn');
                                            btn.disabled = true;
                                            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جاري الجلب...';
                                            fetch('{{ route('admin.oto.fetchLabel', $order) }}', {
                                                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                            })
                                            .then(r => r.json())
                                            .then(data => {
                                                const result = document.getElementById('labelResult');
                                                if (data.success && data.label_url) {
                                                    result.innerHTML = '<a href="' + data.label_url + '" target="_blank" class="btn btn-success btn-sm w-100 rounded-pill"><i class="fas fa-file-pdf me-1"></i> فتح/تحميل البوليصة</a>';
                                                    btn.style.display = 'none';
                                                } else {
                                                    result.innerHTML = '<div class="alert alert-warning py-2 px-3 small">' + (data.error || 'البوليصة غير جاهزة. جرّب إعادة إنشاء الشحنة.') + '</div>';
                                                    btn.disabled = false;
                                                    btn.innerHTML = '<i class="fas fa-download me-1"></i> تحميل بوليصة الشحن (AWB)';
                                                }
                                            })
                                            .catch(e => {
                                                document.getElementById('labelResult').innerHTML = '<div class="alert alert-danger py-2 px-3 small">خطأ: ' + e.message + '</div>';
                                                btn.disabled = false;
                                                btn.innerHTML = '<i class="fas fa-download me-1"></i> تحميل بوليصة الشحن (AWB)';
                                            });
                                        }
                                        </script>
                                    @endif
                                @else
                                    {{-- No shipment yet — show create form --}}
                                    @if ($otoMode === 'test')
                                        <div class="alert alert-warning py-2 px-3 small mb-3">
                                            <i class="fas fa-flask me-1"></i>
                                            <strong>وضع تجريبي:</strong> البوليصة لن تُولَّد في sandbox.
                                            للبوليصة الحقيقية غيّر الوضع إلى Live في
                                            <a href="{{ route('admin.settings') }}">الإعدادات</a>.
                                        </div>
                                    @endif

                                    {{-- City field — always visible --}}
                                    <div class="mb-3">
                                        <label class="form-label small text-muted fw-bold">
                                            <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                                            مدينة التوصيل (سعودية)
                                        </label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" id="cityOverride"
                                                class="form-control"
                                                placeholder="Riyadh / Jeddah / Dammam ..."
                                                value="{{ in_array(strtolower($order->shipping_city ?? ''), ['riyadh','jeddah','dammam','mecca','medina','khobar','tabuk','abha','taif']) ? $order->shipping_city : 'Riyadh' }}">
                                            <button type="button" id="retryOtoBtn" class="btn btn-outline-primary rounded-end">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                        @if ($otoError)
                                            <div class="text-danger small mt-1">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $otoError }}
                                            </div>
                                        @endif
                                    </div>

                                    <form action="{{ route('admin.oto.createShipment', $order) }}" method="POST" id="otoShipForm">
                                        @csrf
                                        <input type="hidden" name="override_city" id="overrideCityInput" value="">

                                        <div class="mb-3">
                                            <label class="form-label small text-muted">@lang('admin.choose_delivery_option')</label>
                                            <select name="delivery_option_id" class="form-select form-select-sm" id="otoOptionsSelect" required>
                                                <option value="">-- اضغط بحث أولاً --</option>
                                                @foreach ($otoOptions ?? [] as $opt)
                                                    <option value="{{ $opt['id'] }}">
                                                        {{ $opt['carrier_name'] }}
                                                        {{ $opt['delivery_time'] ? '(' . $opt['delivery_time'] . ')' : '' }}
                                                        — {{ number_format($opt['cost'], 2) }} SAR
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <button class="btn btn-dark btn-sm w-100 rounded-pill shadow-sm" id="createShipBtn"
                                            {{ empty($otoOptions) ? 'disabled' : '' }}>
                                            <i class="fas fa-shipping-fast me-1"></i>
                                            @lang('admin.create_oto_shipment')
                                        </button>
                                    </form>

                                    <script>
                                    function fetchOtoOptions(city) {
                                        const btn     = document.getElementById('retryOtoBtn');
                                        const select  = document.getElementById('otoOptionsSelect');
                                        const shipBtn = document.getElementById('createShipBtn');

                                        btn.disabled = true;
                                        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                                        fetch('{{ route('shipping.calculate') }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json',
                                            },
                                            body: JSON.stringify({
                                                city: city,
                                                country: 'SA',
                                                payment_method: '{{ $order->payment_method }}',
                                                order_total: {{ $order->total }},
                                            }),
                                        })
                                        .then(r => r.json())
                                        .then(data => {
                                            select.innerHTML = '<option value="">-- Select --</option>';
                                            if (data.success && data.options?.length) {
                                                data.options.forEach(opt => {
                                                    const o = document.createElement('option');
                                                    o.value = opt.id;
                                                    o.textContent = opt.carrier_name +
                                                        (opt.delivery_time ? ' (' + opt.delivery_time + ')' : '') +
                                                        ' — ' + parseFloat(opt.cost).toFixed(2) + ' SAR';
                                                    select.appendChild(o);
                                                });
                                                shipBtn.disabled = false;
                                                document.getElementById('overrideCityInput').value = city;
                                            } else {
                                                select.innerHTML = '<option value="">-- لا توجد خيارات لهذه المدينة --</option>';
                                                shipBtn.disabled = true;
                                            }
                                        })
                                        .catch(e => alert('Network error: ' + e.message))
                                        .finally(() => {
                                            btn.disabled = false;
                                            btn.innerHTML = '<i class="fas fa-search"></i>';
                                        });
                                    }

                                    document.getElementById('retryOtoBtn').addEventListener('click', function () {
                                        const city = document.getElementById('cityOverride').value.trim();
                                        if (city) fetchOtoOptions(city);
                                    });

                                    // Auto-load if no options yet
                                    @if (empty($otoOptions))
                                    window.addEventListener('DOMContentLoaded', function () {
                                        const city = document.getElementById('cityOverride').value.trim();
                                        if (city) fetchOtoOptions(city);
                                    });
                                    @endif
                                    </script>

                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="col-lg-4">
                {{-- Quick Actions --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 text-dark">@lang('admin.actions')</h6>
                        @if ($order->status === 'pending')
                            <form action="{{ route('admin.orders.approve', $order) }}" method="POST" class="mb-2">
                                @csrf
                                <button class="btn w-100 rounded-pill py-2 shadow-sm fw-bold"
                                    style="background-color: #2E3192; color: white">@lang('admin.confirm_order')</button>
                            </form>
                        @endif
                        <div class="dropdown">
                            <button class="btn btn-light w-100 rounded-pill"
                                data-bs-toggle="dropdown">@lang('admin.more_actions')</button>
                            <ul class="dropdown-menu border-0 shadow w-100">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.orders.print', $order) }}"
                                        target="_blank">
                                        @lang('admin.print_invoice')
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><button class="dropdown-item text-danger"
                                        onclick="confirmCancel()">@lang('admin.cancel_order')</button></li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Customer Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 text-dark">@lang('admin.customer')</h6>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-soft-info p-3 rounded-circle me-3 fw-bold text-primary"
                                style="background: #e7f3ff; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                {{ substr($order->user->name ?? 'G', 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark">{{ $order->user->name ?? __('admin.guest') }}</div>
                                <p class="small text-dark mb-0"><i class="fas fa-map-marker-alt text-danger me-1"></i>
                                    {{ $order->shipping_email }}</p>
                                <p class="small text-dark mb-0"><i class="fas fa-map-marker-alt text-danger me-1"></i>
                                    {{ $order->shipping_phone }}</p>
                            </div>
                        </div>
                        <hr class="opacity-10">
                        <div class="mb-3">
                            <small class="text-muted d-block small text-uppercase fw-bold mb-1">@lang('admin.shipping_address')</small>
                            <p class="small text-dark mb-0"><i class="fas fa-map-marker-alt text-danger me-1"></i>
                                <strong> ADRESS :</strong> {{ $order->shipping_address }}
                            </p>
                            <p class="small text-dark mb-0"><i class="fas fa-map-marker-alt text-danger me-1"></i>
                                <strong>CITY :</strong> {{ $order->shipping_city }}
                            </p>

                        </div>
                    </div>
                </div>

                {{-- Timeline Card --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 text-dark">@lang('admin.timeline')</h6>
                        <div class="modern-timeline">
                            <div class="timeline-item active">
                                <div class="timeline-point"></div>
                                <div class="timeline-info">
                                    <span class="d-block small fw-bold">@lang('admin.order_created')</span>
                                    <span class="text-muted smaller">{{ $order->created_at->format('d M, H:i') }}</span>
                                </div>
                            </div>
                            @if ($order->status == 'delivered')
                                <div class="timeline-item active">
                                    <div class="timeline-point"></div>
                                    <div class="timeline-info">
                                        <span class="d-block small fw-bold text-success">@lang('admin.order_delivered')</span>
                                        <span
                                            class="text-muted smaller">{{ $order->updated_at->format('d M, H:i') }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Modern Elegant CSS */
        .bg-soft-info {
            background-color: #eef2f7;
        }

        .tracking-wider {
            letter-spacing: 0.05em;
        }

        .smaller {
            font-size: 0.7rem;
        }

        .modern-timeline {
            position: relative;
            padding-left: 20px;
        }

        .modern-timeline::before {
            content: '';
            position: absolute;
            left: 4px;
            top: 0;
            width: 2px;
            height: 100%;
            background: #e9ecef;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }

        .timeline-point {
            position: absolute;
            left: -20px;
            top: 4px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #dee2e6;
            border: 2px solid #fff;
        }

        .timeline-item.active .timeline-point {
            background: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2);
        }

        .card {
            transition: transform 0.2s ease;
        }

        .table thead th {
            font-weight: 600;
            color: #6c757d;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            content: "›";
        }
    </style>
@endsection
