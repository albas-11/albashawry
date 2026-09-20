<?php

namespace App\Services;

use App\Models\Review;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    public function createReview(array $data, int $userId)
    {
        // Vérifier si l'utilisateur a déjà fait un review pour ce produit
        $existingReview = Review::where('user_id', $userId)
            ->where('product_id', $data['product_id'])
            ->first();

        if ($existingReview) {
            throw new \Exception(__('You have already reviewed this product.'));
        }

        // Vérifier si l'utilisateur a acheté le produit (optionnel)
        $isVerified = false;
        if (isset($data['order_id'])) {
            $isVerified = $this->verifyPurchase($userId, $data['product_id'], $data['order_id']);
        }

        // Créer le review
        return DB::transaction(function () use ($data, $userId, $isVerified) {
            $review = Review::create([
                'user_id' => $userId,
                'product_id' => $data['product_id'],
                'order_id' => $data['order_id'] ?? null,
                'rating' => $data['rating'],
                'title' => $data['title'] ?? null,
                'comment' => $data['comment'],
                'is_verified' => $isVerified,
                'is_approved' => false, // En attente d'approbation
            ]);

            return $review;
        });
    }

    /**
     * Vérifier si l'utilisateur a acheté le produit
     */
    private function verifyPurchase(int $userId, int $productId, ?int $orderId): bool
    {
        if (!$orderId) {
            return false;
        }

        return Order::where('id', $orderId)
            ->where('user_id', $userId)
            ->whereHas('items', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->where('status', 'completed') // Ajustez selon votre logique
            ->exists();
    }

    /**
     * Approuver un review
     */
    public function approveReview(int $reviewId): bool
    {
        return DB::transaction(function () use ($reviewId) {
            $review = Review::findOrFail($reviewId);
            $review->update(['is_approved' => true]);

            // Mettre à jour la note moyenne du produit
            $this->updateProductRating($review->product_id);

            return true;
        });
    }

    /**
     * Rejeter un review
     */
    public function rejectReview(int $reviewId): bool
    {
        $review = Review::findOrFail($reviewId);
        $review->update(['is_approved' => false]);

        // Mettre à jour la note moyenne du produit
        $this->updateProductRating($review->product_id);

        return true;
    }

    /**
     * Mettre à jour la note moyenne du produit
     */
    private function updateProductRating(int $productId): void
    {
        $product = Product::findOrFail($productId);

        $approvedReviews = Review::where('product_id', $productId)
            ->where('is_approved', true)
            ->select(DB::raw('AVG(rating) as avg_rating, COUNT(*) as total'))
            ->first();

        $product->update([
            'average_rating' => round($approvedReviews->avg_rating ?? 0, 1),
            'reviews_count' => $approvedReviews->total ?? 0
        ]);
    }

    /**
     * Obtenir les reviews approuvés d'un produit
     */
    public function getApprovedReviews(int $productId, int $perPage = 10)
    {
        return Review::where('product_id', $productId)
            ->where('is_approved', true)
            ->with('user')
            ->orderBy('is_verified', 'desc') // Reviews vérifiés en premier
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Obtenir les statistiques des reviews
     */
    public function getReviewStats(int $productId): array
    {
        $reviews = Review::where('product_id', $productId)
            ->where('is_approved', true)
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        $stats = [];
        for ($i = 5; $i >= 1; $i--) {
            $stats[$i] = $reviews[$i] ?? 0;
        }

        return [
            'ratings' => $stats,
            'total' => array_sum($stats),
            'average' => Review::where('product_id', $productId)
                ->where('is_approved', true)
                ->avg('rating')
        ];
    }
}
