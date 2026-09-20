<?php

namespace App\Models;

use App\Models\Concerns\HasSeoFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasTranslations, HasSeoFields;

    public $translatable = ['name', 'description', 'short_description', 'seo_title', 'seo_description'];

    protected $fillable = [
        'name',
        'sku',
        'slug',
        'description',
        'price',
        'discount_price',
        'stock',
        'brand_id',
        'category_id',
        'type',
        'gender',
        'lens_shape',
        'frame_color',
        'frame_type',
        'lens_size',
        'arm_length',
        'bridge_width',
        'tryon_available',
        'tryon_file_id',
        'is_featured',
        'is_active',
        'views',
        'rating',
        'total_reviews',
        'seo_title',
        'seo_description',
        'canonical_url',
        'seo_noindex',
    ];

    protected $casts = [
        'tryon_available' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'seo_noindex' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'rating' => 'decimal:2',
        'name' => 'array',
        'description' => 'array',
        'seo_title' => 'array',
        'seo_description' => 'array',
    ];


    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function primaryImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')
            ->where('is_primary', true);
    }
    
    public function getPrimaryImagePath()
    {
        if (!$this->relationLoaded('primaryImage')) {
            $this->load('primaryImage');
        }

        $primaryImage = $this->primaryImage;

        if ($primaryImage) {
            return $primaryImage->image;
        }

        // prendre la première image
        if (!$this->relationLoaded('images')) {
            $this->load('images');
        }
        $firstImage = $this->images->first();
        return $firstImage ? $firstImage->image : null;
    }


    public function getMainImageAttribute()
    {
        return $this->getPrimaryImagePath();
    }

    public function tryonFile()
    {
        return $this->belongsTo(TryonFile::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /* ================= ACCESSORS ================= */

    public function getDiscountPercentageAttribute()
    {
        if ($this->discount_price && $this->price > 0) {
            return round((($this->price - $this->discount_price) / $this->price) * 100);
        }
        return 0;
    }

    public function getFinalPriceAttribute()
    {
        return $this->discount_price ?? $this->price;
    }

    public function isInStock()
    {
        return $this->stock > 0;
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function updateRating()
    {
        $avg = $this->reviews()->where('is_approved', true)->avg('rating');
        $count = $this->reviews()->where('is_approved', true)->count();

        $this->update([
            'rating' => round($avg, 2),
            'total_reviews' => $count,
        ]);
    }

    /* ==================== SEO ==================== */

    /**
     * Segment utilisé dans l'URL publique.
     * Retombe sur l'id tant qu'aucun slug n'a été défini,
     * afin de ne pas casser les URLs des produits existants.
     */
    public function getSeoUrlAttribute()
    {
        return $this->slug ?: $this->id;
    }

    /** URL publique du produit */
    public function getPublicUrlAttribute(): string
    {
        return route('products.show', $this->seo_url);
    }

    /**
     * Variables disponibles dans les champs SEO : {Name}, {Category}, {Brand}, {SKU}
     */
    public function seoTokens(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();

        return [
            '{Name}' => (string) $this->getTranslation('name', $locale, true),
            '{Category}' => (string) ($this->category?->getTranslation('name', $locale, true) ?? ''),
            '{Brand}' => (string) ($this->brand?->getTranslation('name', $locale, true) ?? ''),
            '{SKU}' => (string) $this->sku,
        ];
    }

    // renderSeoTemplate(), metaTitle(), metaDescription() et canonicalUrl()
    // proviennent du trait HasSeoFields.
}
