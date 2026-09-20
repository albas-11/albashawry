<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pixel extends Model
{
    protected $fillable = [
        'provider',
        'pixel_id',
        'name',
        'is_active',
        'events'
    ];

    protected $casts = [
        'events' => 'array',
        'is_active' => 'boolean',
    ];
}
