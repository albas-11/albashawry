<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard')</title>

    {{-- Espace d'administration : jamais indexé --}}
    <meta name="robots" content="noindex, nofollow">


    <link rel="icon"
        href="{{ $globalSettings && $globalSettings->favicon
            ? asset('images/settings/' . $globalSettings->favicon)
            : asset('images/default-favicon.ico') }}"
        type="image/x-icon">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Option RTL -->
    @if (app()->getLocale() === 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&family=Dancing+Script:wght@400..700&family=Roboto:ital,wght@0,100..900;1,100..900&family=Winky+Sans:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">

    @stack('styles')

    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed: 70px;
            --header-height: 56px;
            --border-color: #e5e7eb;
            --text-color: #374151;
            --bg: #f9fafb;
            --white: #ffffff;
            --primary: #2563eb;
        }

        body {
            background: var(--bg);
            font-family: cairo, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            font-size: 13px;
            color: var(--text-color);
        }

        h1 {
            font-size: 1.25rem;
        }

        h2 {
            font-size: 1.1rem;
        }

        h3 {
            font-size: 1rem;
        }

        h4 {
            font-size: .9rem;
        }

        /* =========================
           SIDEBAR
        ========================= */
        .sidebar {
            position: fixed;
            inset-block: 0;
            inset-inline-start: 0;
            /* left in LTR, right in RTL automatically */
            width: var(--sidebar-width);
            max-width: 100%;
            background: var(--white);
            border-inline-end: 1px solid var(--border-color);
            /* replaces border-right/left */
            overflow-y: auto;
            overflow-x: hidden;
            transition: width 0.2s ease;
        }

        .sidebar * {
            max-width: 100%;
            box-sizing: border-box;
        }

        .sidebar-header {
            padding: .9rem 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .nav-item {
            margin: 2px 6px;
        }

        .nav-link {
            color: #64748b !important;
            padding: .45rem .7rem;
            border-radius: 6px;
            font-size: .8rem;
            display: flex;
            align-items: center;
            gap: .5rem;
            transition: all 0.2s ease;
        }

        .nav-link i {
            font-size: .9rem;
            width: 16px;
            text-align: center;
            color: #6b7280;
        }

        .nav-link:hover {
            background-color: #f1f5f9 !important;
            color: #2E3192 !important;
        }

        .nav-link.active {
            background-color: #eef2ff !important;
            color: #2E3192 !important;
            font-weight: 600;
        }

        .nav-link.active i {
            color: #2E3192 !important;
        }

        /* =========================
           MAIN (✅ uses logical margin)
        ========================= */
        .main-content {
            margin-inline-start: var(--sidebar-width);
            transition: margin-inline-start 0.2s ease;
        }

        body.sidebar-collapsed .sidebar {
            width: var(--sidebar-collapsed);
        }

        body.sidebar-collapsed .main-content {
            margin-inline-start: var(--sidebar-collapsed);
        }

        body.sidebar-collapsed .nav-link span,
        body.sidebar-collapsed .sidebar-header span {
            display: none;
        }

        /* =========================
           HEADER
        ========================= */
        .top-header {
            background: var(--white);
            height: var(--header-height);
            border-bottom: 1px solid var(--border-color);
            font-size: .8rem;
        }

        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e5e7eb;
            color: #111827;
            font-weight: 600;
            font-size: .75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* CARDS */
        .card {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: none;
        }

        /* DROPDOWN */
        .dropdown-menu {
            font-size: .8rem;
        }

        /* MOBILE */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1050;
            }

            body.rtl .sidebar {
                transform: translateX(100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin: 0 !important;
            }
        }

        #sidebarCollapse:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            transform: scale(1.05);
        }

        .dropdown-item:hover {
            background-color: #f4f5ff;
            color: #2E3192;
        }

        .dropdown-item.active {
            background-color: #2E3192 !important;
        }

        .transition-all {
            transition: all 0.2s ease-in-out;
        }

        /* Animation du bouton de partage */
        #shareDropdown:hover {
            transform: scale(1.05);
            background-color: #25287a !important;
        }

        @media (max-width: 576px) {

            body {
                font-size: 12px;
            }

            /* SIDEBAR */
            .sidebar {
                width: 260px;
            }

            body.sidebar-open .sidebar {
                transform: translateX(0);
            }

            body:not(.sidebar-open) .sidebar {
                transform: translateX(-100%);
            }

            body.rtl:not(.sidebar-open) .sidebar {
                transform: translateX(100%);
            }

            /* MAIN CONTENT */
            .main-content {
                margin: 0 !important;
            }

            /* HEADER */
            .top-header {
                height: 60px;
                padding: 0 10px;
            }

            .top-header .container-fluid {
                padding: 0 !important;
            }

            /* HIDE TEXTS */
            .top-header .fw-bold,
            .top-header small {
                display: none !important;
            }

            /* CARDS */
            .card,
            .bg-white {
                padding: 1rem !important;
            }

            /* NAV LINKS */
            .nav-link span {
                font-size: 0.85rem;
            }

            /* DROPDOWNS */
            .dropdown-menu {
                font-size: 0.75rem;
            }
        }

        @media (min-width: 768px) and (max-width: 1024px) {

            :root {
                --sidebar-width: 200px;
            }

            body {
                font-size: 12.5px;
            }

            body.sidebar-collapsed .sidebar {
                width: 70px;
            }

            body.sidebar-collapsed .main-content {
                margin-inline-start: 70px;
            }

            .nav-link {
                font-size: 0.8rem;
                padding: 0.45rem 0.6rem;
            }

            .nav-link i {
                font-size: 0.9rem;
            }

            .top-header {
                height: 64px;
            }

            .top-header small {
                display: none;
            }
        }

        .hover-light:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .whitespace-normal {
            white-space: normal !important;
        }

        .btn:focus {
            box-shadow: none !important;
        }

        /* Hide scrollbar */
        .sidebar {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .sidebar::-webkit-scrollbar {
            display: none;
        }

        /* collapsed: hide share button, keep visit button full width */
        body.sidebar-collapsed .sidebar-header .visit-share-row .dropdown {
            display: none !important;
            /* hide share */
        }

        body.sidebar-collapsed .sidebar-header .visit-share-row>a {
            flex: 1;
            width: 100%;
            justify-content: center;
            padding: 8px 0 !important;
        }

        body.sidebar-collapsed .sidebar-header .visit-share-row>a span {
            display: none !important;
            /* hide text */
        }

        body.sidebar-collapsed .sidebar-header .visit-share-row>a i {
            margin: 0 !important;
            /* center icon */
        }
    </style>
</head>

<body class="{{ app()->getLocale() === 'ar' ? 'rtl' : '' }}">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar" style="background-color: #f8f9ff;">
        <div class="sidebar-header d-flex flex-column align-items-center py-4 px-3">
            @php
                $defaultLogo = asset('images/logo.png');
                $savedLogo =
                    $globalSettings && $globalSettings->logo
                        ? public_path('images/settings/' . $globalSettings->logo)
                        : null;

                $logoSrc =
                    $savedLogo && file_exists($savedLogo)
                        ? asset('images/settings/' . $globalSettings->logo)
                        : $defaultLogo;
            @endphp

            <a href="{{ route('admin.dashboard') }}" class="text-2xl mb-3 font-bold text-gray-900 flex items-center">
                <img src="{{ $logoSrc }}" alt="AL Bashawri Logo"
                    style="height: 45px; width: auto; object-fit: contain;">
            </a>

            <div class="d-flex align-items-center w-100 shadow-sm visit-share-row"
                style="background: white; border: 1px solid #2E3192; border-radius: 50px; padding: 2px;">

                <a href="{{ url('/') }}" target="_blank"
                    class="btn flex-grow-1 d-flex align-items-center justify-content-center py-1 ps-3 pe-2"
                    style="color: #2E3192; font-weight: 600; font-size: 0.75rem; border: none; text-decoration: none;">
                    <span>@lang('admin.Visit Site')</span>
                    <i class="bi bi-chevron-right ms-1" style="font-size: 0.6rem;"></i>
                </a>

                <div class="dropdown">
                    <button class="btn d-flex align-items-center justify-content-center p-0" type="button"
                        id="shareDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                        style="background-color: #2E3192; border-radius: 50%; width: 32px; height: 32px; color: white; transition: transform 0.2s;">
                        <i class="bi bi-share" style="font-size: 0.8rem;"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="shareDropdown"
                        style="border-radius: 12px; font-size: 0.8rem;">
                        <li><a class="dropdown-item py-2 d-flex align-items-center" href="#" onclick="copyLink()">
                                <i class="bi bi-clipboard me-2"></i>@lang('admin.Copy Store Link') </a></li>
                        <li><a class="dropdown-item py-2 d-flex align-items-center" href="https://wa.me/966534473335"
                                target="_blank">
                                <i class="bi bi-whatsapp me-2 text-success"></i>@lang('admin.WhatsApp') </a></li>
                        <li><a class="dropdown-item py-2 d-flex align-items-center"
                                href="https://www.tiktok.com/@albashawrisa" target="_blank">
                                <i class="bi bi-tiktok me-2"></i>@lang('admin.Tiktok') </a></li>
                        <li><a class="dropdown-item py-2 d-flex align-items-center"
                                href="https://www.instagram.com/albashawriksa/" target="_blank">
                                <i class="bi bi-instagram me-2 text-danger"></i>@lang('admin.Instagram') </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <nav class="nav flex-column px-2">
            @php
                $menuItems = [
                    [
                        'route' => 'admin.dashboard',
                        'icon' => 'bi-speedometer2',
                        'label' => 'Dashboard',
                        'permission' => null,
                    ],
                    [
                        'route' => 'admin.orders.index',
                        'icon' => 'bi-cart3',
                        'label' => 'Manage Orders',
                        'permission' => 'view_orders',
                    ],
                    [
                        'route' => 'admin.products.index',
                        'icon' => 'bi-box-seam',
                        'label' => 'Manage Products',
                        'permission' => 'manage_products',
                    ],
                    [
                        'route' => 'admin.stores.index',
                        'icon' => 'bi-shop',
                        'label' => 'Manage Stores',
                        'permission' => 'manage_stores',
                    ],
                    [
                        'route' => 'admin.eye-exam-bookings.index',
                        'icon' => 'bi-calendar-check',
                        'label' => 'Eye Exam Bookings',
                        'permission' => 'view_orders',
                    ],
                    [
                        'route' => 'admin.brands.index',
                        'icon' => 'bi-tags',
                        'label' => 'Manage Brands',
                        'permission' => 'manage_brands',
                    ],
                    [
                        'route' => 'admin.categories.index',
                        'icon' => 'bi-grid',
                        'label' => 'Manage Categories',
                        'permission' => 'manage_categories',
                    ],
                    [
                        'route' => 'admin.users.index',
                        'icon' => 'bi-people',
                        'label' => 'Manage Users',
                        'permission' => 'manage_users',
                    ],
                    [
                        'route' => 'admin.clients.index',
                        'icon' => 'bi-person-badge',
                        'label' => 'Manage Clients',
                        'permission' => 'manage_clients',
                    ],
                    [
                        'route' => 'admin.tryon.index',
                        'icon' => 'bi-camera-video',
                        'label' => 'Virtual Try-On Files',
                        'permission' => 'manage_tryon',
                    ],
                    [
                        'route' => 'admin.reviews.index',
                        'icon' => 'bi-star',
                        'label' => 'Manage Reviews',
                        'permission' => 'manage_reviews',
                    ],
                    [
                        'route' => 'admin.contact-messages.index',
                        'icon' => 'bi-envelope',
                        'label' => 'Contact Messages',
                        'permission' => 'manage_contacts',
                    ],
                    [
                        'route' => 'admin.pixels.index',
                        'icon' => 'bi-pie-chart',
                        'label' => 'Pixels',
                        'permission' => 'manage_pixels',
                    ],
                ];
            @endphp

            @foreach ($menuItems as $item)
                @if (!$item['permission'] || auth()->user()->can($item['permission']))
                    <div class="nav-item mb-1">
                        <a href="{{ route($item['route']) }}"
                            class="nav-link d-flex align-items-center gap-3 px-3 py-2 {{ request()->routeIs($item['route'] . '*') ? 'active' : '' }}"
                            style="border-radius: 10px;">
                            <i class="bi {{ $item['icon'] }} fs-5"
                                style="color: {{ request()->routeIs($item['route'] . '*') ? '#2E3192' : '#64748b' }}; width: 20px;"></i>
                            <span
                                style="font-weight: {{ request()->routeIs($item['route'] . '*') ? '600' : '500' }}; font-size: 0.85rem;">
                                @lang('message.' . $item['label'])
                            </span>
                        </a>
                    </div>
                @endif
            @endforeach

            <hr class="mx-3 my-3" style="border-top: 1px solid #e2e8f0; opacity: 1;">

            @role('Admin')
                <div class="nav-item mb-1">
                    <a href="{{ route('admin.pages.index') }}"
                        class="nav-link d-flex align-items-center gap-3 px-3 py-2 {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}"
                        style="border-radius: 10px;">
                        <i class="bi bi-file-earmark-text fs-5"
                            style="color: {{ request()->routeIs('admin.pages.*') ? '#2E3192' : '#64748b' }}; width: 20px;"></i>
                        <span
                            style="font-weight: {{ request()->routeIs('admin.pages.*') ? '600' : '500' }}; font-size: 0.85rem;">@lang('message.Site Pages')</span>
                    </a>
                </div>

                <div class="nav-item mb-2">
                    <a href="{{ route('admin.settings') }}"
                        class="nav-link d-flex align-items-center gap-3 px-3 py-2 {{ request()->routeIs('admin.settings') ? 'active' : '' }}"
                        style="border-radius: 10px;">
                        <i class="bi bi-gear fs-5" style="color: #64748b; width: 20px;"></i>
                        <span style="font-weight: 500; font-size: 0.85rem;">@lang('message.Settings')</span>
                    </a>
                </div>
            @endrole

        </nav>
    </aside>

    <!-- MAIN -->
    <main class="main-content">
        <header class="top-header"
            style="background-color: #2E3192; height: 70px; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <div class="container-fluid h-100 d-flex justify-content-between align-items-center px-4">

                <button class="btn border-0 d-flex align-items-center justify-content-center" id="sidebarCollapse"
                    style="color: #f4f5ff; background: rgba(255,255,255,0.1); width: 40px; height: 40px; border-radius: 10px; transition: 0.3s;">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <div class="d-flex align-items-center gap-4">

                    <div class="dropdown">
                        <button
                            class="btn border-0 d-flex align-items-center gap-2 px-3 py-1.5 rounded-pill transition-all"
                            style="background: rgba(255,255,255,0.1); color: #ffffff; font-size: 0.85rem;"
                            type="button" id="langDropdown" data-bs-toggle="dropdown">
                            <img src="{{ asset(app()->getLocale() == 'ar' ? 'images/drapeau1.webp' : 'images/drapeau2.webp') }}"
                                class="rounded-sm" style="width:20px; height:14px; object-fit: cover;">
                            <span class="fw-medium text-uppercase">{{ app()->getLocale() }}</span>
                            <i class="bi bi-chevron-down small opacity-50"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 py-2"
                            style="border-radius: 12px; min-width: 150px;">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                    href="{{ route('language.switch', 'en') }}">
                                    <img src="{{ asset('images/drapeau2.webp') }}" style="width:18px;"> English
                                    @if (app()->getLocale() == 'en')
                                        <i class="bi bi-check2 ms-auto text-primary"></i>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                    href="{{ route('language.switch', 'ar') }}">
                                    <img src="{{ asset('images/drapeau1.webp') }}" style="width:18px;"> العربية
                                    @if (app()->getLocale() == 'ar')
                                        <i class="bi bi-check2 ms-auto text-primary"></i>
                                    @endif
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="dropdown">
                        <a class="position-relative d-flex align-items-center justify-content-center rounded-circle"
                            href="#" role="button" data-bs-toggle="dropdown"
                            style="color: #ffffff; transition: 0.3s;">
                            <i class="bi bi-bell fs-5"></i>
                            @if (auth()->user()->unreadNotifications->count() > 0)
                                <span
                                    class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-danger"
                                    style="font-size: 0.6rem; padding: 0.35em 0.5em; border: 2px solid #2E3192;">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0 mt-3 overflow-hidden"
                            style="width: 320px; border-radius: 16px;">
                            <div class="px-3 py-3 border-bottom bg-light">
                                <h6 class="mb-0 fw-bold">@lang('admin.notifications')</h6>
                            </div>
                            <div style="max-height: 300px; overflow-y: auto;">
                                @forelse (auth()->user()->unreadNotifications->take(5) as $notification)
                                    <a href="{{ route('admin.notifications.open', $notification->id) }}"
                                        class="list-group-item list-group-item-action py-3 px-4 transition-all unread-item"
                                        style="border-left: 3px solid {{ $notification->data['color'] ?? 'transparent' }};">
                                        <p class="mb-1 small text-dark fw-medium" style="line-height: 1.4;">
                                            {{ $notification->data['message'] }}</p>
                                        <small class="text-muted"><i
                                                class="bi bi-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}</small>
                                    </a>
                                @empty
                                    <div class="p-4 text-center text-muted">
                                        <i class="bi bi-bell-slash d-block fs-3 opacity-20 mb-2"></i>
                                        <p class="small mb-0">@lang('admin.no_notifications')</p>
                                    </div>
                                @endforelse
                            </div>
                            <a href="{{ route('admin.notifications.index') }}"
                                class="dropdown-item text-center py-2 small fw-bold text-primary bg-light">
                                @lang('admin.view_all')
                            </a>
                        </div>
                    </div>

                    <div class="dropdown">
                        <button
                            class="btn d-flex align-items-center gap-3 border-0 p-1 pe-3 rounded-pill transition-all"
                            type="button" data-bs-toggle="dropdown"
                            style="background: rgba(255,255,255,0.1); color: #ffffff;">
                            <div class="d-flex align-items-center justify-content-center rounded-circle shadow-sm"
                                style="width: 36px; height: 36px; background: #ffffff; color: #2E3192; font-weight: 800; font-size: 0.9rem;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="text-start d-none d-md-block">
                                <span class="d-block fw-semibold"
                                    style="font-size: 0.85rem; line-height: 1;">{{ Auth::user()->name }}</span>
                                <small class="opacity-70" style="font-size: 0.7rem;">@lang('admin.Admin')</small>
                            </div>
                            <i class="bi bi-chevron-down small opacity-50 d-none d-md-block"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-2"
                            style="border-radius: 12px; min-width: 200px;">
                            <li>
                                <a class="dropdown-item rounded-2 py-2" href="{{ route('admin.profile') }}">
                                    <i class="bi bi-person me-2"></i>@lang('admin.My Profile')
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-2 py-2" href="{{ route('admin.settings') }}">
                                    <i class="bi bi-gear me-2"></i>@lang('admin.Settings')
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider opacity-50">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item rounded-2 py-2 text-danger d-flex align-items-center">
                                        <i class="bi bi-box-arrow-right me-2"></i>@lang('admin.Logout')
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </header>

        <section class="container-fluid py-4" style="background-color: #f4f6f9; min-height: calc(100vh - 70px);">
            <div class="bg-white p-4 shadow-sm" style="border-radius: 12px; border: 1px solid rgba(0,0,0,0.05);">
                @yield('content')
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const collapseBtn = document.getElementById('sidebarCollapse');
        const body = document.body;

        function isMobile() {
            return window.innerWidth <= 767;
        }

        collapseBtn.addEventListener('click', () => {
            // MOBILE → off-canvas
            if (isMobile()) {
                body.classList.toggle('sidebar-open');
            }
            // TABLET & DESKTOP → collapse
            else {
                body.classList.toggle('sidebar-collapsed');
            }
        });

        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', function(e) {
            if (
                isMobile() &&
                body.classList.contains('sidebar-open') &&
                !e.target.closest('#sidebar') &&
                !e.target.closest('#sidebarCollapse')
            ) {
                body.classList.remove('sidebar-open');
            }
        });
    </script>

    @stack('scripts')

</body>

</html>
