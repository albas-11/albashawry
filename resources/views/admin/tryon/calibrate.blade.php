@extends('layouts.admin')

@section('title', __('admin.AR Calibration'))

@section('content')
<div class="container-fluid p-0 overflow-hidden" style="height: calc(100vh - 60px);">
    <div class="row g-0 h-100">

        {{-- LEFT : LIVE PREVIEW --}}
        <div class="col-lg-8 col-md-7 position-relative d-flex flex-column" style="background: #1a1a1a;">

            <div class="position-absolute top-0 start-0 end-0 p-4 z-index-2 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="text-white fw-bold mb-0">@lang('admin.Live Calibration Studio')</h5>
                </div>
                <a href="{{ route('admin.tryon.index') }}" class="btn btn-sm btn-outline-light border-opacity-25 px-3 rounded-pill">
                    <i class="bi bi-x-lg me-1"></i> @lang('admin.Exit Studio')
                </a>
            </div>
<br>
            <div class="flex-grow-1 d-flex justify-content-center align-items-center p-5">
                <div class="calibration-workspace position-relative shadow-lg rounded-4 overflow-hidden border border-secondary border-opacity-25 bg-white">

                    <img src="{{ asset('images/tryon-files/face-placeholder.png') }}"
                         class="img-fluid d-block"
                         style="width: 520px; height: 420px; object-fit: cover;">

                    <img id="tryonImage"
                         src="{{ asset('images/tryon-files/' . $tryonFile->file_path) }}"
                         class="position-absolute top-50 start-50"
                         style="
                            pointer-events: none;
                            transform: translate(-50%, -50%)
                                       translate({{ $tryonFile->offset_x }}px, {{ $tryonFile->offset_y }}px)
                                       scale({{ $tryonFile->scale }})
                                       rotate({{ $tryonFile->rotation }}deg);
                         ">

                    <div class="position-absolute top-50 start-0 end-0 border-top border-primary border-opacity-10 translate-middle-y"></div>
                    <div class="position-absolute start-50 top-0 bottom-0 border-start border-primary border-opacity-10 translate-middle-x"></div>
                </div>
            </div>

            <div class="p-3 text-center text-white-50 x-small">
                <i class="bi bi-info-circle me-1"></i>
                @lang('admin.Adjust sliders to align the frame with the reference eye line.')
            </div>
        </div>

        {{-- RIGHT : CONTROLS --}}
        <div class="col-lg-4 col-md-5 bg-white shadow-lg z-index-3 h-100 d-flex flex-column">
            <div class="p-4 flex-grow-1 overflow-auto">
                <form method="POST" action="{{ route('admin.tryon.updateCalibration', $tryonFile->id) }}" id="calibrationForm">
                    @csrf
                    @method('PUT')

                    <div class="d-flex flex-column gap-5">

                        <div class="control-group">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="small fw-bold text-uppercase text-muted">@lang('admin.Horizontal Axis (X)')</label>
                                <span class="badge bg-light text-dark font-monospace" id="offsetXVal">{{ $tryonFile->offset_x }}px</span>
                            </div>
                            <input type="range" class="form-range custom-slider" name="offset_x"
                                   min="-200" max="200" value="{{ $tryonFile->offset_x }}" oninput="updatePreview()">
                        </div>

                        <div class="control-group">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="small fw-bold text-uppercase text-muted">@lang('admin.Vertical Axis (Y)')</label>
                                <span class="badge bg-light text-dark font-monospace" id="offsetYVal">{{ $tryonFile->offset_y }}px</span>
                            </div>
                            <input type="range" class="form-range custom-slider" name="offset_y"
                                   min="-200" max="200" value="{{ $tryonFile->offset_y }}" oninput="updatePreview()">
                        </div>

                        <div class="control-group">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="small fw-bold text-uppercase text-muted">@lang('admin.Zoom / Scale')</label>
                                <span class="badge bg-light text-dark font-monospace" id="scaleVal">{{ $tryonFile->scale }}</span>
                            </div>
                            <input type="range" class="form-range custom-slider" name="scale"
                                   min="0.5" max="2" step="0.01" value="{{ $tryonFile->scale }}" oninput="updatePreview()">
                        </div>

                        <div class="control-group">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="small fw-bold text-uppercase text-muted">@lang('admin.Rotation (°)')</label>
                                <span class="badge bg-light text-dark font-monospace" id="rotationVal">{{ $tryonFile->rotation }}°</span>
                            </div>
                            <input type="range" class="form-range custom-slider" name="rotation"
                                   min="-45" max="45" step="0.5" value="{{ $tryonFile->rotation }}" oninput="updatePreview()">
                        </div>

                    </div>
                </form>
            </div>

            <div class="p-4 bg-light border-top">
                <div class="row g-2">
                    <div class="col-4">
                        <button type="button" class="btn btn-outline-secondary w-100 py-2 border-0"
                                onclick="resetCalibration()">
                            @lang('admin.Reset')
                        </button>
                    </div>
                    <div class="col-8">
                        <button type="submit" form="calibrationForm"
                                class="btn btn-primary w-100 py-2 shadow-sm fw-bold"
                                style="background: #2E3192;">
                            <i class="bi bi-cloud-check-fill me-2"></i>
                            @lang('admin.Apply Settings')
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@push('scripts')
<script>
    function updatePreview() {
        const img = document.getElementById('tryonImage');

        const offsetX = document.querySelector('input[name="offset_x"]').value;
        const offsetY = document.querySelector('input[name="offset_y"]').value;
        const scale   = document.querySelector('input[name="scale"]').value;
        const rotation = document.querySelector('input[name="rotation"]').value;

        img.style.transform = `
            translate(-50%, -50%)
            translate(${offsetX}px, ${offsetY}px)
            scale(${scale})
            rotate(${rotation}deg)
        `;

        document.getElementById('offsetXVal').innerText = offsetX + 'px';
        document.getElementById('offsetYVal').innerText = offsetY + 'px';
        document.getElementById('scaleVal').innerText   = scale;
        document.getElementById('rotationVal').innerText = rotation + '°';
    }

    function resetCalibration() {
        document.querySelector('input[name="offset_x"]').value = 0;
        document.querySelector('input[name="offset_y"]').value = 0;
        document.querySelector('input[name="scale"]').value = 1;
        document.querySelector('input[name="rotation"]').value = 0;

        updatePreview();
    }

    // Initial sync on page load
    document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endpush

@endsection
