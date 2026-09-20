@extends('layouts.admin')

@section('title', __('admin.Add Brand'))

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 fw-semibold mb-0">@lang('admin.Add Brand')</h1>
        <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> @lang('admin.Back')
        </a>
    </div>

    {{-- Alerts --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Name EN --}}
                <div class="mb-3">
                    <label class="form-label">@lang('admin.Name (EN)')</label>
                    <input type="text" name="name_en" class="form-control" value="{{ old('name_en') }}" required>
                </div>

                {{-- Name AR --}}
                <div class="mb-3">
                    <label class="form-label">@lang('admin.Name (AR)')</label>
                    <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}" required>
                </div>

                {{-- Logo --}}
                <div class="mb-3">
                    <label class="form-label">@lang('admin.Logo')</label>
                    <input type="file" name="logo" class="form-control">
                </div>

                {{-- Banner Image --}}
                <div class="mb-3">
                    <label class="form-label">@lang('admin.Banner Image')</label>
                    <input type="file" name="banner_image" class="form-control">
                </div>

                {{-- Status --}}
                <div class="form-check mb-3">
                    <input type="checkbox" name="is_active" class="form-check-input" value="1" checked>
                    <label class="form-check-label">@lang('admin.Active')</label>
                </div>

                <button type="submit" class="btn " style="background-color: #2E3192; color:aliceblue">
                    <i class="bi bi-plus-circle"></i> @lang('admin.Add Brand')
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
