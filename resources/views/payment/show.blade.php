<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-2xl font-bold mb-4">{{ __('Order Summary') }}</h2>

                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('Order Number') }}</span>
                        <span class="font-semibold">#{{ $order->order_number }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('Subtotal') }}</span>
                        <span>{{ number_format($order->subtotal, 2) }} MAD</span>
                    </div>

                    @if($order->discount_amount > 0)
                    <div class="flex justify-between text-green-600">
                        <span>{{ __('Discount') }}</span>
                        <span>-{{ number_format($order->discount_amount, 2) }} MAD</span>
                    </div>
                    @endif

                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('Shipping') }}</span>
                        <span>{{ number_format($order->shipping_cost, 2) }} MAD</span>
                    </div>

                    <div class="border-t pt-2 flex justify-between font-bold text-lg">
                        <span>{{ __('Total Amount') }}</span>
                        <span class="text-blue-600">{{ number_format($order->total_amount, 2) }} MAD</span>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold mb-6">{{ __('Select Payment Method') }}</h2>

                <form action="{{ route('payment.process', $order) }}" method="POST" id="payment-form">
                    @csrf

                    <div class="space-y-4">
                        @foreach($paymentMethods as $method)
                        <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-blue-500 transition {{ $loop->first ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            <input type="radio"
                                   name="payment_method_id"
                                   value="{{ $method->id }}"
                                   class="mr-4"
                                   {{ $loop->first ? 'checked' : '' }}
                                   required>

                            <div class="flex-1">
                                <div class="flex items-center">
                                    @if($method->icon)
                                    <img src="{{ asset($method->icon) }}"
                                         alt="{{ $method->translated_name }}"
                                         class="w-12 h-12 object-contain mr-4">
                                    @endif

                                    <div>
                                        <h3 class="font-semibold text-lg">{{ $method->translated_name }}</h3>
                                        @if($method->description)
                                        <p class="text-sm text-gray-600">{{ $method->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <!-- Payment Method Specific Fields -->
                    <div id="stripe-card-element" class="hidden mt-4 p-4 border rounded"></div>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex gap-4">
                        <a href="{{ route('checkout.index') }}"
                           class="flex-1 py-3 px-6 bg-gray-200 text-gray-700 rounded-lg text-center font-semibold hover:bg-gray-300">
                            {{ __('Back to Checkout') }}
                        </a>

                        <button type="submit"
                                class="flex-1 py-3 px-6 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
                            {{ __('Complete Payment') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Add Stripe.js or other payment gateway scripts here
    </script>
    @endpush
</x-app-layout>
