<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Accessory extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'description',
        'type',
        'price',
        'stock',
        'is_active'
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'is_active' => 'boolean',
    ];

    /** 🔥 POLYMORPHIC IMAGES */
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
        return $this->morphMany(Review::class, 'reviewable');
    }

    
}
