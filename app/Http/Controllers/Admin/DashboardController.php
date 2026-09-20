<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Review;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Initialiser les stats
        $stats = [
            'total_orders' => 0,
            'pending_orders' => 0,
            'total_revenue' => 0,
            'total_products' => 0,
            'out_of_stock' => 0,
            'total_users' => 0,
            'pending_reviews' => 0,
        ];
        $permissions = [
            'view_orders'     => $user->can('view_orders'),
            'manage_products' => $user->can('manage_products'),
            'manage_users'    => $user->can('manage_users'),
            'manage_reviews'  => $user->can('manage_reviews'),
            'is_admin'        => $user->hasRole('Admin'),
        ];


        // Stats des commandes (si permission)
        if ($user->hasPermissionTo('view_orders')) {
            $stats['total_orders'] = Order::count();
            $stats['pending_orders'] = Order::where('status', 'pending')->count();
            $stats['total_revenue'] = Order::where('payment_status', 'paid')->sum('total');
        }

        // Stats des produits (si permission)
        if ($user->hasPermissionTo('manage_products')) {
            $stats['total_products'] = Product::count();
            $stats['out_of_stock'] = Product::where('stock', 0)->count();
        }

        // Stats des utilisateurs (si permission admin)
        if ($user->hasRole('admin')) {
            $stats['total_users'] = User::count(); 
        }

        if ($user->hasPermissionTo('manage_reviews')) {
            $stats['pending_reviews'] = Review::where('is_approved', false)->count();
        }

        // Récupérer les commandes récentes (si permission)
        $recentOrders = collect();
        if ($user->hasPermissionTo('view_orders')) {
            $recentOrders = Order::with('user')
                ->latest()
                ->take(10)
                ->get();
        }

        // Récupérer les produits en rupture de stock (si permission)
        $lowStockProducts = collect();
        if ($user->hasPermissionTo('manage_products')) {
            $lowStockProducts = Product::where('stock', '<', 10)
                ->where('stock', '>', 0)
                ->take(10)
                ->get();
        }

        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'lowStockProducts',
            'permissions'
        ));
    }
}
