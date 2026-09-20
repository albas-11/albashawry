
@extends('layouts.app')

@section('title', __('My Account'))

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">{{ __('My Account') }}</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Orders Card -->
            <a href="{{ route('account.orders') }}"
               class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition">
                        <i class="fas fa-shopping-bag text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold text-gray-900">{{ auth()->user()->orders->count() }}</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1">{{ __('Orders') }}</h3>
                <p class="text-sm text-gray-600">{{ __('View your order history') }}</p>
            </a>

            <!-- Wishlist Card -->
            <a href="{{ route('wishlist.index') }}"
               class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center group-hover:bg-red-200 transition">
                        <i class="fas fa-heart text-red-600 text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold text-gray-900">{{ auth()->user()->wishlists->count() }}</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1">{{ __('Wishlist') }}</h3>
                <p class="text-sm text-gray-600">{{ __('Your saved products') }}</p>
            </a>

            <!-- Profile Card -->
            <a href="{{ route('account.profile') }}"
               class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition">
                        <i class="fas fa-user text-green-600 text-xl"></i>
                    </div>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1">{{ __('Profile') }}</h3>
                <p class="text-sm text-gray-600">{{ __('Edit your information') }}</p>
            </a>

            <!-- Reviews Card -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-star text-yellow-600 text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold text-gray-900">{{ auth()->user()->reviews->count() }}</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1">{{ __('Reviews') }}</h3>
                <p class="text-sm text-gray-600">{{ __('Your product reviews') }}</p>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Recent Orders') }}</h2>

            @if(auth()->user()->orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="text-left px-4 py-3 text-sm font-semibold text-gray-700">{{ __('Order Number') }}</th>
                            <th class="text-left px-4 py-3 text-sm font-semibold text-gray-700">{{ __('Date') }}</th>
                            <th class="text-left px-4 py-3 text-sm font-semibold text-gray-700">{{ __('Status') }}</th>
                            <th class="text-right px-4 py-3 text-sm font-semibold text-gray-700">{{ __('Total') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(auth()->user()->orders->take(5) as $order)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 font-semibold">{{ $order->order_number }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $order->status == 'delivered' ? 'bg-green-100 text-green-700' :
                                       ($order->status == 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-bold">${{ number_format($order->total, 2) }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('account.orders.show', $order->id) }}"
                                   class="text-blue-600 hover:text-blue-700 font-semibold text-sm">
                                    {{ __('View') }} →
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-center">
                <a href="{{ route('account.orders') }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                    {{ __('View All Orders') }} →
                </a>
            </div>
            @else
            <p class="text-gray-600 text-center py-8">{{ __('No orders yet') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
