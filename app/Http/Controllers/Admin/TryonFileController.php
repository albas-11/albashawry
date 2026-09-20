<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TryonFile;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class TryonFileController extends Controller
{
    public function index()
    {
        $tryonFiles = TryonFile::with('products')
            ->latest()
            ->paginate(10);

        return view('admin.tryon.index', compact('tryonFiles'));
    }

    public function create()
    {
        return view('admin.tryon.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|image|mimes:png|max:5120',
            'preview_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'offset_x' => 'nullable|numeric',
            'offset_y' => 'nullable|numeric',
            'scale' => 'nullable|numeric|min:0.1|max:5',
            'rotation' => 'nullable|numeric|min:-180|max:180',
        ]);

        // ===============================
        // MAIN TRY-ON FILE
        // ===============================
        $originalName = pathinfo(
            $request->file('file')->getClientOriginalName(),
            PATHINFO_FILENAME
        );

        $extension = $request->file('file')->getClientOriginalExtension();
        $cleanName = Str::slug($originalName);
        $fileName = $cleanName . '.' . $extension;

        $destinationPath = public_path('images/tryon-files');

        // créer le dossier si inexistant
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        // MOVE vers public/images/tryon-files
        $request->file('file')->move($destinationPath, $fileName);

        // ===============================
        // PREVIEW IMAGE
        // ===============================
        $previewName = null;

        if ($request->hasFile('preview_image')) {
            $previewOriginal = pathinfo(
                $request->file('preview_image')->getClientOriginalName(),
                PATHINFO_FILENAME
            );

            $previewExt = $request->file('preview_image')->getClientOriginalExtension();
            $previewName = Str::slug($previewOriginal) . '.' . $previewExt;

            $previewPath = public_path('images/tryon-previews');

            if (!File::exists($previewPath)) {
                File::makeDirectory($previewPath, 0755, true);
            }

            $request->file('preview_image')->move($previewPath, $previewName);
        }

        TryonFile::create([
            'name' => $validated['name'],
            'file_path' => $fileName,
            'preview_image' => $previewName,
            'offset_x' => $validated['offset_x'] ?? 0,
            'offset_y' => $validated['offset_y'] ?? 0,
            'scale' => $validated['scale'] ?? 1,
            'rotation' => $validated['rotation'] ?? 0,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.tryon.index')
            ->with('success', __('Try-on file uploaded successfully'));
    }



    public function edit(TryonFile $tryonFile)
    {
        return view('admin.tryon.edit', compact('tryonFile'));
    }

    public function update(Request $request, TryonFile $tryonFile)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'nullable|image|mimes:png|max:5120',
            'preview_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'offset_x' => 'nullable|numeric',
            'offset_y' => 'nullable|numeric',
            'scale' => 'nullable|numeric|min:0.1|max:5',
            'rotation' => 'nullable|numeric|min:-180|max:180',
            'is_active' => 'boolean',
        ]);

        // Update main file if provided
        if ($request->hasFile('file')) {
            // Delete old file
            Storage::disk('public')->delete($tryonFile->file_path);

            // Upload new file
            $validated['file_path'] = $request->file('file')->store('tryon-files', 'public');
        }

        // Update preview image if provided
        if ($request->hasFile('preview_image')) {
            // Delete old preview
            if ($tryonFile->preview_image) {
                Storage::disk('public')->delete($tryonFile->preview_image);
            }

            // Upload new preview
            $validated['preview_image'] = $request->file('preview_image')->store('tryon-previews', 'public');
        }

        $tryonFile->update($validated);

        return redirect()
            ->route('admin.tryon.index')
            ->with('success', __('Try-on file updated successfully'));
    }

    public function destroy(TryonFile $tryonFile)
    {
        // Check if used by any products
        if ($tryonFile->products()->count() > 0) {
            return back()->with('error', __('Cannot delete: This file is used by products'));
        }

        // Delete files
        Storage::disk('public')->delete($tryonFile->file_path);
        if ($tryonFile->preview_image) {
            Storage::disk('public')->delete($tryonFile->preview_image);
        }

        $tryonFile->delete();

        return redirect()
            ->route('admin.tryon.index')
            ->with('success', __('Try-on file deleted successfully'));
    }

    public function calibrate(TryonFile $tryonFile)
    {
        return view('admin.tryon.calibrate', compact('tryonFile'));
    }
    public function updateCalibration(Request $request, TryonFile $tryonFile)
    {
        $tryonFile->update([
            'offset_x' => $request->offset_x,
            'offset_y' => $request->offset_y,
            'scale'    => $request->scale,
            'rotation' => $request->rotation,
        ]);

        return redirect()->back()->with('success', __('Calibration saved successfully'));
    }


    public function saveCalibration(Request $request, TryonFile $tryonFile)
    {
        $validated = $request->validate([
            'offset_x' => 'required|numeric',
            'offset_y' => 'required|numeric',
            'scale' => 'required|numeric|min:0.1|max:5',
            'rotation' => 'required|numeric|min:-180|max:180',
            'calibration_data' => 'nullable|json',
        ]);

        $tryonFile->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('Calibration saved successfully')
        ]);
    }
}
