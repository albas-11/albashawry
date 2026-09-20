<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TryonFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'file_path',
        'preview_image',
        'offset_x',
        'offset_y',
        'scale',
        'rotation',
        'calibration_data',
        'is_active',
    ];

    protected $casts = [
        'offset_x' => 'decimal:2',
        'offset_y' => 'decimal:2',
        'scale' => 'decimal:2',
        'rotation' => 'decimal:2',
        'calibration_data' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get products using this try-on file
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'tryon_file_id');
    }

    /**
     * Scope: Active files only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get full file URL
     */
    // ✅ URL du fichier try-on
    public function getFileUrl()
    {
        return asset('images/tryon-files/' . $this->file_path);
    }

    // ✅ URL du preview
    public function getPreviewUrl()
    {
        if (!$this->preview_image) {
            return asset('images/placeholder.png');
        }

        return asset('images/tryon-previews/' . $this->preview_image);
    }


    /**
     * Get calibration configuration
     */
    public function getCalibrationConfig()
    {
        return [
            'offset_x' => (float) $this->offset_x,
            'offset_y' => (float) $this->offset_y,
            'scale' => (float) $this->scale,
            'rotation' => (float) $this->rotation,
            'additional_data' => $this->calibration_data ?? [],
        ];
    }

    /**
     * Check if file is being used
     */
    public function isInUse()
    {
        return $this->products()->count() > 0;
    }

    /**
     * Get usage count
     */
    public function getUsageCount()
    {
        return $this->products()->count();
    }
}
