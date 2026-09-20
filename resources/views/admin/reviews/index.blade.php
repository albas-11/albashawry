@extends('layouts.admin')

@section('title', 'Reviews Management')

@section('content')
    <div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">

        <div class="row align-items-end mb-5">
            <div class="col">
                <h1 class="display-6 fw-bold text-dark mb-0">@lang('admin.Reviews') </h1>
                <p class="text-muted mt-2 mb-0">@lang('admin.Manage and moderate customer feedback')</p>
            </div>
            <div class="col-auto">
                <div class="bg-white border rounded-pill px-4 py-2 shadow-sm d-flex align-items-center">
                    <span class="text-muted small fw-bold text-uppercase me-2">@lang('admin.Total') :</span>
                    <span class="fw-bold text-primary">{{ $reviews->total() }}</span>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr class="bg-light bg-opacity-50">
                                <th class="ps-4 py-4 border-0 text-uppercase small fw-bold text-muted" style="width: 15%">
                                    @lang('admin.Client')</th>
                                <th class="py-4 border-0 text-uppercase small fw-bold text-muted" style="width: 20%">
                                    @lang('admin.Product')
                                </th>
                                <th class="py-4 border-0 text-uppercase small fw-bold text-muted" style="width: 15%">
                                    @lang('admin.Rating')
                                </th>
                                <th class="py-4 border-0 text-uppercase small fw-bold text-muted">@lang('admin.Reviews')</th>
                                <th class="py-4 border-0 text-uppercase small fw-bold text-muted text-end pe-4">
                                    @lang('admin.Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reviews as $review)
                                <tr class="hover-bg-light transition-all">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">

                                            <span
                                                class="fw-bold text-dark">{{ $review->user->name ?? __('admin.guest') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark small fw-medium text-truncate" style="max-width: 180px;">
                                            {{ $review->reviewable->name ?? '--' }}
                                        </div>
                                        <span class="text-muted x-small">@lang('admin.ID'):
                                            #{{ $review->reviewable_id ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="rating-stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i
                                                    class="bi bi-star-fill {{ $i <= $review->rating ? 'text-warning' : 'text-light-gray' }}"></i>
                                            @endfor
                                            <span class="ms-2 x-small fw-bold text-muted">({{ $review->rating }}/5)</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="review-comment p-2 rounded-3">
                                            @if ($review->title)
                                                <div class="fw-bold text-dark small mb-1">{{ $review->title }}</div>
                                            @endif
                                            <div class="text-muted small text-wrap lh-sm" style="max-width: 350px;">
                                                {{ \Illuminate\Support\Str::limit($review->comment, 120) }}
                                            </div>
                                            <div class="x-small text-muted mt-2 border-top pt-1 italic">
                                                @lang('admin.Posted on'){{ $review->created_at->format('d M, Y') }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            @if (!$review->is_approved)
                                                <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-action btn-approve"
                                                        title="Approve">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('admin.reviews.show', $review) }}"
                                                class="btn btn-action btn-view" title="@lang('admin.View')">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
                                                class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-action btn-delete" title="Delete">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="py-5">
                                            <i class="bi bi-chat-left-dots display-1 text-light mb-3 d-block"></i>
                                            <p class="text-muted">@lang('admin.No customer reviews at the moment.')</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-5 d-flex justify-content-between align-items-center">
            <p class="x-small text-muted text-uppercase fw-bold tracking-wider mb-0">
                @lang('admin.Showing') {{ $reviews->firstItem() }} @lang('admin.to') {{ $reviews->lastItem() }}
                @lang('admin.of') {{ $reviews->total() }}
            </p>
            <div class="custom-pagination d-flex justify-content-end mt-4">
                <nav>
                    <ul class="pagination mb-0">
                        {{-- Previous --}}
                        <li class="page-item {{ $reviews->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $reviews->previousPageUrl() }}" aria-label="Previous">
                                &laquo;
                            </a>
                        </li>

                        {{-- Numbers --}}
                        @foreach ($reviews->getUrlRange(1, $reviews->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $reviews->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach

                        {{-- Next --}}
                        <li class="page-item {{ $reviews->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $reviews->nextPageUrl() }}" aria-label="Next">
                                &raquo;
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

        </div>

    </div>

    <style>
        /* TYPOGRAPHIE & BASE */
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
            transition: all 0.2s ease;
        }

        .text-light-gray {
            color: #e2e8f0;
        }

        /* AVATARS */
        .avatar-circle-sm {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 0.8rem;
        }

        /* RATING & COMMENT */
        .rating-stars {
            font-size: 0.85rem;
        }

        .review-comment {
            background-color: #fcfcfd;
            border: 1px solid #f1f5f9;
        }

        .hover-bg-light:hover {
            background-color: #f8fafc;
        }

        /* BADGES DE STATUS */
        .badge-status {
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            display: inline-block;
            letter-spacing: 0.5px;
        }

        .badge-status.approved {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #dcfce7;
        }

        .badge-status.pending {
            background: #fffbeb;
            color: #d97706;
            border: 1px solid #fef3c7;
        }

        /* BOUTONS D'ACTION */
        .btn-action {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e2e8f0;
            background: white;
            color: #64748b;
            transition: all 0.2s;
        }

        .btn-approve:hover {
            background: #16a34a;
            color: white;
            border-color: #16a34a;
        }

        .btn-delete:hover {
            background: #ef4444;
            color: white;
            border-color: #ef4444;
        }

        /* PAGINATION OVERRIDE (Si nécessaire selon votre layout) */
        .pagination {
            margin-bottom: 0;
            gap: 5px;
        }

        .page-link {
            border: none;
            border-radius: 8px !important;
            color: #64748b;
            font-weight: 600;
            padding: 0.5rem 1rem;
        }

        .page-item.active .page-link {
            background-color: #2E3192;
            color: white;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('form');
                Swal.fire({
                    title: 'Supprimer cet avis ?',
                    text: "Cette action est définitive.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-4',
                        confirmButton: 'rounded-pill px-4',
                        cancelButton: 'rounded-pill px-4'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
