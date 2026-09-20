@extends('layouts.admin')

@section('title', __('admin.Contact Messages'))

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="fw-bold h4 mb-1 text-dark">@lang('admin.Customer Inquiries')</h1>
            <p class="text-muted small mb-0">
                @lang('admin.Manage and respond to messages from your contact form')
            </p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-white border text-dark px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-inbox me-1 text-primary"></i>
                {{ count($messages) }} @lang('admin.Messages')
            </span>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-muted small fw-bold text-uppercase" style="width: 40%">
                            @lang('admin.Sender')
                        </th>
                        <th class="py-3 text-muted small fw-bold text-uppercase" style="width: 25%">
                            @lang('admin.Message Content')
                        </th>
                        <th class="py-3 text-muted small fw-bold text-uppercase text-center" style="width: 20%">
                            @lang('admin.Received Date')
                        </th>
                        <th class="pe-4 py-3 text-muted small fw-bold text-uppercase text-end" style="width: 15%">
                            @lang('admin.Actions')
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($messages as $message)
                        <tr class="message-row">
                            <td class="ps-4 py-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3 d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary fw-bold"
                                         style="width: 38px; height: 38px; font-size: 0.8rem;">
                                        {{ strtoupper(substr($message->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0">{{ $message->name }}</div>
                                        <a href="mailto:{{ $message->email }}"
                                           class="text-muted x-small text-decoration-none">
                                            <i class="bi bi-envelope me-1"></i>{{ $message->email }}
                                        </a>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4">
                                <p class="mb-0 text-muted small"
                                   style="line-height: 1.6; display: -webkit-box;
                                   -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $message->message }}
                                </p>
                            </td>

                            <td class="py-4 text-center">
                                <div class="text-dark small fw-medium">
                                    {{ $message->created_at->format('M d, Y') }}
                                </div>
                                <div class="text-muted x-small">
                                    {{ $message->created_at->format('H:i') }}
                                </div>
                            </td>

                            {{-- ACTIONS (UPDATED like Orders) --}}
                            <td class="pe-4 py-4 text-end">
                                <div class="d-inline-flex gap-2">

                                    {{-- Reply --}}
                                    <a href="mailto:{{ $message->email }}?subject=RE: Contact Inquiry"
                                       class="btn btn-action btn-action-secondary"
                                       title="@lang('admin.Reply')" aria-label="@lang('admin.Reply')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16" fill="currentColor">
                                            <path d="M9.354 5.5H3.5a.5.5 0 0 0 0 1h5.854V9a.5.5 0 0 0 .854.354l4-4a.5.5 0 0 0 0-.708l-4-4A.5.5 0 0 0 9.354 1.5v4z"/>
                                        </svg>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.contact-messages.destroy', $message->id) }}"
                                          method="POST" class="m-0"
                                          onsubmit="return confirm('@lang('admin.Are you sure you want to delete this message?')')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-action btn-action-danger"
                                                title="@lang('admin.Delete Message')" aria-label="@lang('admin.Delete Message')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 16 16" fill="currentColor">
                                                <path d="M2.5 1a1 1 0 0 0-1 1v1h13V2a1 1 0 0 0-1-1h-11zm1 4v8a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1V5h-9z" />
                                            </svg>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <i class="bi bi-chat-left-dots opacity-25 mb-3" style="font-size: 3rem;"></i>
                                <h6 class="text-muted fw-light">@lang('admin.Your inbox is empty')</h6>
                                <p class="text-muted x-small">
                                    @lang('admin.When customers contact you, messages will appear here.')
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
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

    .btn-action:focus {
        box-shadow: 0 0 0 .2rem rgba(46, 49, 146, .15);
    }
</style>
@endsection
