<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_lenses', function (Blueprint $table) {
            $table->id();

            $table->json('name');
            $table->json('description')->nullable();

            $table->string('type'); // daily, monthly, yearly, cosmetic, medical
            $table->string('color')->nullable();

            $table->string('duration_of_use');
            $table->decimal('water_content', 5, 2)->nullable(); // %
            $table->decimal('diameter', 5, 2)->nullable(); // mm
            $table->decimal('convexity', 4, 2)->nullable(); // BC

            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('views')->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_lenses');
    }
};
