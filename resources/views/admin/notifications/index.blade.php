@extends('layouts.admin')

@section('title', __('admin.Notifications'))

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h4 fw-bold mb-1 text-dark">
                    @lang('admin.Notifications')
                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger ms-2" style="font-size: 0.8rem;">
                        {{ $unreadCount }} @lang('admin.New')
                    </span>
                </h1>
                <p class="text-muted small mb-0">@lang('admin.Stay updated with the latest activities and alerts')</p>
            </div>

            @if ($unreadCount > 0)
                <form action="{{ route('admin.notifications.readAll') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn px-4 shadow-sm btn-sm"
                        style="background-color: #2E3192; color:aliceblue">
                        <i class="fas fa-check-double me-2"></i>@lang('admin.Mark all as read')
                    </button>
                </form>
            @endif
        </div>

        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="list-group list-group-flush">
                @forelse($notifications as $notification)
                    @php
                        $url = $notification->data['url'] ?? '#';
                        $isUnread = !$notification->read_at;
                    @endphp

                    <a href="{{ $url }}"
                        class="list-group-item list-group-item-action py-3 px-4 transition-all {{ $isUnread ? 'unread-item' : '' }}"
                        style="border-left: 3px solid {{ $notification->data['color'] ?? 'transparent' }};"
                        @if ($isUnread) onclick="markAsRead({{ $notification->id }})" @endif>
                        <div class="d-flex align-items-start">
                            <div class="bg-{{ $notification->data['color'] ?? 'primary' }} bg-opacity-10 text-{{ $notification->data['color'] ?? 'primary' }} rounded-circle p-3 me-3 d-flex align-items-center justify-content-center"
                                style="width: 45px; height: 45px; flex-shrink: 0;">
                                <i class="fas fa-{{ $notification->data['icon'] ?? 'bell' }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 fw-bold {{ $isUnread ? 'text-dark' : 'text-secondary' }}">
                                        {{ $notification->data['title'] ?? __('admin.Notification') }}
                                    </h6>
                                    <small class="text-muted fw-light">
                                        <i class="far fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <p class="mb-0 text-muted small leading-relaxed">
                                    {{ $notification->data['message'] }}
                                </p>
                            </div>
                        </div>
                    </a>

                @empty
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-3x text-light mb-3"></i>
                        <h6 class="text-muted fw-light">@lang('admin.No notifications yet.')</h6>
                    </div>
                @endforelse

            </div>

            @if (method_exists($notifications, 'links') && $notifications->hasPages())
                <div class="card-footer bg-white border-top-0 py-4 d-flex justify-content-center">
                    <div class="custom-pagination">
                        {{ $notifications->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
    <style>
        /* Style de la pagination personnalisée */
        .custom-pagination .pagination {
            margin-bottom: 0;
            gap: 5px;
            /* Espace entre les boutons */
        }

        .custom-pagination .page-item .page-link {
            border: none;
            border-radius: 8px !important;
            padding: 8px 16px;
            color: #495057;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .custom-pagination .page-item.active .page-link {
            background-color: #2E3192 !important;
            /* Votre couleur violette */
            border-color: #2E3192 !important;
            color: white !important;
            box-shadow: 0 4px 10px rgba(46, 49, 146, 0.3);
        }

        .custom-pagination .page-item .page-link:hover {
            background-color: #f8f9fa;
            color: #2E3192;
            transform: translateY(-2px);
        }

        .custom-pagination .page-item.disabled .page-link {
            background-color: transparent;
            color: #dee2e6;
        }

        .unread-item {
            background-color: #f8f9fa !important;
        }

        .unread-item:hover {
            background-color: #f1f3f5 !important;
        }

        .list-group-item {
            border-bottom: 1px solid #f2f4f7 !important;
            transition: all 0.2s ease;
        }

        .list-group-item:last-child {
            border-bottom: none !important;
        }

        .list-group-item-action:hover {
            z-index: 1;
            transform: translateX(4px);
        }

        .leading-relaxed {
            line-height: 1.6;
        }
    </style>
@endsection
