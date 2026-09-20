<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'guard_name',
        'description',
    ];

    /**
     * Les utilisateurs qui ont cette permission
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'model_has_permissions',
            'permission_id',
            'model_id'
        )->where('model_type', User::class);
    }

    /**
     * Les rôles qui ont cette permission
     */
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'role_has_permissions',
            'permission_id',
            'role_id'
        );
    }

    /**
     * Scope pour rechercher par slug
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    /**
     * Scope pour rechercher par guard
     */
    public function scopeByGuard($query, $guard = 'web')
    {
        return $query->where('guard_name', $guard);
    }
}
