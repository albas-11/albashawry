<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'item_id', 'item_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation polymorphique pour item
    public function item()
    {
        return $this->morphTo(__FUNCTION__, 'item_type', 'item_id');
    }
}
