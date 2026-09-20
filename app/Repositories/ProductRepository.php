<?php
namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function all()
    {
        return Product::with(['brand', 'category', 'primaryImage'])
            ->where('is_active', true)
            ->get();
    }

    public function find($id)
    {
        return Product::with(['brand', 'category', 'images', 'tryonFile', 'reviews.user'])
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update($id, array $data)
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product;
    }

    public function delete($id)
    {
        return Product::findOrFail($id)->delete();
    }

    public function filter(array $filters)
    {
        $query = Product::with(['brand', 'category', 'primaryImage'])
            ->where('is_active', true);

        // Brand filter
        if (!empty($filters['brand'])) {
            $query->whereIn('brand_id', (array) $filters['brand']);
        }

        // Category filter
        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        // Gender filter
        if (!empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        // Shape filter
        if (!empty($filters['shape'])) {
            $query->where('frame_shape', $filters['shape']);
        }

        // Price range filter
        if (!empty($filters['min_price'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('discount_price', '>=', $filters['min_price'])
                  ->orWhere(function ($q2) use ($filters) {
                      $q2->whereNull('discount_price')
                         ->where('price', '>=', $filters['min_price']);
                  });
            });
        }

        if (!empty($filters['max_price'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('discount_price', '<=', $filters['max_price'])
                  ->orWhere(function ($q2) use ($filters) {
                      $q2->whereNull('discount_price')
                         ->where('price', '<=', $filters['max_price']);
                  });
            });
        }

        // Search query
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('sku', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Sorting
        $sortBy = $filters['sort'] ?? 'newest';

        switch ($sortBy) {
            case 'price_low':
                $query->orderByRaw('COALESCE(discount_price, price) ASC');
                break;
            case 'price_high':
                $query->orderByRaw('COALESCE(discount_price, price) DESC');
                break;
            case 'popularity':
                $query->orderBy('total_reviews', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($filters['per_page'] ?? 12);
    }

    public function getFeatured($limit = 8)
    {
        return Product::with(['brand', 'category', 'primaryImage'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public function getLatest($limit = 12)
    {
        return Product::with(['brand', 'category', 'primaryImage'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function search($query)
    {
        return Product::with(['brand', 'category', 'primaryImage'])
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('description', 'like', '%' . $query . '%')
                  ->orWhere('sku', 'like', '%' . $query . '%')
                  ->orWhereHas('brand', function ($q2) use ($query) {
                      $q2->where('name', 'like', '%' . $query . '%');
                  });
            })
            ->paginate(10);
    }

    public function getBySku($sku)
    {
        return Product::where('sku', $sku)->firstOrFail();
    }

    public function getRelated($productId, $limit = 4)
    {
        $product = Product::findOrFail($productId);

        return Product::with(['brand', 'category', 'primaryImage'])
            ->where('is_active', true)
            ->where('id', '!=', $productId)
            ->where(function ($query) use ($product) {
                $query->where('category_id', $product->category_id)
                      ->orWhere('brand_id', $product->brand_id);
            })
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}

?>
