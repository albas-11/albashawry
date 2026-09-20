<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $storesForMap = Store::orderBy('id')->get()->map(function ($store) {
            return [
                'id' => $store->id,
                'name' => $store->name,
                'address' => $store->address,
                'city' => $store->city,
                'country' => $store->country,
                'phone' => $store->phone,
                'latitude' => (float) $store->latitude,
                'longitude' => (float) $store->longitude,
            ];
        });

        // Construire la requête principale
        $query = Store::orderBy('id');

        // Filtrer par ville si fourni
        if ($request->has('city') && $request->city) {
            $query->where('city', $request->city);
        }

        // Filtrer par recherche si fourni
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Récupérer les boutiques
        $stores = $query->get();

        // Récupérer les villes disponibles pour le filtre
        $cities = Store::distinct()
            ->pluck('city')
            ->sort();

        // Calculer la distance si latitude et longitude fournies
        if ($request->has('lat') && $request->has('lng')) {
            $stores = $stores->map(function ($store) use ($request) {
                $store->distance = $store->getDistanceFrom($request->lat, $request->lng);
                return $store;
            })->sortBy('distance');
        }

        return view('stores.index', compact('stores', 'cities', 'storesForMap'));
    }

    public function show($id)
    {
        $store = Store::findOrFail($id);

        return response()->json([
            'success' => true,
            'store' => [
                'id' => $store->id,
                'name' => $store->name,
                'address' => $store->address,
                'city' => $store->city,
                'country' => $store->country,
                'phone' => $store->phone,
                'latitude' => (float) $store->latitude,
                'longitude' => (float) $store->longitude,
            ]
        ]);
    }

    // API endpoint pour récupérer les boutiques en JSON
public function getStoresJson(Request $request)
{
    $locale = app()->getLocale();

    $stores = Store::orderBy('id')->get()->map(function ($store) use ($locale) {
        return [
            'id'      => $store->id,
            'name'    => $store->getTranslation('name', $locale) ?: $store->getTranslation('name', 'en'),
            'address' => $store->getTranslation('address', $locale) ?: $store->getTranslation('address', 'en'),
            'city'    => $store->city,
            'country' => $store->country,
            'phone'   => $store->phone,
            'lat'     => $store->latitude !== null ? (float) $store->latitude : null,
            'lng'     => $store->longitude !== null ? (float) $store->longitude : null,
            'schedule'=> $store->schedule,
            'is_open_now' => $store->isOpenNow(),
            'today_hours' => $store->getTodayHours(),
        ];
    });

    return response()->json($stores);
}

}
