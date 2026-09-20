<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    protected $fillable = [
        'name',
        'email',
        'password',
        'num',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    public static function admins()
    {
        return self::role('admin')->get();
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isUser(): bool
    {
        return $this->hasRole('user');
    }

    public function role2()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }


    public function isClient(): bool
    {
        return $this->hasRole('client');
    }

    // Attribuer une permission
    public function givePermission(string $permission)
    {
        $this->givePermissionTo($permission);
    }

    // Retirer une permission
    public function removePermission(string $permission)
    {
        $this->revokePermissionTo($permission);
    }

    // Synchroniser plusieurs permissions
    public function syncPermissions(array $permissions)
    {
        $this->syncPermissions($permissions);
    }

    // Vérifier une permission
    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) return true;
        return $this->can($permission);
    }

    // Vérifier plusieurs permissions
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->isAdmin()) return true;
        return $this->hasAnyPermission($permissions);
    }
}
