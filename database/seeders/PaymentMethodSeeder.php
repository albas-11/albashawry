<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'cash_on_delivery',
                'display_name' => [
                    'en' => 'Cash on Delivery',
                    'ar' => 'الدفع عند الاستلام',
                    'fr' => 'Paiement à la livraison'
                ],
                'description' => 'Pay with cash when you receive your order',
                'is_active' => true,
                'icon' => '/images/payment/cod.svg',
                'sort_order' => 1,
                'min_amount' => null,
                'max_amount' => 5000.00,
                'allowed_currencies' => ['MAD']
            ],
            [
                'name' => 'stripe',
                'display_name' => [
                    'en' => 'Credit/Debit Card',
                    'ar' => 'بطاقة ائتمان/خصم',
                    'fr' => 'Carte de crédit/débit'
                ],
                'description' => 'Pay securely with Visa, Mastercard, or Amex',
                'is_active' => true,
                'configuration' => [
                    'publishable_key' => env('STRIPE_PUBLIC_KEY'),
                    'secret_key' => env('STRIPE_SECRET_KEY'),
                    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET')
                ],
                'icon' => '/images/payment/cards.svg',
                'sort_order' => 2,
                'allowed_currencies' => ['MAD', 'USD', 'EUR']
            ],
            [
                'name' => 'paypal',
                'display_name' => [
                    'en' => 'PayPal',
                    'ar' => 'باي بال',
                    'fr' => 'PayPal'
                ],
                'description' => 'Pay with your PayPal account',
                'is_active' => false, // Disable by default
                'configuration' => [
                    'client_id' => env('PAYPAL_CLIENT_ID'),
                    'client_secret' => env('PAYPAL_CLIENT_SECRET'),
                    'mode' => env('PAYPAL_MODE', 'sandbox')
                ],
                'icon' => '/images/payment/paypal.svg',
                'sort_order' => 3,
                'allowed_currencies' => ['USD', 'EUR']
            ],
            [
                'name' => 'bank_transfer',
                'display_name' => [
                    'en' => 'Bank Transfer',
                    'ar' => 'تحويل بنكي',
                    'fr' => 'Virement bancaire'
                ],
                'description' => 'Transfer money directly to our bank account',
                'is_active' => true,
                'configuration' => [
                    'bank_name' => 'Banque Populaire',
                    'account_name' => 'Eyewear Shop SARL',
                    'account_number' => '1234567890',
                    'rib' => '123456789012345678901234',
                    'swift' => 'BCPOMAMC'
                ],
                'icon' => '/images/payment/bank.svg',
                'sort_order' => 4,
                'allowed_currencies' => ['MAD']
            ],
            [
                'name' => 'cmi',
                'display_name' => [
                    'en' => 'CMI Payment',
                    'ar' => 'الدفع CMI',
                    'fr' => 'Paiement CMI'
                ],
                'description' => 'Secure payment via CMI Morocco',
                'is_active' => false,
                'configuration' => [
                    'merchant_id' => env('CMI_MERCHANT_ID'),
                    'secret_key' => env('CMI_SECRET_KEY'),
                    'store_key' => env('CMI_STORE_KEY')
                ],
                'icon' => '/images/payment/cmi.svg',
                'sort_order' => 5,
                'allowed_currencies' => ['MAD']
            ]
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::updateOrCreate(
                ['name' => $method['name']],
                $method
            );
        }

        $this->command->info('Payment methods seeded successfully!');
    }
}
