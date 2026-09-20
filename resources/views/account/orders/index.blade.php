@extends('layouts.app')

@section('title', __('message.my_orders'))

@section('content')
    <div class="bg-[#F8F9FB] min-h-screen py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">

            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">@lang('message.my_orders')</h1>
                    <p class="text-gray-500 mt-1">@lang('message.orders_subtitle')</p>
                </div>

                <div class="bg-white px-4 py-2 rounded-lg border border-gray-100 shadow-sm text-sm">
                    <span class="text-gray-500">@lang('message.total') :</span>
                    <span class="font-bold text-[#2E3192]">
                        {{ $orders->count() }} @lang('message.order_s', ['count' => $orders->count()])
                    </span>
                </div>
            </div>

            @if ($orders->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 border-b border-gray-100">
                                    <th class="px-6 py-4 text-xs uppercase tracking-wider font-bold text-gray-400">
                                        @lang('message.order_number')
                                    </th>
                                    <th class="px-6 py-4 text-xs uppercase tracking-wider font-bold text-gray-400">
                                        @lang('message.date')
                                    </th>
                                    <th class="px-6 py-4 text-xs uppercase tracking-wider font-bold text-gray-400">
                                        @lang('message.total')
                                    </th>
                                    <th class="px-6 py-4 text-xs uppercase tracking-wider font-bold text-gray-400">
                                        @lang('message.status_label')
                                    </th>
                                    <th
                                        class="px-6 py-4 text-xs uppercase tracking-wider font-bold text-gray-400 text-right">
                                        @lang('message.action')
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-50">
                                @foreach ($orders as $order)
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-orange-50 text-orange-600',
                                            'processing' => 'bg-blue-50 text-blue-600',
                                            'completed' => 'bg-green-50 text-green-600',
                                            'cancelled' => 'bg-red-50 text-red-600',
                                        ];

                                        $class = $statusClasses[$order->status] ?? 'bg-gray-50 text-gray-600';
                                    @endphp

                                    <tr class="hover:bg-gray-50/80 transition-colors group">
                                        <td class="px-6 py-5">
                                            <span
                                                class="font-bold text-gray-900 group-hover:text-[#2E3192] transition-colors">
                                                #{{ $order->order_number }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-5 text-gray-600">
                                            {{ $order->created_at->format('d M Y') }}
                                        </td>

                                        <td class="px-6 py-5">
                                            <span class="font-semibold text-gray-900">
                                                {{ number_format($order->total, 2) }} <span class="text-xs">SAR</span>
                                            </span>
                                        </td>

                                        <td class="px-6 py-5">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $class }}">
                                                <span class="w-1.5 h-1.5 rounded-full bg-current mr-2"></span>
                                                @lang('message.status.' . $order->status)
                                            </span>
                                        </td>

                                        <td class="px-6 py-5 text-right">
                                            <a href="{{ route('account.orders.show', $order->id) }}"
                                                class="inline-flex items-center text-sm font-bold text-[#2E3192] hover:text-[#1e2063] transition-colors">
                                                @lang('message.details')
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>

                <div class="mt-8">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="bg-white rounded-2xl border border-dashed border-gray-200 py-16 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-50 rounded-full mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 118 0m-4 5v2m-8 0V7a4 4 0 100 8v2m-4 0a4 4 0 100 8h12a4 4 0 100-8H4z">
                            </path>
                        </svg>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900">@lang('message.no_orders')</h3>
                    <p class="text-gray-500 mb-6">@lang('message.no_orders_desc')</p>

                    <a href="{{ route('home') }}"
                        class="inline-flex items-center px-6 py-3 bg-[#2E3192] text-white rounded-xl font-bold hover:bg-[#1e2063] transition-all shadow-md">
                        @lang('message.start_shopping')
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
