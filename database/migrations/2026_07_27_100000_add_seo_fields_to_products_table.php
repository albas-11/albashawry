<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Slug SEO facultatif : tant qu'il est NULL, le produit garde son URL /products/{id}
            $table->string('slug')->nullable()->unique()->after('sku');

            $table->json('seo_title')->nullable()->after('total_reviews');
            $table->json('seo_description')->nullable()->after('seo_title');
            $table->string('canonical_url')->nullable()->after('seo_description');
            $table->boolean('seo_noindex')->default(false)->after('canonical_url');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn([
                'slug',
                'seo_title',
                'seo_description',
                'canonical_url',
                'seo_noindex',
            ]);
        });
    }
};
