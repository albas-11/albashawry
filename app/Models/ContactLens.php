<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ContactLens extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'description',
        'type',
        'color',
        'duration_of_use',
        'water_content',
        'diameter',
        'convexity',
        'price',
        'discount_price',
        'stock',
        'is_featured',
        'is_active',
        'brand_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'water_content' => 'decimal:2',
        'diameter' => 'decimal:2',
        'convexity' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    protected $table = 'contact_lenses';

    // Relation avec Brand
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function primaryImage()
    {
        return $this->morphOne(\App\Models\Image::class, 'imageable')
            ->where('is_primary', 1)
            ->orderBy('order');
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

        if (!$this->relationLoaded('images')) {
            $this->load('images');
        }

        $firstImage = $this->images->first();
        return $firstImage ? $firstImage->image : null;
    }

    public function isInStock()
    {
        return $this->stock > 0;
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable')->where('is_approved', true);
    }
    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function totalReviews()
    {
        return $this->reviews()->count();
    }
}
