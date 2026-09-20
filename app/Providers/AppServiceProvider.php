<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Setting;
use App\Models\Order;
use App\Models\Pixel;
use App\Observers\OrderObserver;
use App\Providers\config;
use Illuminate\Database\Eloquent\Relations\Relation;


class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Order::observe(OrderObserver::class);

        try {
            // Share settings with all views
            $settings = Setting::getInstance();
            View::share('globalSettings', $settings);

            // Share active pixels with views
            $pixels = Pixel::query()
                ->where('is_active', 1)
                ->get(['id', 'provider', 'pixel_id', 'events']);

            View::share('activePixels', $pixels);

            // Update app config
            config([
                'app.name' => $settings->site_name ?: config('app.name'),
                'app.locale' => $settings->locale ?: config('app.locale'),

                'mail.mailers.smtp.host' => $settings->smtp_host ?: env('MAIL_HOST', config('mail.mailers.smtp.host')),
                'mail.mailers.smtp.port' => $settings->smtp_port ?: (int) env('MAIL_PORT', config('mail.mailers.smtp.port')),
                'mail.mailers.smtp.encryption' => $settings->smtp_encryption ?: env('MAIL_ENCRYPTION', config('mail.mailers.smtp.encryption')),
                'mail.mailers.smtp.username' => $settings->smtp_username ?: env('MAIL_USERNAME', config('mail.mailers.smtp.username')),
                'mail.mailers.smtp.password' => $settings->smtp_password ?: env('MAIL_PASSWORD', config('mail.mailers.smtp.password')),

                'mail.from.address' => env('MAIL_FROM_ADDRESS') ?: ($settings->admin_email ?: config('mail.from.address')),
                'mail.from.name' => env('MAIL_FROM_NAME', config('mail.from.name')),
            ]);
        } catch (\Exception $e) {
            // Handle case where settings table doesn't exist yet
            logger()->warning('Settings not loaded: ' . $e->getMessage());
        }


        Relation::morphMap([
            'product' => 'App\Models\Product',
            'contact_lens' => 'App\Models\ContactLens',
            'accessory' => 'App\Models\Accessory',
        ]);
    }
}
