@extends('layouts.admin')

@section('title', __('admin.Virtual Try-On Files'))

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
        <div>
            <h1 class="fw-bold h4 mb-1 text-dark">@lang('admin.Virtual Try-On Assets')</h1>
            <p class="text-muted small mb-0">@lang('admin.Manage 3D overlays and AR calibration files for products')</p>
        </div>

        <a href="{{ route('admin.tryon.create') }}" class="btn btn-dark px-4 shadow-sm d-flex align-items-center gap-2"
           style="border-radius: 8px; background: #2E3192; border: none;">
            <i class="bi bi-cloud-arrow-up-fill"></i>
            <span>@lang('admin.Upload New Asset')</span>
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-muted small fw-bold text-uppercase" style="width: 150px;">@lang('admin.Preview')</th>
                        <th class="py-3 text-muted small fw-bold text-uppercase">@lang('admin.Asset Details')</th>
                        <th class="py-3 text-muted small fw-bold text-uppercase text-center">@lang('admin.Linked Products')</th>
                        <th class="py-3 text-muted small fw-bold text-uppercase text-center">@lang('admin.Status')</th>
                        <th class="pe-4 py-3 text-muted small fw-bold text-uppercase text-end">@lang('admin.Management')</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($tryonFiles as $file)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="position-relative overflow-hidden rounded-3 shadow-sm border" style="width: 100px; height: 75px; background: #f8f9fa;">
                                    <img src="{{ $file->getFileUrl() }}" alt="Preview"
                                         class="w-100 h-100 object-fit-cover shadow-inner">
                                    <div class="position-absolute bottom-0 end-0 bg-dark bg-opacity-50 text-white p-1" style="font-size: 0.6rem;">
                                        <i class="bi bi-eye"></i>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="fw-bold text-dark">{{ $file->name }}</div>
                                <div class="text-muted x-small">@lang('admin.Added'): {{ $file->created_at->format('M d, Y') }}</div>
                            </td>

                            <td class="text-center">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-pill bg-info bg-opacity-10 text-info border border-info border-opacity-10 px-3 py-1">
                                    <i class="bi bi-tag-fill me-2 x-small"></i>
                                    <span class="fw-bold small">{{ $file->products->count() }}</span>
                                </div>
                            </td>

                            <td class="text-center">
                                @if($file->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-medium border border-success border-opacity-10">
                                        <i class="bi bi-check-circle-fill me-1"></i> @lang('admin.Active')
                                    </span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2 fw-medium border border-danger border-opacity-10">
                                        <i class="bi bi-slash-circle me-1"></i> @lang('admin.Inactive')
                                    </span>
                                @endif
                            </td>

                            {{-- ACTIONS (UPDATED like Orders) --}}
                            <td class="pe-4 py-3 text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('admin.tryon.calibrate', $file) }}"
                                       class="btn btn-action btn-action-primary"
                                       title="@lang('admin.Calibrate')" aria-label="@lang('admin.Calibrate')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             viewBox="0 0 16 16" fill="currentColor">
                                            <path d="M8 1a.5.5 0 0 1 .5.5V2h-1v-.5A.5.5 0 0 1 8 1z"/>
                                            <path d="M3 5.5A.5.5 0 0 1 3.5 5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 5.5z"/>
                                            <path d="M6 8.5A.5.5 0 0 1 6.5 8h6a.5.5 0 0 1 0 1h-6A.5.5 0 0 1 6 8.5z"/>
                                            <path d="M3 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5z"/>
                                            <path d="M4.5 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm0 1a.5.5 0 1 1 0 1 .5.5 0 0 1 0-1z"/>
                                            <path d="M7.5 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm0 1a.5.5 0 1 1 0 1 .5.5 0 0 1 0-1z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="mb-3 opacity-25">
                                    <i class="bi bi-camera-reels" style="font-size: 3.5rem;"></i>
                                </div>
                                <h5 class="text-muted fw-light">@lang('admin.No Virtual Try-On files found')</h5>
                                <p class="text-muted small">@lang('admin.Upload your first AR asset to get started.')</p>
                                <a href="{{ route('admin.tryon.create') }}" class="btn btn-outline-primary btn-sm mt-2 px-4 rounded-pill">
                                    @lang('admin.Upload Now')
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if (method_exists($tryonFiles, 'links') && $tryonFiles->hasPages())
    <div class="mt-2">
        {{ $tryonFiles->links('pagination::bootstrap-5') }}
    </div>
@endif

</div>

<style>
    :root { --primary-custom: #2E3192; }

    .object-fit-cover { object-fit: cover; }
    .x-small { font-size: 0.72rem; }
    .table thead th { border-bottom: none; }

    .btn-white { background: #fff; }
    .btn-white:hover { background: #f8f9fa; border-color: #2E3192 !important; }

    .shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); }
    .badge { font-size: 0.7rem; letter-spacing: 0.02em; }

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

    /* Primary (blue accent) */
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
</style>
@endsection
