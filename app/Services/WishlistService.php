<?php

namespace App\Services;

use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistService
{
    /**
     * Récupérer la wishlist d'un utilisateur
     */
    public function getUserWishlist(int $userId)
    {
        return Wishlist::where('user_id', $userId)->get();
    }

    /**
     * Vérifier si un item est dans la wishlist
     */
    public function isInWishlist(int $userId, int $itemId, string $itemType): bool
    {
        return Wishlist::where('user_id', $userId)
            ->where('item_id', $itemId)
            ->where('item_type', $itemType)
            ->exists();
    }

    /**
     * Ajouter ou retirer un item de la wishlist
     */
    public function toggleItem(int $userId, int $itemId, string $itemType): array
    {
        $exists = $this->isInWishlist($userId, $itemId, $itemType);

        if ($exists) {
            Wishlist::where('user_id', $userId)
                ->where('item_id', $itemId)
                ->where('item_type', $itemType)
                ->delete();

            return [
                'action' => 'removed',
                'message' => 'Item removed from wishlist'
            ];
        }

        Wishlist::create([
            'user_id' => $userId,
            'item_id' => $itemId,
            'item_type' => $itemType,
        ]);

        return [
            'action' => 'added',
            'message' => 'Item added to wishlist'
        ];
    }

    /**
     * Vider la wishlist d'un utilisateur
     */
    public function clear(int $userId)
    {
        Wishlist::where('user_id', $userId)->delete();
    }
}
