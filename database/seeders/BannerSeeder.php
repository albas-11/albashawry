<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        Banner::create([
            'title' => [
                'en' => 'Summer Collection 2024',
                'ar' => 'مجموعة صيف 2024',
            ],
            'subtitle' => [
                'en' => 'Get up to 50% off on selected items',
                'ar' => 'خصم يصل إلى 50% على منتجات مختارة',
            ],
            'image' => 'banners/summer-2024.jpg',
            'link' => '/products',
            'button_text' => 'Shop Now',
            'position' => 'hero',
            'is_active' => true,
            'order' => 1,
        ]);
    }
}
