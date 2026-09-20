<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pixel;
use Illuminate\Http\Request;

class PixelController extends Controller
{
    public function index(Request $request)
    {
        $query = Pixel::query()->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('pixel_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('provider')) {
            $query->where('provider', $request->provider);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', 1);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', 0);
            }
        }

        $pixels = $query->paginate(1)->withQueryString();

        return view('admin.pixels.index', compact('pixels'));
    }


    public function create()
    {
        return view('admin.pixels.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate(
            [
                'provider'  => 'required|in:facebook,tiktok,google',
                'pixel_id'  => 'required|string',
                'name'      => 'nullable|string',
                'events'    => 'nullable|array',
                'is_active' => 'nullable|boolean',
            ],
            [
                'provider.required' => __('admin.validation.provider_required'),
                'provider.in'       => __('admin.validation.provider_invalid'),

                'pixel_id.required' => __('admin.validation.pixel_id_required'),
                'pixel_id.string'   => __('admin.validation.pixel_id_string'),

                'name.string'       => __('admin.validation.name_string'),

                'events.array'      => __('admin.validation.events_array'),

                'is_active.boolean' => __('admin.validation.is_active_boolean'),
            ],
            [
                'provider'  => __('admin.Provider'),
                'pixel_id'  => __('admin.Pixel ID'),
                'name'      => __('admin.Name (optional)'),
                'events'    => __('admin.Events'),
                'is_active' => __('admin.Status'),
            ]
        );


        Pixel::create($data);
        return redirect()->route('admin.pixels.index');
    }

    public function edit(Pixel $pixel)
    {
        return view('admin.pixels.edit', compact('pixel'));
    }

    public function update(Request $r, Pixel $pixel)
    {
        $data = $r->validate(
            [
                'provider'  => 'required|in:facebook,tiktok,google,snapchat',
                'pixel_id'  => 'required|string',
                'name'      => 'nullable|string',
                'events'    => 'nullable|array',
                'is_active' => 'nullable|boolean',
            ],
            [
                'provider.required' => __('admin.validation.provider_required'),
                'provider.in'       => __('admin.validation.provider_invalid'),

                'pixel_id.required' => __('admin.validation.pixel_id_required'),
                'pixel_id.string'   => __('admin.validation.pixel_id_string'),

                'name.string'       => __('admin.validation.name_string'),

                'events.array'      => __('admin.validation.events_array'),

                'is_active.boolean' => __('admin.validation.is_active_boolean'),
            ],
            [
                'provider'  => __('admin.Provider'),
                'pixel_id'  => __('admin.Pixel ID'),
                'name'      => __('admin.Name (optional)'),
                'events'    => __('admin.Events'),
                'is_active' => __('admin.Status'),
            ]
        );


        $pixel->update($data);
        return redirect()->route('admin.pixels.index');
    }

    public function destroy(Pixel $pixel)
    {
        $pixel->delete();
        return back();
    }
}
