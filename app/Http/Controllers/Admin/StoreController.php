<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $query = Store::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('city', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name,'$.en')) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name,'$.ar')) LIKE ?", ["%{$search}%"]);
            });
        }

        $stores = $query->latest()->paginate(10)->withQueryString();
        return view('admin.stores.index', compact('stores'));
    }

    public function create()
    {
        return view('admin.stores.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_en'       => 'required|string',
            'name_ar'       => 'required|string',
            'address_en'    => 'required|string',
            'address_ar'    => 'required|string',
            'city'          => 'required|string',
            'country'       => 'required|string',
            'phone'         => 'nullable|string',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
            'schedule_json' => 'nullable|string',
        ]);

        $schedule = $this->decodeSchedule($data['schedule_json'] ?? null);
        if ($schedule === false) {
            return back()->withInput()->withErrors(['schedule_json' => 'Schedule JSON is not valid']);
        }

        Store::create([
            'name' => ['en' => $data['name_en'], 'ar' => $data['name_ar']],
            'address' => ['en' => $data['address_en'], 'ar' => $data['address_ar']],
            'city'      => $data['city'],
            'country'   => $data['country'],
            'phone'     => $data['phone'] ?? null,
            'latitude'  => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'schedule'  => $schedule ?: null,
        ]);

        return redirect()->route('admin.stores.index')->with('success', 'Store created successfully');
    }

    public function edit(Store $store)
    {
        $initialSchedule = $store->schedule;

        $initialSchedule = $this->normalizeSchedule($initialSchedule);

        return view('admin.stores.edit', compact('store', 'initialSchedule'));
    }

    public function update(Request $request, Store $store)
    {
        // dd($request->all());
        $data = $request->validate([
            'name_en'       => 'required|string',
            'name_ar'       => 'required|string',
            'address_en'    => 'required|string',
            'address_ar'    => 'required|string',
            'city'          => 'required|string',
            'country'       => 'required|string',
            'phone'         => 'nullable|string',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
            'schedule_json' => 'nullable|string',
        ]);
        // dd($data);
        $schedule = $this->decodeSchedule($data['schedule_json'] ?? null);
        if ($schedule === false) {
            return back()->withInput()->withErrors(['schedule_json' => 'Schedule JSON is not valid']);
        }

        $store->update([
            'name' => ['en' => $data['name_en'], 'ar' => $data['name_ar']],
            'address' => ['en' => $data['address_en'], 'ar' => $data['address_ar']],
            'city'      => $data['city'],
            'country'   => $data['country'],
            'phone'     => $data['phone'] ?? null,
            'latitude'  => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'schedule'  => $schedule ?: null,
        ]);

        return redirect()->route('admin.stores.index')->with('success', 'Store updated successfully');
    }

    public function destroy(Store $store)
    {
        $store->delete();
        return redirect()->route('admin.stores.index')->with('success', 'Store deleted successfully');
    }

    private function decodeSchedule(?string $scheduleJson)
    {
        if (!$scheduleJson) return null;

        $decoded = json_decode($scheduleJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) return false;

        return $decoded;
    }

    private function normalizeSchedule($schedule)
    {
        if (is_array($schedule)) return $schedule;

        if (is_string($schedule)) {
            $a = json_decode($schedule, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($a)) return $a;

            //double encoded
            if (is_string($a)) {
                $b = json_decode($a, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($b)) return $b;
            }
        }

        return null;
    }
}
