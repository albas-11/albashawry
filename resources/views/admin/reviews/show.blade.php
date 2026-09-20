@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="font-weight-bold text-dark mb-0">
                    @lang('admin.Review Details') <span class="text-muted">#{{ $review->id }}</span>
                </h3>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary shadow-sm">
                    <i class="fas fa-chevron-left mr-1"></i> @lang('admin.Back to List')
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row">
                        {{-- Customer Information --}}
                        <div class="col-md-6 border-right">
                            <h6 class="text-uppercase text-muted font-weight-bold mb-3">@lang('admin.Customer Information')</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between px-0 border-0">
                                    <span class="text-secondary">@lang('admin.Customer')</span>
                                    <span class="font-weight-bold">{{ $review->user->name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0 border-0">
                                    <span class="text-secondary">@lang('admin.Email')</span>
                                    <span>{{ $review->user->email }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0 border-0">
                                    <span class="text-secondary">@lang('admin.Rating')</span>
                                    <span>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-light' }}"></i>
                                        @endfor
                                        <small class="ml-1 text-muted">({{ $review->rating }}/5)</small>
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0 border-0">
                                    <span class="text-secondary">@lang('admin.Date')</span>
                                    <span>{{ $review->created_at->format('M d, Y - H:i') }}</span>
                                </li>
                            </ul>
                        </div>

                        {{-- Product & Status --}}
                        <div class="col-md-6 pl-md-4">
                            <h6 class="text-uppercase text-muted font-weight-bold mb-3">@lang('admin.Product & Status')</h6>
                            <ul class="list-group list-group-flush">
                                @if ($review->order)
                                    <li class="list-group-item d-flex justify-content-between px-0 border-0">
                                        <span class="text-secondary">@lang('admin.Order')</span>
                                        <a href="{{ route('admin.orders.show', $review->order->id) }}" class="font-weight-bold">
                                            #{{ $review->order->order_number }}
                                        </a>
                                    </li>
                                @endif
                                <li class="list-group-item d-flex justify-content-between px-0 border-0">
                                    <span class="text-secondary">@lang('admin.Product')</span>
                                    <span class="text-truncate">
                                        {{ $review->reviewable->name ?? 'N/A' }}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0 border-0">
                                    <span class="text-secondary">@lang('admin.Verified Purchase')</span>
                                    <span class="badge badge-pill {{ $review->is_verified ? 'badge-soft-success' : 'badge-soft-secondary' }}"
                                        style="{{ $review->is_verified ? 'background: #e8f5e9; color: #2e7d32;' : 'background: #f5f5f5; color: #757575;' }} padding: 5px 12px;">
                                        {{ $review->is_verified ? __('admin.Yes') : __('admin.No') }}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0 border-0">
                                    <span class="text-secondary">@lang('admin.Status')</span>
                                    @if ($review->is_approved)
                                        <span class="text-success small font-weight-bold">
                                            <i class="fas fa-check-circle"></i> @lang('admin.Approved')
                                        </span>
                                    @else
                                        <span class="text-warning small font-weight-bold">
                                            <i class="fas fa-clock"></i> @lang('admin.Pending Approval')
                                        </span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- Customer Feedback --}}
                    <div class="review-content bg-light p-4 rounded border-left border-primary shadow-sm"
                        style="border-left-width: 5px !important;">
                        <h6 class="text-muted small text-uppercase font-weight-bold mb-3" style="letter-spacing: 1px;">
                            <i class="fas fa-comment-dots mr-1"></i> @lang('admin.Customer Feedback')
                        </h6>

                        @if ($review->title)
                            <h5 class="text-dark font-weight-bold mb-2">
                                {{ $review->title }}
                            </h5>
                        @endif

                        <div class="mt-3">
                            <p class="mb-0 text-secondary"
                               style="font-style: italic; font-size: 1.1rem; line-height: 1.7; border-left: 2px solid #dee2e6; padding-left: 15px;">
                                "{{ $review->comment }}"
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        @if (!$review->is_approved)
                            <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" class="mr-2">
                                @csrf
                                <button type="submit" class="btn btn-success px-4 shadow-sm">
                                    <i class="fas fa-check mr-1"></i> @lang('admin.Approve Review')
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST"
                              onsubmit="return confirm('@lang('admin.Confirm Delete Review')')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger font-weight-bold">
                                <i class="fas fa-trash-alt mr-1"></i> @lang('admin.Delete Permanently')
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
