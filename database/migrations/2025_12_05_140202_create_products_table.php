<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('sku')->unique();
            $table->json('description');
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);

            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');

            $table->string('type'); // sunglasses, glasses, clipon
            $table->string('gender')->nullable(); // men, women, girls, boys

            $table->string('lens_shape')->nullable();
            $table->string('frame_color')->nullable();
            $table->string('frame_type')->nullable();
            $table->string('lens_size')->nullable();
            $table->string('arm_length')->nullable();
            $table->string('bridge_width')->nullable();

            $table->boolean('tryon_available')->default(false);
            $table->foreignId('tryon_file_id')->nullable()->constrained()->onDelete('set null');

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('views')->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
