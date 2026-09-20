@if ($paginator->hasPages())
    <nav class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between gap-3">

        {{-- Mobile: Prev / Next --}}
        <div class="d-flex justify-content-between d-sm-none">
            <ul class="pagination mb-0">
                {{-- Previous --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item  mx-1 disabled" aria-disabled="true">
                        <span class="page-link">@lang('pagination.previous')</span>
                    </li>
                @else
                    <li class="page-item  mx-1">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                            @lang('pagination.previous')
                        </a>
                    </li>
                @endif

                {{-- Next --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item  mx-1">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                            @lang('pagination.next')
                        </a>
                    </li>
                @else
                    <li class="page-item  mx-1 disabled" aria-disabled="true">
                        <span class="page-link">@lang('pagination.next')</span>
                    </li>
                @endif
            </ul>
        </div>

        {{-- Desktop: Showing + numbered pages --}}
        <div class="d-none d-sm-flex flex-sm-fill align-items-sm-center justify-content-sm-between w-100">

            {{-- Showing text --}}
            <div class="small text-muted mb-0">
                @lang('message.showing')
                @if ($paginator->firstItem())
                    <span class="fw-semibold">{{ $paginator->firstItem() }}</span>
                    @lang('message.to')
                    <span class="fw-semibold">{{ $paginator->lastItem() }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
                @lang('message.of')
                <span class="fw-semibold">{{ $paginator->total() }}</span>
                @lang('message.results')
            </div>

            {{-- Pagination --}}
            <div>
                <ul class="pagination mb-0">

                    {{-- Previous --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item  mx-1 disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                            <span class="page-link" aria-hidden="true">
                                <svg class="pagination-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path d="M15 18l-6-6 6-6" fill="none" stroke="currentColor" stroke-width="2"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </li>
                    @else
                        <li class="page-item  mx-1">
                            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev"
                               aria-label="@lang('pagination.previous')">
                                <svg class="pagination-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path d="M15 18l-6-6 6-6" fill="none" stroke="currentColor" stroke-width="2"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </li>
                    @endif

                    {{-- Elements --}}
                    @foreach ($elements as $element)
                        {{-- Dots --}}
                        @if (is_string($element))
                            <li class="page-item  mx-1 disabled" aria-disabled="true">
                                <span class="page-link">{{ $element }}</span>
                            </li>
                        @endif

                        {{-- Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="page-item  mx-1 active" aria-current="page">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item  mx-1">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item  mx-1">
                            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next"
                               aria-label="@lang('pagination.next')">
                                <svg class="pagination-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path d="M9 18l6-6-6-6" fill="none" stroke="currentColor" stroke-width="2"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </li>
                    @else
                        <li class="page-item  mx-1 disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                            <span class="page-link" aria-hidden="true">
                                <svg class="pagination-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path d="M9 18l6-6-6-6" fill="none" stroke="currentColor" stroke-width="2"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </li>
                    @endif

                </ul>
            </div>
        </div>
    </nav>
@endif

<style>
    /* Keep arrows normal size (fix huge SVG issue) */
    .pagination-icon {
        width: 16px;
        height: 16px;
        display: block;
    }

    .pagination .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        padding: 0 12px;
        border-radius: 12px !important;
        border-color: #2E3192;
        color: #2E3192;
        font-size: .9rem;
    }

    /* Active */
    .pagination .page-item.active .page-link {
        background-color: #2E3192;
        border-color: #2E3192;
        color: #fff;
    }

    /* Hover */
    .pagination .page-item:not(.disabled) .page-link:hover {
        background-color: #2E3192;
        border-color: #2E3192;
        color: #fff;
    }

    /* Disabled */
    .pagination .page-item.disabled .page-link {
        color: #aaa;
        border-color: #ddd;
        background-color: #f8f9fa;
    }
</style>
