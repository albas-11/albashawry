@extends('layouts.app')

@section('title', __('Order Confirmation'))

@section('content')
    <div class="bg-gray-50 min-h-screen py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-green-100">
                    <i class="fas fa-check text-2xl text-green-600"></i>
                </div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    @lang('message.Order Confirmed')!
                </h1>
                <p class="mt-2 text-gray-600">
                    @lang('message.Thank you for your order'). @lang('message.We will send you a confirmation email shortly').
                </p>
            </div>

            {{-- Card --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                {{-- Top bar --}}
                <div class="px-6 py-5 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <p class="text-sm text-gray-500">@lang('message.Order Number')</p>
                            <p class="text-xl font-semibold text-gray-900">#{{ $order->order_number }}</p>
                        </div>
                        <div class="sm:text-right">
                            <p class="text-sm text-gray-500">@lang('message.Order Date')</p>
                            <p class="font-medium text-gray-900">{{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">

                    {{-- Status --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="border border-gray-200 rounded-xl p-4">
                            <p class="text-xs text-gray-500 uppercase tracking-wider">@lang('message.Payment')</p>
                            <p class="mt-2 font-semibold text-gray-900">
                                @if ($order->payment_status === 'paid')
                                    <span class="inline-flex items-center gap-2">
                                        <span class="h-2 w-2 rounded-full bg-green-500"></span>
                                        @lang('message.Paid')
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2">
                                        <span class="h-2 w-2 rounded-full bg-yellow-500"></span>
                                        @lang('message.Pending')
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div class="border border-gray-200 rounded-xl p-4">
                            <p class="text-xs text-gray-500 uppercase tracking-wider">@lang('message.Order Status')</p>
                            <p class="mt-2 font-semibold text-gray-900 capitalize">{{ ucfirst($order->status) }}</p>
                        </div>

                        <div class="border border-gray-200 rounded-xl p-4">
                            <p class="text-xs text-gray-500 uppercase tracking-wider">@lang('message.Shipping')</p>
                            <p class="mt-2 font-semibold text-gray-900 capitalize">{{ ucfirst($order->status) }}</p>
                        </div>
                    </div>

                    {{-- Payment Info --}}
                    @if ($order->payment_method === 'online' && $order->telrTransaction)
                        <div class="border border-gray-200 rounded-xl p-5 mb-6 bg-gray-50">
                            <h3 class="font-semibold text-gray-900 mb-3">@lang('message.Payment Information')</h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                <div class="flex justify-between gap-3">
                                    <span class="text-gray-600">@lang('message.Method')</span>
                                    <span class="font-medium text-gray-900">
                                        {{ ucfirst($order->telrTransaction->payment_method ?? 'Card') }}
                                    </span>
                                </div>

                                <div class="flex justify-between gap-3">
                                    <span class="text-gray-600">@lang('message.Status')</span>
                                    <span class="font-medium text-gray-900">
                                        {{ ucfirst($order->telrTransaction->status) }}
                                    </span>
                                </div>

                                @if ($order->telrTransaction->card_last4)
                                    <div class="flex justify-between gap-3">
                                        <span class="text-gray-600">@lang('message.Card')</span>
                                        <span class="font-medium text-gray-900">**** **** ****
                                            {{ $order->telrTransaction->card_last4 }}</span>
                                    </div>
                                @endif

                                @if ($order->telrTransaction->transaction_ref)
                                    <div class="flex justify-between gap-3">
                                        <span class="text-gray-600">@lang('message.Reference')</span>
                                        <span
                                            class="font-medium text-gray-900">{{ $order->telrTransaction->transaction_ref }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Shipping Address --}}
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-900 mb-3">@lang('message.Shipping Address')</h3>
                        <div class="border border-gray-200 rounded-xl p-5">
                            <p class="font-semibold text-gray-900">{{ $order->customer_name }}</p>
                            <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                                {{ $order->shipping_address }}<br>
                                {{ $order->shipping_city }}, {{ $order->shipping_state }}<br>
                                {{ $order->shipping_postal_code }}<br>
                                {{ $order->shipping_country }}
                            </p>

                            <div class="mt-4 pt-4 border-t border-gray-200 text-sm text-gray-600 space-y-1">
                                <p><i class="fas fa-envelope mr-2"></i>{{ $order->shipping_email }}</p>
                                <p><i class="fas fa-phone mr-2"></i>{{ $order->shipping_phone }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Order Items --}}
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-900 mb-3">@lang('message.Order Items')</h3>

                        <div class="divide-y divide-gray-200 border border-gray-200 rounded-xl overflow-hidden">
                            @foreach ($order->items as $item)
                                <div class="p-4 flex items-center gap-4">
                                    @if ($item->product && $item->product->image)
                                        <div class="w-14 h-14 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                            <img src="{{ asset($item->product->image) }}"
                                                class="w-full h-full object-cover" alt="{{ $item->product_name }}">
                                        </div>
                                    @endif

                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ $item->product_name }}</p>
                                        <p class="text-sm text-gray-600 mt-1">
                                            @lang('message.Quantity'): {{ $item->quantity }} ×
                                            {{ number_format($item->price, 2) }} SAR
                                        </p>
                                    </div>

                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900">
                                            {{ number_format(($item['final_price'] ?? $item['price']) * $item['quantity'], 2) }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Summary --}}
                    <div class="pt-6 border-t border-gray-200">
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">@lang('message.Subtotal')</span>
                                <span class="font-medium text-gray-900">{{ number_format($order->subtotal, 2) }} SAR</span>
                            </div>

                            @if ($order->discount > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">@lang('message.Discount')</span>
                                    <span class="font-medium text-green-600">-{{ number_format($order->discount, 2) }}
                                        SAR</span>
                                </div>
                            @endif

                            <div class="flex justify-between">
                                <span class="text-gray-600">@lang('message.Shipping')</span>
                                <span class="font-medium text-gray-900">
                                    @if ($order->shipping_cost > 0)
                                        {{ number_format($order->shipping_cost, 2) }} SAR
                                    @else
                                        <span class="text-green-600">@lang('message.Free')</span>
                                    @endif
                                </span>
                            </div>

                            <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                                <span class="text-base font-semibold text-gray-900">@lang('message.Total')</span>
                                <span class="text-xl font-bold text-gray-900">
                                    {{ number_format($order->total, 2) }} <span class="text-sm font-medium">SAR</span>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Buttons --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-6">
                <a href="{{ route('orders.show', $order->id) }}"
                    class="flex items-center justify-center px-6 py-3 bg-gray-900 text-white rounded-xl font-semibold hover:bg-black transition">
                    <i class="fas fa-receipt mr-2"></i>
                    @lang('message.View Order Details')
                </a>

                <a href="{{ route('home') }}"
                    class="flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-800 rounded-xl font-semibold hover:border-gray-900 transition">
                    <i class="fas fa-home mr-2"></i>
                    @lang('message.Continue Shopping')
                </a>
            </div>

        </div>
    </div>
@endsection
