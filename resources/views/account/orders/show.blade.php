@extends('layouts.app')

@section('title', __('message.order') . ' #' . $order->order_number)

@section('content')
<div class="bg-[#F8F9FB] min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">

        <div class="flex items-center justify-between mb-8">
            <a href="{{ route('orders.index') }}"
               class="group flex items-center text-sm font-medium text-gray-500 hover:text-[#2E3192] transition-colors">
                <svg class="w-5 h-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                @lang('message.back_to_orders')
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <h1 class="text-2xl font-bold text-gray-900">
                                @lang('message.order') #{{ $order->order_number }}
                            </h1>

                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                {{ $order->status === 'processing' ? 'bg-blue-50 text-blue-600' : '' }}
                                {{ $order->status === 'completed' ? 'bg-green-50 text-green-600' : '' }}
                                {{ $order->status === 'cancelled' ? 'bg-red-50 text-red-600' : '' }}
                                {{ $order->status === 'pending' ? 'bg-orange-50 text-orange-600' : '' }}
                            ">
                                @lang('message.status.' . $order->status)
                            </span>
                        </div>

                        <p class="text-gray-500 text-sm italic">
                            @lang('message.ordered_on', [
                                'date' => $order->created_at->format('d F Y'),
                                'time' => $order->created_at->format('H:i')
                            ])
                        </p>
                    </div>

                    <div class="text-left md:text-right">
                        <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">
                            @lang('message.total_amount')
                        </p>
                        <p class="text-3xl font-black text-[#2E3192] leading-none">
                            {{ number_format($order->total, 2) }} <span class="text-sm">SAR</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">
                            @lang('message.payment_info')
                        </h3>

                        <div class="bg-gray-50 rounded-2xl p-5 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-500 text-sm">@lang('message.method')</span>
                                <span class="text-gray-900 font-medium text-sm">
                                    {{ ucfirst($order->payment_method) }}
                                </span>
                            </div>

                            <div class="flex justify-between pt-3 border-t border-gray-200/50">
                                <span class="text-gray-500 text-sm">@lang('message.status_label')</span>
                                <span class="text-green-600 font-bold text-sm">
                                    @lang('message.validated')
                                </span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">
                            @lang('message.shipping_address')
                        </h3>

                        <p class="text-gray-600 text-sm leading-relaxed italic">
                            @lang('message.shipping_info')
                        </p>
                    </div>
                </div>

                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">
                    @lang('message.items_details')
                </h3>

                <div class="border border-gray-100 rounded-2xl overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase">
                                    @lang('message.product')
                                </th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase text-center">
                                    @lang('message.quantity')
                                </th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase text-right">
                                    @lang('message.price')
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-50">
                            @foreach($order->items as $item)
                            <tr>
                                <td class="px-6 py-5 text-sm font-semibold text-gray-800">
                                    {{ $item->product_name }}
                                </td>
                                <td class="px-6 py-5 text-sm text-gray-600 text-center">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-5 text-sm font-bold text-gray-900 text-right">
                                    {{ number_format($item->price, 2) }} SAR
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-8 flex justify-end">
                    <div class="w-full md:w-64 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">@lang('message.subtotal')</span>
                            <span class="text-gray-900 font-medium">
                                {{ number_format($order->total, 2) }} SAR
                            </span>
                        </div>

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">@lang('message.shipping')</span>
                            <span class="text-green-600 font-medium">
                                @lang('message.free')
                            </span>
                        </div>

                        <div class="flex justify-between text-lg font-bold border-t border-gray-100 pt-4 text-gray-900">
                            <span>@lang('message.total')</span>
                            <span>{{ number_format($order->total, 2) }} SAR</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div>
</div>
@endsection
