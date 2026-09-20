<?php

namespace App\Http\Controllers;

use App\Services\WishlistService;
use Illuminate\Http\Request;
use App\Models\Wishlist;


class WishlistController extends Controller
{
    protected $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    // Afficher la wishlist
    public function index()
    {
        $user = auth()->user();

        $wishlists = Wishlist::where('user_id', $user->id)
            ->with('item') // charge automatiquement product / contactLens / accessory
            ->get();

        return view('wishlist.index', compact('wishlists'));
    }

    // Ajouter ou retirer un item de la wishlist
    public function toggle(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'item_type' => 'required|string|in:product,contact_lens,accessory',
        ]);

        $user = auth()->user();

        // Vérifier si l'item existe déjà dans la wishlist
        $wishlist = Wishlist::where([
            'user_id' => $user->id,
            'item_id' => $request->item_id,
            'item_type' => $request->item_type,
        ])->first();

        if ($wishlist) {
            // Si existe -> supprimer
            $wishlist->delete();
            return response()->json(['message' => 'Removed from wishlist', 'success' => true]);
        }

        // Ajouter à la wishlist
        Wishlist::create([
            'user_id' => $user->id,
            'item_id' => $request->item_id,
            'item_type' => $request->item_type,
        ]);

        return response()->json(['message' => 'Added to wishlist', 'success' => true]);
    }
    public function clear(Request $request)
{
    auth()->user()->wishlists()->delete();

    return response()->json([
        'success' => true
    ]);
}

}
