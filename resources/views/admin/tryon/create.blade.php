@extends('layouts.admin')

@section('title', __('admin.Upload Try-On Asset'))

@section('content')
    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="fw-bold h4 mb-1 text-dark">@lang('admin.Upload Try-On Asset')</h1>
                <p class="text-muted small mb-0">@lang('admin.Prepare your AR assets with transparent PNGs')</p>
            </div>
            <a href="{{ route('admin.tryon.index') }}" class="btn btn-outline-secondary border-0 btn-sm px-3">
                <i class="bi bi-arrow-left me-1"></i> @lang('admin.Back to Assets')
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-9">

                {{-- Alert Messages --}}
                @if (session('success'))
                    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.tryon.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div style="height: 4px; background: #2E3192;"></div>
                        <div class="card-body p-4 p-md-5">

                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">@lang('admin.Asset Name') <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}" required
                                        class="form-control bg-light border-0 py-2 px-3" placeholder="@lang('admin.Example: Ray-Ban Aviator Classic (Gold Edition)')">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">@lang('admin.Try-On Image (PNG)') <span
                                            class="text-danger">*</span></label>
                                    <div
                                        class="upload-area p-4 rounded-4 border-2 border-dashed bg-light text-center position-relative">
                                        <input type="file" name="file" id="file-input" accept=".png" required
                                            class="position-absolute w-100 h-100 top-0 start-0 opacity-0 cursor-pointer"
                                            onchange="previewImage(event, 'file-preview')">
                                        <div id="file-instruction">
                                            <i class="bi bi-cloud-arrow-up display-6 text-primary opacity-50"></i>
                                            <p class="small text-muted mb-0 mt-2">@lang('admin.Click or drag transparent PNG')</p>
                                        </div>

                                        <div id="file-preview" class="mt-2 d-none position-relative z-index-1">
                                            <div class="preview-checkerboard rounded-3 overflow-hidden shadow-sm mb-2"
                                                style="height: 120px;">
                                                <img id="preview-img" class="img-fluid h-100 object-fit-contain">
                                            </div>
                                            <button type="button" onclick="removePreview('file-preview', 'file-input')"
                                                class="btn btn-sm btn-danger rounded-pill px-3">
                                                <i class="bi bi-x-circle me-1"></i> @lang('admin.Remove')
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-text x-small"><i class="bi bi-info-circle me-1"></i> @lang('admin.Must have a transparent background (Max 5MB).')
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">@lang('admin.Thumbnail Image (Optional)')</label>
                                    <div
                                        class="upload-area p-4 rounded-4 border-2 border-dashed bg-light text-center position-relative">
                                        <input type="file" name="preview_image" id="preview-input"
                                            accept=".jpg,.jpeg,.png"
                                            class="position-absolute w-100 h-100 top-0 start-0 opacity-0 cursor-pointer"
                                            onchange="previewImage(event, 'preview-preview')">
                                        <div id="preview-instruction">
                                            <i class="bi bi-image text-secondary opacity-50 fs-1"></i>
                                            <p class="small text-muted mb-0 mt-2">@lang('admin.Upload a catalog thumbnail')</p>
                                        </div>

                                        <div id="preview-preview" class="mt-2 d-none position-relative z-index-1">
                                            <div class="rounded-3 overflow-hidden shadow-sm mb-2 border bg-white"
                                                style="height: 120px;">
                                                <img id="preview-img-2" class="img-fluid h-100 object-fit-contain w-100">
                                            </div>
                                            <button type="button"
                                                onclick="removePreview('preview-preview', 'preview-input')"
                                                class="btn btn-sm btn-danger rounded-pill px-3">
                                                <i class="bi bi-x-circle me-1"></i> @lang('admin.Remove')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 p-4 rounded-4 border border-light shadow-inner bg-white">
                                <h6 class="fw-bold mb-1 text-dark">@lang('admin.Initial Calibration')</h6>
                                <p class="text-muted small mb-4">@lang('admin.Set default positioning values (can be tuned later)')</p>

                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="p-2 border rounded-3 bg-light bg-opacity-50">
                                            <label
                                                class="form-label x-small fw-bold text-uppercase text-muted mb-1">@lang('admin.X Offset')</label>
                                            <input type="number" name="offset_x" value="{{ old('offset_x', 0) }}"
                                                step="0.01" class="form-control border-0 bg-transparent py-0 fw-bold">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-2 border rounded-3 bg-light bg-opacity-50">
                                            <label
                                                class="form-label x-small fw-bold text-uppercase text-muted mb-1">@lang('admin.Y Offset')</label>
                                            <input type="number" name="offset_y" value="{{ old('offset_y', 0) }}"
                                                step="0.01" class="form-control border-0 bg-transparent py-0 fw-bold">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-2 border rounded-3 bg-light bg-opacity-50">
                                            <label
                                                class="form-label x-small fw-bold text-uppercase text-muted mb-1">@lang('admin.Scale')</label>
                                            <input type="number" name="scale" value="{{ old('scale', 1) }}"
                                                min="0.1" max="5" step="0.1"
                                                class="form-control border-0 bg-transparent py-0 fw-bold">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-2 border rounded-3 bg-light bg-opacity-50">
                                            <label
                                                class="form-label x-small fw-bold text-uppercase text-muted mb-1">@lang('admin.Rotation (°)')</label>
                                            <input type="number" name="rotation" value="{{ old('rotation', 0) }}"
                                                min="-180" max="180" step="1"
                                                class="form-control border-0 bg-transparent py-0 fw-bold">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-5">
                                <a href="{{ route('admin.tryon.index') }}"
                                    class="btn btn-light px-4 border-0 py-2 fw-bold text-muted rounded-3">
                                    @lang('admin.Cancel')
                                </a>
                                <button type="submit" class="btn px-5 shadow-sm fw-bold text-white rounded-3"
                                    style="background-color: #2E3192;">
                                    <i class="bi bi-cloud-check me-2"></i> @lang('admin.Create Asset')
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .cursor-pointer {
            cursor: pointer;
        }

        .x-small {
            font-size: 0.72rem;
        }

        .shadow-inner {
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.03);
        }

        .upload-area {
            transition: all 0.2s ease;
            border-color: #dee2e6;
        }

        .upload-area:hover {
            background-color: #fff !important;
            border-color: #2E3192;
        }

        /* Transparency Background Effect */
        .preview-checkerboard {
            background-color: #eee;
            background-image: linear-gradient(45deg, #ddd 25%, transparent 25%),
                linear-gradient(-45deg, #ddd 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, #ddd 75%),
                linear-gradient(-45deg, transparent 75%, #ddd 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-control:focus {
            box-shadow: none;
            background-color: #fff !important;
        }
    </style>

    <script>
        function previewImage(event, previewContainerId) {
            const file = event.target.files[0];
            const instructionId = previewContainerId.replace('preview', 'instruction');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const container = document.getElementById(previewContainerId);
                    const instruction = document.getElementById(instructionId);
                    const img = container.querySelector('img');

                    img.src = e.target.result;
                    container.classList.remove('d-none');
                    instruction.classList.add('d-none');
                }
                reader.readAsDataURL(file);
            }
        }

        function removePreview(previewContainerId, inputId) {
            const container = document.getElementById(previewContainerId);
            const instructionId = previewContainerId.replace('preview', 'instruction');
            const instruction = document.getElementById(instructionId);
            const input = document.getElementById(inputId);

            container.classList.add('d-none');
            instruction.classList.remove('d-none');
            input.value = '';
        }
    </script>
@endsection
